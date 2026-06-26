<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use App\Services\CountryService;
use Exception;

class CountryCodeController extends Controller
{

    public function __construct(private readonly CountryService $countryService)
    {
    }

    public function index()
    {
        try {
            $countries = $this->countryService->getAll();
            return $this->success($countries, 'Countries retrieved successfully');
        } catch (Exception $e) {
            return $this->error('An error occurred while retrieving countries', 500);
        }
    }
}
