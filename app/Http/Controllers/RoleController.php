<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Traits\ApiResponseTrait;
use Exception;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::query()
                ->with('permissions')
                ->get();

            return $this->success($roles);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # send the Roles for each account
    public function assignRole(string $type, int $id)
    {
        try {
            $account = $this->resolveAccount($type, $id);
            $role = request('role');

            $account->assignRole($role);

            return $this->success(null, "Role assigned successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # revoke the Role for each account
    public function revokeRole(string $type, int $id)
    {
        try {
            $account = $this->resolveAccount($type, $id);
            $role = request('role');

            $account->removeRole($role);

            return $this->success(null, "Role revoked successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # mach the Model
    private function resolveAccount(string $type, int $id)
    {
        return match ($type) {
            'admin' => Admin::query()->findOrFail($id),
            'mentor' => Mentor::query()->findOrFail($id),
            'student' => Student::query()->findOrFail($id),
            default => abort(404, 'Account type not found')
        };
    }
}
