<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserStoreRequest;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $users = User::with(['role'])->get();

        return view('user.index', compact('users'));
    }

    public function create()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $roles = Role::pluck('name', 'id')->prepend('Pilih Salah Satu', '');

        return view('user.create', compact('roles'));
    }

    public function store(UserStoreRequest $request): Response
    {
        $requestData = $request->all();
        $requestData['password'] = bcrypt($requestData['password']);

        if ($request->hasFile('profile_photo_path')) {
            $requestData['profile_photo_path'] = $request->file('profile_photo_path')->store('users', 'public');
        }

        $user = User::create($requestData);
        $user->roles()->sync($request->input('role_id'));

        

        return redirect()->route('app.users.index');
    }

    public function show(Request $request, User $user): Response
    {
        return view('user.show', compact('user'));
    }

    public function edit(Request $request, User $user): Response
    {
        return view('user.edit', compact('user'));
    }

    public function update(UserControllerUpdateRequest $request, User $user): Response
    {
        $user->update($request->validated());

        $request->session()->flash('user.id', $user->id);

        return redirect()->route('users.index');
    }

    public function destroy(Request $request, User $user): Response
    {
        $user->delete();

        return redirect()->route('app.users.index');
    }
}
