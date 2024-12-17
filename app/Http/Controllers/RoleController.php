<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\MassDestroyRoleRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\RoleControllerStoreRequest;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $roles = Role::with(['permissions'])->get();
        
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permissions = Permission::pluck('name', 'id');

        return view('role.create', compact('permissions'));
    }

    public function store(RoleStoreRequest $request)
    {
        $data = $request->all();
        $data['guard_name'] = 'web';
        $role = Role::create($data);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('app.roles.index');
    }


    public function edit(Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        $permissions = Permission::pluck('name', 'id');  // Ambil list permission

        return view('role.edit', compact('role', 'permissions'));
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        
        // Update role dengan data baru
        $data = $request->validated();
        $data['guard_name'] = 'web';  // Pastikan guard_name tetap 'web'
        $role->update($data);
        
        // Update permission yang terkait dengan role
        $role->permissions()->sync($request->input('permissions', []));
        
        return redirect()->route('app.roles.index')->with('success', 'Peran berhasil diupdate');
    }


    public function destroy(Request $request, Role $role): Response
    {
        $role->delete();

        return redirect()->route('app.roles.index');
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
