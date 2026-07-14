<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\StudentRequest;
use App\Http\Resources\Student\StudentDashboardResource;
use App\Http\Resources\Student\StudentPathResource;
use App\Models\Student;
use App\Repositories\Student\StudentRepository;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentController extends Controller
{

    /**
     * need to cut the Logic or the Backend Code
     * in here Just the Request
     */
    public function __construct(private readonly StudentRepository $studentRepository)
    {
    }

    public function getAllStudents()
    {
        try {
            $students = $this->studentRepository->getAllStudents();
            return $this->success(data: $students, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error(__('message.an_error_occurred_while_retrieving_students'), $status);
        }
    }

    public function getAllStudentsIsTrashed()
    {
        try {
            $students = $this->studentRepository->getAllStudentsIsTrashed();
            return $this->success(data: $students, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving trashed students', $status);
        }
    }


    /**
     * Summary of register
     * Creating a new Student Account
     * Validation Layer (StudentRequest)
     */
    public function register(StudentRequest $request)
    {
        try {
            $this->studentRepository->register($request->validated());
            return $this->success(null, __('message.student_registered_successfully'), ResponseAlias::HTTP_CREATED);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error($th->getMessage(), $status);
        }
    }


    /**
     * @param LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        try {
            $token = $this->studentRepository->login($request->validated());
            return $this->success(data: $token, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error($th->getMessage(), $status);
        }
    }

    /**
     */
    public function logout()
    {
        try {
            $result = $this->studentRepository->logout();
            if (!$result) {
                return $this->error('Token not found or already deleted', ResponseAlias::HTTP_NOT_FOUND);
            }
            return $this->success(data: null, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while logging out', $status);
        }
    }

    /**
     */
    public function getStudentProfile()
    {
        try {
            $info = $this->studentRepository->getStudentProfile();
            $info = new StudentDashboardResource($info);
            return $this->success(data: $info, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving student info\n' . $th->getMessage(), $status);
        }
    }

    public function getAllStudentsForMentor()
    {
        try {
            $mentor = auth()->guard('mentor')->user();
            $students = $this->studentRepository->getAllStudentsForMentor($mentor);
            return $this->success(data: $students, code: ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving students for the mentor\n' . $th->getMessage(), $status);
        }
    }

    public function updateStudentData(Request $request)
    {
        try {
            $student = auth()->guard('student')->user();
            $updatedStudent = $this->studentRepository->updateStudentData($student, $request->all());
            return $this->success($updatedStudent, __('message.student_info_updated_successfully'), ResponseAlias::HTTP_OK);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while updating student info\n' . $th->getMessage(), $status);
        }
    }

    public function deleteStudent(Student $student)
    {
        try {
            $this->studentRepository->deleteStudent($student);
            return $this->success('', "Success Deleted Account: $student->full_name / Student");
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while deleting the student\n' . $th->getMessage(), $status);
        }
    }

    public function restoreStudent(Student $student)
    {
        try {
            $this->studentRepository->restoreStudent($student);
            return $this->success('', "Success Restored Account: $student->name / Student");
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while restoring the student\n' . $th->getMessage(), $status);
        }
    }

    public function forceDeleteStudent(Student $student)
    {
        try {
            $this->studentRepository->forceDeleteStudent($student);
            return $this->success('', "Success Force Deleted Account: $student->name / Student");
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while force deleting the student\n' . $th->getMessage(), $status);
        }
    }

    public function searchStudents(Request $request)
    {
        try {
            $searchTerm = $request->query('q');
            $students = $this->studentRepository->searchStudents($searchTerm);
            return $this->success($students);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while searching for students\n' . $th->getMessage(), $status);
        }
    }

    public function filterStudents($filters)
    {
        try {
            $students = $this->studentRepository->filterStudents($filters);
            return $this->success($students);
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while filtering students\n' . $th->getMessage(), $status);
        }
    }

    /**
     * Summary of enableStudent
     * Can the Admin Enable Student Account
     * Validation Layer (StudentRequest)
     */
    public function enableStudent(Student $student)
    {
        // in here need to make update for the student account and make it active
        try {
            $this->studentRepository->enableStudent($student);
            return $this->success(message: "Success Enabled Account: $student->name / Student");
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while enabling the student account\n' . $th->getMessage(), $status);
        }
    }

    /**
     * Summary of disableStudent
     * Can the Admin Disable Student Account
     * Validation Layer (StudentRequest)
     */
    public function disableStudent(Student $student)
    {
        try {
            $this->studentRepository->disableStudent($student);
            return $this->success(message: "Success Disabled Account: $student->name / Student");
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while disabling the student account\n' . $th->getMessage(), $status);
        }
    }

    public function uploadProfileImage(ProfileImageRequest $request)
    {
        try {
            $student = auth()->guard('student')->user();
            $imageUrl = $this->studentRepository->uploadProfileImage($student, $request->file('profile_image'));
            return $this->success($imageUrl, 'Profile image uploaded successfully');
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while uploading the profile image\n' . $th->getMessage(), $status);
        }
    }

    public function uploadMultipleFiles(FileRequest $request, Student $student)
    {
        try {
            $fileUrls = $this->studentRepository->uploadMultipleFiles($student, $request->file('files'));
            return $this->success($fileUrls, 'Files uploaded successfully');
        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while uploading the files\n' . $th->getMessage(), $status);
        }
    }

    public function path()
    {
        try {
            $path = $this->studentRepository->getStudentPath();
            return $this->success(data: new StudentPathResource($path), code: ResponseAlias::HTTP_OK);

        } catch (Exception $th) {
            $status = method_exists($th, 'getStatusCode') ? $th->getStatusCode() : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
            return $this->error('An error occurred while retrieving student path\n' . $th->getMessage(), $status);
        }
    }
}
