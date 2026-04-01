<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Models\Room;
use App\Models\RoomPhoto;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandlordRoomPhotoController extends Controller
{
    use ApiResponseTrait;

    public function index(int $roomId): JsonResponse
    {
        $room = $this->findOwnedRoom($roomId);

        $photos = $room->photos()
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(RoomPhoto $photo) => $this->transformPhoto($photo))
            ->toArray();

        return $this->successResponse($photos, 'Lấy danh sách ảnh phòng thành công');
    }

    public function upload(Request $request, int $roomId): JsonResponse
    {
        $room = $this->findOwnedRoom($roomId);

        $validated = $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'is_cover' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $relativePath = $this->saveImageToStorage($request->file('image'));

            if (! Storage::disk('public')->exists($relativePath)) {
                throw new \RuntimeException('Không tìm thấy ảnh đã lưu trong bộ nhớ công khai.');
            }

            $hasCover = $room->photos()->where('is_cover', true)->exists();
            $isCover = (bool) ($validated['is_cover'] ?? false);

            if (! $hasCover) {
                $isCover = true;
            }

            if ($isCover) {
                $room->photos()->update(['is_cover' => false]);
            }

            $photo = RoomPhoto::create([
                'room_id' => $room->id,
                'photo_url' => $relativePath,
                'is_cover' => $isCover,
                'sort_order' => $validated['sort_order'] ?? (($room->photos()->max('sort_order') ?? -1) + 1),
            ]);

            DB::commit();

            return $this->successResponse(
                $this->transformPhoto($photo->fresh()),
                'Tải ảnh phòng lên thành công',
                HttpStatus::CREATED
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to upload room photo',
                HttpStatus::BAD_REQUEST,
                ['message' => $e->getMessage()]
            );
        }
    }

    public function update(Request $request, int $roomId, int $photoId): JsonResponse
    {
        $room = $this->findOwnedRoom($roomId);
        $photo = $room->photos()->findOrFail($photoId);

        $validated = $request->validate([
            'is_cover' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            if (array_key_exists('is_cover', $validated) && (bool) $validated['is_cover'] === true) {
                $room->photos()->update(['is_cover' => false]);
                $photo->is_cover = true;
            }

            if (array_key_exists('sort_order', $validated)) {
                $photo->sort_order = (int) $validated['sort_order'];
            }

            $photo->save();

            DB::commit();

            return $this->successResponse(
                $this->transformPhoto($photo->fresh()),
                'Cập nhật ảnh phòng thành công'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to update room photo',
                HttpStatus::BAD_REQUEST,
                ['message' => $e->getMessage()]
            );
        }
    }

    public function sort(Request $request, int $roomId): JsonResponse
    {
        $room = $this->findOwnedRoom($roomId);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $item) {
                $photo = $room->photos()->find($item['id']);
                if ($photo) {
                    $photo->update([
                        'sort_order' => $item['sort_order'],
                    ]);
                }
            }

            DB::commit();

            $photos = $room->photos()
                ->orderByDesc('is_cover')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn(RoomPhoto $photo) => $this->transformPhoto($photo))
                ->toArray();

            return $this->successResponse($photos, 'Sắp xếp ảnh phòng thành công');
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to sort room photos',
                HttpStatus::BAD_REQUEST,
                ['message' => $e->getMessage()]
            );
        }
    }

    public function destroy(int $roomId, int $photoId): JsonResponse
    {
        $room = $this->findOwnedRoom($roomId);
        $photo = $room->photos()->findOrFail($photoId);

        DB::beginTransaction();

        try {
            $wasCover = (bool) $photo->is_cover;

            $this->deleteImageFromStorage($photo->photo_url);
            $photo->delete();

            $this->reindexRoomPhotos($room);

            if ($wasCover) {
                $nextPhoto = $room->photos()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($nextPhoto) {
                    $nextPhoto->update(['is_cover' => true]);
                }
            }

            DB::commit();

            return $this->successResponse([], 'Xóa ảnh phòng thành công');
        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->failedResponse(
                'Failed to delete room photo',
                HttpStatus::BAD_REQUEST,
                ['message' => $e->getMessage()]
            );
        }
    }

    private function findOwnedRoom(int $id): Room
    {
        return Room::query()
            ->withoutGlobalScopes()
            ->where('owner_user_id', Auth::user()->id)
            ->findOrFail($id);
    }

    private function saveImageToStorage(?UploadedFile $file): string
    {
        if (! $file) {
            throw new \RuntimeException('Image file is required.');
        }

        $fileNameWithoutExtension = now()->format('YmdHis') . '_' . Str::random(12);
        $finalRelativePath = 'rooms/' . $fileNameWithoutExtension . '.webp';
        $finalAbsolutePath = storage_path('app/public/' . $finalRelativePath);

        $originalExtension = strtolower($file->getClientOriginalExtension());

        if ($originalExtension === 'webp') {
            Storage::disk('public')->putFileAs('rooms', $file, $fileNameWithoutExtension . '.webp');
        } else {
            if (! is_dir(dirname($finalAbsolutePath))) {
                mkdir(dirname($finalAbsolutePath), 0777, true);
            }

            Webp::make($file)->save($finalAbsolutePath);
        }

        return $finalRelativePath;
    }

    private function deleteImageFromStorage(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    private function reindexRoomPhotos(Room $room): void
    {
        $room->photos()
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function (RoomPhoto $photo, int $index) {
                if ((int) $photo->sort_order !== $index) {
                    $photo->update(['sort_order' => $index]);
                }
            });
    }

    private function transformPhoto(RoomPhoto $photo): array
    {
        $data = $photo->toArray();

        $rawPath = $photo->photo_url ? ltrim($photo->photo_url, '/') : null;

        $data['photo_path'] = $rawPath;
        $data['photo_version'] = optional($photo->updated_at)->timestamp;

        if (! $rawPath) {
            $data['photo_url'] = null;

            return $data;
        }

        if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
            $data['photo_url'] = $rawPath;

            return $data;
        }

        if (str_starts_with($rawPath, 'storage/')) {
            $data['photo_url'] = asset($rawPath);

            return $data;
        }

        $data['photo_url'] = asset('storage/' . $rawPath);

        return $data;
    }
}
