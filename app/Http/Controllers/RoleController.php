<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RoleStoreRequest;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\RoleControllerStoreRequest;
use App\Http\Requests\RoleControllerUpdateRequest;

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

    public function show(Request $request, Role $role): Response
    {
        return view('role.show', compact('role'));
    }

    public function edit(Request $request, Role $role): Response
    {
        return view('role.edit', compact('role'));
    }

    public function update(RoleControllerUpdateRequest $request, Role $role): Response
    {
        $role->update($request->validated());

        $request->session()->flash('role.id', $role->id);

        return redirect()->route('roles.index');
    }

    public function destroy(Request $request, Role $role): Response
    {
        $role->delete();

        return redirect()->route('roles.index');
    }
}
