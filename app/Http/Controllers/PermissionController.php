<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
// use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\PermissionStoreRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\PermissionControllerStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;

class PermissionController extends Controller
{  
    public function index(Request $request): View
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $permissions = Permission::all();

        return view('permission.index', compact('permissions'));
    }

    public function create()
    {
        abort_if(Gate::denies('permission_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('permission.create');
    }

    public function store(PermissionStoreRequest $request)
    {
        $data = $request->all();
        $data['guard_name'] = 'web';
        $permission = Permission::create($data);

        return redirect()->route('app.permissions.index');
    }

    public function edit(Permission $permission)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('permission.edit', compact('permission'));
    }

    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        abort_if(Gate::denies('permission_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Update permission dengan data yang divalidasi
        $permission->update($request->validated());

        return redirect()->route('app.permissions.index')->with('success', 'Hak Akses berhasil diupdate');
    }

    public function destroy(Request $request, Permission $permission): Response
    {
        $permission->delete();

        return redirect()->route('app.permissions.index');
    }
}
