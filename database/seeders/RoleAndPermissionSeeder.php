<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        # now need make clear cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $adminManagement = [
            'create-admin', 'delete-admin', 'update-admin', 'show-admins',
        ];

        $courseManagement = [
            'create-course', 'update-course', 'delete-course', 'restore-course',
            'force-delete-course', 'show-courses', 'show-course-students',
            'show-course-mentors', 'create-link', 'delete-link', 'update-link', 'create-task', 'update-task', 'delete-task', 'restore-task', 'force-delete-task',
            'get-task-pending', 'get-task-submissions', 'task-approve', 'task-reject',
        ];

        $studentManagement = [
            'show-students', 'enable-student', 'disable-student', 'delete-student',
            'restore-student', 'force-delete-student', 'show-trashed-students',
            'filter-students', 'update-student-data',
            'send-email-to-student', 'send-email-to-all-students',
        ];

        $mentorManagement = [
            'enable-mentor', 'disable-mentor', 'show-mentors', 'show-students-mentor',
            'delete-mentor', 'restore-mentor', 'force-delete-mentor', 'show-students-mentor',
            'send-email-to-mentor', 'send-email-to-all-mentors'
        ];

        $roleAndPermissionManagement = [
//            'create-role', 'delete-role', 'show-roles',
//            'create-permission', 'delete-permission', 'show-permissions',
            'assign-role', 'assign-permission', 'revoke-role', 'assign-permission', 'revoke-permission',
        ];

        # create permissions for admin
        $adminPermissions = array_merge(
            $adminManagement,
            $courseManagement,
            $studentManagement,
            $mentorManagement,
            $roleAndPermissionManagement
        );

        # create permissions for mentor
        $mentorPermissions = array_merge(array_diff($courseManagement, ['create-course', 'update-course', 'delete-course', 'restore-course', 'force-delete-course']));

        # create permissions for student
//        $studentPermissions = array_merge();

        # now create permissions
        foreach ($adminPermissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }
        foreach ($mentorPermissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'mentor']);
        }
//        foreach ($studentPermissions as $permission) {
//            Permission::query()->firstOrCreate(['name' => $permission, 'guard_name' => 'student']);
//        }

        # send the Role and Permission
        $administrator = Role::query()->firstOrCreate([
            'name' => 'administrator',
            'guard_name' => 'admin'
        ]);
        $administrator->syncPermissions(Permission::query()->where('guard_name', 'admin')->get());

        # admin
        $admin = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admin'
        ]);
        $admin->syncPermissions(
            Permission::query()->where('guard_name', 'admin')
                ->whereNotIn('name', $roleAndPermissionManagement)
                ->whereNotIn('name', $adminManagement)
                ->get()
        );

        // mentor
        $mentor = Role::query()->firstOrCreate([
            'name' => 'mentor',
            'guard_name' => 'mentor'
        ]);
        $mentor->syncPermissions(
            Permission::query()->where('guard_name', 'mentor')->get()
        );

        // student
//        $student = Role::query()->firstOrCreate([
//            'name' => 'student',
//            'guard_name' => 'student'
//        ]);
//        $student->syncPermissions(
//            Permission::query()->where('guard_name', 'student')->get()
//        );
    }
}
