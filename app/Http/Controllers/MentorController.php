<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\MentorRequest;
use App\Http\Requests\ProfileImageRequest;
use App\Http\Resources\Mentor\MentorDashboardResource;
use App\Models\Mentor;
use App\Repositories\Mentor\MentorRepository;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class MentorController extends Controller
{

    public function __construct(private readonly MentorRepository $repository)
    {
        // inject the repository
    }

    /**
     * Summary of register
     * Creating a new Mentor Account
     */
    public function register(MentorRequest $request)
    {
        try {
            $data = $this->repository->registerMentor($request->validated());
            return $this->success($data, 'Mentor registered successfully', 201);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }


    public function login(LoginRequest $request)
    {
        try {
            $mentor = $this->repository->loginMentor($request->validated());
            return $this->success(data: $mentor);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function logout()
    {
        try {
            $this->repository->logoutMentor();
            return $this->success(null, 'Mentor logged out successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function updateMentorInformation(Request $request)
    {
        try {
            $mentor = auth()->guard('mentor')->user();
            $information = $this->repository->updateInformation($mentor, $request->all());
            return $this->success($information);
        } catch (Throwable $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : 500;
            return $this->error($th->getMessage(), $status);
        }
    }

    public function getAllMentors()
    {
        try {
            $mentors = $this->repository->getAllMentors();
            return $this->success(data: $mentors);
        } catch (Throwable $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : 500;
            return $this->error($th->getMessage(), $status);
        }
    }

    public function mentorDashboard()
    {
        try {
            $mentor = auth()->guard('mentor')->user();
            $dashboardData = new MentorDashboardResource($this->repository->mentorDashboard($mentor));
            return $this->success(data: $dashboardData);
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * Summary of enableAccount
     * Can the Admin Enable Mentor Account
     */
    public function enableAccount(Mentor $mentor)
    {

        try {
            // Implement the logic to enable a mentor account
            // You can use the repository method to enable the mentor account
            $this->repository->enableMentor($mentor);
            return $this->success(null, 'Mentor account enabled successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    /**
     * Summary of disableAccount
     * Can the Admin Disable Mentor Account
     */
    public function disableAccount(Mentor $mentor)
    {
        try {
            // Implement the logic to disable a mentor account
            // You can use the repository method to disable the mentor account
            $this->repository->disableMentor($mentor);
            return $this->success(null, 'Mentor account disabled successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function uploadProfileImage(ProfileImageRequest $request)
    {
        try {
            $mentor = auth()->guard('mentor')->user();
            $imageUrl = $this->repository->uploadProfileImage($mentor, $request->file('profile_image'));
            return $this->success($imageUrl, 'Profile image uploaded successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function uploadMultipleFiles(FileRequest $request, Mentor $mentor)
    {
        try {
            $fileUrls = $this->repository->uploadMultipleFiles($mentor, $request->file('files'));
            return $this->success($fileUrls, 'Files uploaded successfully');
        } catch (Throwable $th) {
            return $this->error($th->getMessage());
        }
    }

    public function getAllMentorsCount(): JsonResponse
    {
        $count = Mentor::query()->count();
        return $this->success($count);
    }
}
