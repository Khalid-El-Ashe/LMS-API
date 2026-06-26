<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Mentor;
use App\Models\NotificationRecipient;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    /**
     * notification control
     */
    public function storeEmails(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:notification_recipients,email'
        ]);

        NotificationRecipient::query()->create([
            'email' => $request->email
        ]);

        return response()->json(['message' => 'Email added']);
    }

    public function toggleEmails($id)
    {
        $recipient = NotificationRecipient::query()->findOrFail($id);

        $recipient->update([
            'is_active' => !$recipient->is_active
        ]);

        return response()->json(['message' => 'Updated']);
    }

    /**
     * Build A Authorization Roles
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
        ]);

        Role::query()->create($request->only('name', 'slug'));

        return response()->json(['message' => 'Role added']);
    }

    private function resolveModel(string $type, int $id)
    {
        return match ($type) {
            'student' => Student::query()->findOrFail($id),
            'mentor' => Mentor::query()->findOrFail($id),
            'admin' => Admin::query()->findOrFail($id),
        };
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'model_type' => 'required|in:student,mentor,admin',
            'model_id' => 'required|integer',
            'role_id' => 'required|exists:roles,id',
        ]);

        $model = $this->resolveModel($request->model_type, $request->model_id);
        $model->roles()->syncWithoutDetaching([$request->role_id]);

        return response()->json([
            'message' => 'Role assigned successfully'
        ]);
    }

    public function removeRole(Request $request)
    {
        $request->validate([
            'model_type' => 'required|in:student,mentor,admin',
            'model_id' => 'required|integer',
            'role_id' => 'required|exists:roles,id',
        ]);

        $model = $this->resolveModel($request->model_type, $request->model_id);

        $model->roles()->detach($request->role_id);

        return response()->json([
            'message' => 'Role removed successfully'
        ]);
    }

    public function getUserRoles(Student $student)
    {
        return $student->roles;
    }

}
