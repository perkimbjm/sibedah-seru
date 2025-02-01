<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user(); // Ambil data pengguna yang sedang login
        $confirmsTwoFactorAuthentication = $user->two_factor_secret !== null;
        $sessions = $user->sessions; // Jika Anda menggunakan fitur session management

        return view('profile.show', [
            'user' => $user,
            'confirmsTwoFactorAuthentication' => $confirmsTwoFactorAuthentication,
            'sessions' => $sessions,
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Update data dasar
        $user->update($request->validated());

        // Reset verifikasi email jika email berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        return Redirect::back()->with('status', 'Data Diri Berhasil Diperbarui');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:3072'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        try {
            if ($user->google_id) {
                // Jika user login dengan Google, simpan di field avatar
                $path = $request->file('photo')->store('users', 'public');
                $url = asset('storage/' . $path);

                // Hapus file lama jika ada
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $user->update([
                    'avatar' => $url,
                    'profile_photo_path' => null
                ]);
            } else {
                // Jika user regular, simpan di field profile_photo_path
                // Hapus foto lama
                if ($user->profile_photo_path) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $path = $request->file('photo')->store('users', 'public');

                $user->update([
                    'profile_photo_path' => $path,
                    'avatar' => null
                ]);
            }

        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['photo' => 'Gagal mengupdate foto: ' . $e->getMessage()]);
        }

        return Redirect::back()->with('status', 'Foto profil berhasil diperbarui');
    }

    public function deleteProfilePhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->update([
                'profile_photo_path' => null,
                'avatar' => null
            ]);

        } catch (\Exception $e) {
            return Redirect::back()->withErrors(['photo' => 'Gagal menghapus foto: ' . $e->getMessage()]);
        }

        return Redirect::back()->with('status', 'profile-photo-deleted');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('status', 'Kata sandi berhasil diubah!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Akunmu sudah dihapus secara permanen');
    }

}
