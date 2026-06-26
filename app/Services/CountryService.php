<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class CountryService
{
    public static function getAll()
    {
        return Cache::remember('countries', 86400, function () {
            return json_decode(
                File::get(resource_path('data/countries.json')),
                true
            );
        });
    }

    public static function getCodeByIso(string $iso)
    {
        $countries = self::getAll();

        foreach ($countries as $country) {
            if (strtoupper($country['iso']) === strtoupper($iso)) {
                return [
                    'name' => $country['nationality'],
                    'countryCode' => $country['countryCode'],
                ];
            }
        }

        return null;
    }

}
