<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\MassDestroyUserRequest;
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

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }


    public function edit(Request $request, $id)
    {
        // Mengambil data user berdasarkan ID
        $user = User::findOrFail($id);

        // Mengambil semua role untuk digunakan dalam dropdown
        $roles = Role::pluck('name', 'id');

        return view('user.edit', compact('user', 'roles'));
    }

    public function update(UserUpdateRequest $request, User $user): Response
    {
        
        $user->update(Arr::except($request->validated(), ['password']));

        
        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // Update role user
        $user->role_id = $request->role_id;


        // Jika ada file foto baru yang diupload
        if ($request->hasFile('profile_photo_path')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo_path) {
                Storage::delete('public/' . $user->profile_photo_path);
            }

            // Simpan foto baru dan update path-nya di database
            $path = $request->file('profile_photo_path')->store('users', 'public');
            $user->profile_photo_path = $path;
        }


        // Cek input dari radio button 'email_verified_at'
        $emailVerifiedOption = $request->input('email_verified_at');

        switch($emailVerifiedOption) {
            case 'now':
                $user->email_verified_at = now();
                break;
            case 'unverify':
                $user->email_verified_at = null;
                break;
            case 'keep':
                // Tidak perlu mengubah nilai email_verified_at
                break;
        }
        
        $user->save();

        $request->session()->flash('user.id', $user->id);

        return redirect()->route('app.users.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function destroy(Request $request, User $user): Response
    {
        $user->delete();

        return redirect()->route('app.users.index');
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
