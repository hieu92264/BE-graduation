<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'dvhcvn.json';

        $this->command?->warn('base_path: ' . base_path());
        $this->command?->warn('looking for: ' . Storage::path($path));

        if (!Storage::exists($path)) {
            $this->command?->error("Không tìm thấy file: storage/app/{$path}");
            return;
        }

        $raw = Storage::get($path);
        $json = json_decode($raw, true);

        if (!is_array($json) || !isset($json['data']) || !is_array($json['data'])) {
            $this->command?->error("JSON không đúng format (cần có key 'data').");
            return;
        }

        $data = $json['data'];

        DB::transaction(function () use ($data) {

            // 1) CITIES
            $citiesPayload = [];
            foreach ($data as $i => $c) {
                $code = (string)($c['level1_id'] ?? '');
                $name = (string)($c['name'] ?? '');
                if ($code === '' || $name === '') continue;

                $citiesPayload[] = [
                    'code' => $code,
                    'name' => $name,
                    'isactive' => 'Y',
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            City::upsert(
                $citiesPayload,
                ['code'],
                ['name', 'isactive', 'sort_order', 'updated_at']
            );

            $cityIdByCode = City::query()->pluck('id', 'code');

            // 2) DISTRICTS
            $districtsPayload = [];
            foreach ($data as $c) {
                $cityCode = (string)($c['level1_id'] ?? '');
                $cityId = $cityIdByCode[$cityCode] ?? null;
                if (!$cityId) continue;

                $level2s = $c['level2s'] ?? [];
                foreach ($level2s as $j => $d) {
                    $dCode = (string)($d['level2_id'] ?? '');
                    $dName = (string)($d['name'] ?? '');
                    if ($dCode === '' || $dName === '') continue;

                    $districtsPayload[] = [
                        'code' => $dCode,
                        'name' => $dName,
                        'city_id' => $cityId,
                        'isactive' => 'Y',
                        'sort_order' => $j,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            District::upsert(
                $districtsPayload,
                ['code'],
                ['name', 'city_id', 'isactive', 'sort_order', 'updated_at']
            );

            $districtIdByCode = District::query()->pluck('id', 'code');

            // 3) WARDS (chunk để nhẹ)
            $buffer = [];
            $flush = function () use (&$buffer) {
                if (empty($buffer)) return;

                Ward::upsert(
                    $buffer,
                    ['code'],
                    ['name', 'district_id', 'isactive', 'sort_order', 'updated_at']
                );
                $buffer = [];
            };

            foreach ($data as $c) {
                $level2s = $c['level2s'] ?? [];
                foreach ($level2s as $d) {
                    $districtCode = (string)($d['level2_id'] ?? '');
                    $districtId = $districtIdByCode[$districtCode] ?? null;
                    if (!$districtId) continue;

                    $level3s = $d['level3s'] ?? [];
                    foreach ($level3s as $k => $w) {
                        $wCode = (string)($w['level3_id'] ?? '');
                        $wName = (string)($w['name'] ?? '');
                        if ($wCode === '' || $wName === '') continue;

                        $buffer[] = [
                            'code' => $wCode,
                            'name' => $wName,
                            'district_id' => $districtId,
                            'isactive' => 'Y',
                            'sort_order' => $k,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        if (count($buffer) >= 2000) {
                            $flush();
                        }
                    }
                }
            }

            $flush();
        });

        $this->command?->info("Seed location xong: cities/districts/wards");
    }
}
