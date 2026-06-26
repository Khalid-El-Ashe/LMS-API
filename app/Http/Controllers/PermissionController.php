<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        try {
            $permissions = Permission::query()
                ->get()
                ->groupBy('guard_name');

            return $this->success($permissions, 'Permissions retrieved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # get the permissions for each account
    public function accountPermissions(string $type, int $id)
    {
        try {
            $account = $this->resolveAccount($type, $id);

            return $this->success([
                'role_permissions' => $account->getPermissionsViaRoles(),
                'direct_permissions' => $account->getDirectPermissions(),
                'all_permissions' => $account->getAllPermissions(),
            ], 'Permissions retrieved successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # send the permissions for each account
    public function assignPermission(string $type, int $id)
    {
        try {
            $account = $this->resolveAccount($type, $id);
            $permissions = request('permissions');

            $account->givePermissionTo($permissions);

            return $this->success(null, 'Permission assigned successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    # remove the permissions
    public function revokePermission(string $type, int $id)
    {
        try {
            $account = $this->resolveAccount($type, $id);
            $permissions = request('permissions');

            $account->revokePermissionTo($permissions);

            return $this->success(null, 'Permission revoked successfully');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

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
