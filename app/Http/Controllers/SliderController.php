<?php

namespace App\Http\Controllers;

use App\Common\Constants\HttpStatus;
use App\Common\Traits\ApiResponseTrait;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;
use App\Models\Slider;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    use ApiResponseTrait;

    public function publicIndex(): JsonResponse
    {
        $data = Slider::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($slider) => $this->transformSlider($slider))
            ->toArray();

        return $this->successResponse($data, 'Lấy danh sách slider thành công', HttpStatus::OK);
    }

    public function index(): JsonResponse
    {
        $data = Slider::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($slider) => $this->transformSlider($slider))
            ->toArray();

        return $this->successResponse($data, 'Lấy chi tiết slider thành công', HttpStatus::OK);
    }

    public function store(StoreSliderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $relativeImagePath = $this->saveImageToStorage($request->file('image'));

        $slider = Slider::create([
            'title' => $data['title'] ?? null,
            'image_url' => $relativeImagePath,
            'link_url' => $data['link_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'isactive' => $data['isactive'] ?? 'Y',
            'remark' => $data['remark'] ?? null,
        ]);

        return $this->successResponse(
            $this->transformSlider($slider->fresh()),
            'Tạo slider thành công',
            HttpStatus::CREATED
        );
    }

    public function update(UpdateSliderRequest $request, int $id): JsonResponse
    {
        $slider = Slider::query()->findOrFail($id);
        $data = $request->validated();

        $imagePath = $slider->image_url;

        if ($request->hasFile('image')) {
            $this->deleteImageFromStorage($slider->image_url);
            $imagePath = $this->saveImageToStorage($request->file('image'));
        }

        $slider->update([
            'title' => $data['title'] ?? null,
            'image_url' => $imagePath,
            'link_url' => $data['link_url'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'isactive' => $data['isactive'] ?? 'Y',
            'remark' => $data['remark'] ?? null,
        ]);

        return $this->successResponse(
            $this->transformSlider($slider->fresh()),
            'Cập nhật slider thành công',
            HttpStatus::OK
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $slider = Slider::query()->findOrFail($id);

        $this->deleteImageFromStorage($slider->image_url);
        $slider->delete();

        return $this->successResponse([], 'Xóa slider thành công', HttpStatus::OK);
    }

    private function saveImageToStorage(?UploadedFile $file): string
    {
        if (!$file) {
            throw new \RuntimeException('Image file is required.');
        }

        $fileNameWithoutExtension = now()->format('YmdHis') . '_' . Str::random(12);
        $finalRelativePath = 'sliders/' . $fileNameWithoutExtension . '.webp';
        $finalAbsolutePath = storage_path('app/public/' . $finalRelativePath);

        $originalExtension = strtolower($file->getClientOriginalExtension());

        if ($originalExtension === 'webp') {
            Storage::disk('public')->putFileAs('sliders', $file, $fileNameWithoutExtension . '.webp');
        } else {
            if (!is_dir(dirname($finalAbsolutePath))) {
                mkdir(dirname($finalAbsolutePath), 0777, true);
            }

            Webp::make($file)->save($finalAbsolutePath);
        }

        return $finalRelativePath;
    }

    private function deleteImageFromStorage(?string $relativePath): void
    {
        if (empty($relativePath)) {
            return;
        }

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    private function transformSlider(Slider $slider): array
    {
        $data = $slider->toArray();

        $data['image_path'] = $slider->image_url;
        $data['image_url'] = $slider->image_url
            ? asset('storage/' . ltrim($slider->image_url, '/'))
            : null;

        return $data;
    }
}
