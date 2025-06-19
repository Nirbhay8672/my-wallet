<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Inertia\Response;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('permission/Index',[
            'page_name' => 'Role Permissions'
        ]);
    }

    public function rolePermission(): JsonResponse
    {
        try {
            $roles = Role::select([
                'id',
                'display_name',
                'guard_name',
            ])
            ->with('permissions')
            ->get();
                
            $permissions = Permission::select([
                'id',
                'display_name',
                'category',
                'guard_name',
            ])
            ->orderBy('category')
            ->get();
                
            $permission_collection = collect();
            
            foreach ($permissions as $permission) {
                $permission_collection->push([
                    'id' => $permission->id,
                    'display_name' => $permission->display_name,
                    'category' => $permission->category,
                    'roles' => $this->getRolesByPermission($permission, $roles),
                ]);
            }
                
            return $this->successResponse(message: "Role permissions details fetch.", data: [
                'roles' => $roles->toArray(),
                'grouped_permissions' => $permission_collection->groupBy('category'),   
            ]);
        } catch (Exception $exception) {
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function getRolesByPermission(Permission $permission, Collection $roles): array
    {   
        $roles_array = [];

        foreach ($roles as $role) {
            array_push($roles_array,[
                'id' => $role->id,
                'display_name' => $role->display_name,
                'has_permission' => $role->hasPermissionTo($permission->id)
            ]);
        }

        return $roles_array;
    }

    public function assignPermissionsByRoles(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            foreach ($request->permissions_by_roles as $role_id => $permission_ids) {
                $role = Role::find($role_id);
                $permissions = Permission::whereIn('id', $permission_ids)->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return $this->successResponse(message: "Role permissions update successfully.");
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
