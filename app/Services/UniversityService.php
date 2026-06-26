<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class UniversityService {
    public static function getAll(): array
    {
        return Cache::remember('universities', 86400, function () {
            return json_decode(
                File::get(resource_path('data/universities.json')),
                true
            );
        });
    }

    public static function exists(int $id)
    {
        return collect(self::getAll())->firstWhere('id', $id);
    }
}
