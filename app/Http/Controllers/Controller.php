<?php

namespace App\Http\Controllers;

use App\Services\MajorService;
use App\Services\UniversityService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    use ApiResponseTrait;

    public function __construct(private readonly UniversityService $universityService, private readonly MajorService $majorService)
    {

    }

    public function universityList()
    {
        try {
            $universities = $this->universityService->getAll();
            return $this->success($universities, 'Universities retrieved successfully');
        } catch (Exception $e) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving universities', $status);
        }
    }

    public function majorList()
    {
        try {
            $majors = $this->majorService->getAll();
            return $this->success($majors, 'Majors retrieved successfully');
        } catch (Exception $e) {
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving majors', $status);
        }
    }
}
