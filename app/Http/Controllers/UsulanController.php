<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Usulan;
use App\Models\District;
use App\Models\Village;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreUsulanRequest;
use App\Http\Requests\UpdateUsulanRequest;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class UsulanController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('usulan_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

                if ($request->ajax() || $request->wantsJson()) {
            $query = Usulan::query()
                ->with([
                    'user:id,name',
                    'district:id,name',
                    'village:id,name'
                ]);

            // Filter berdasarkan role user - menggunakan hybrid system
            $user = Auth::user();

            // Support kedua sistem: role_id dan Spatie roles
            $isAdminOrTfl = $user->role_id == 1 || // Super Admin (ID)
                            $user->role_id == 2 || // Admin (ID)
                            $user->role_id == 10 || // TFL (ID)
                            $user->hasRole(['Super Admin', 'Admin', 'tfl']); // Spatie roles

            if (!$isAdminOrTfl) {
                // User biasa hanya bisa melihat usulan milik mereka sendiri
                $query->where('user_id', $user->id);
            }

            // Apply filters dari DataTable
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('jenis')) {
                $query->where('jenis_usulan', $request->jenis);
            }

            if ($request->filled('search')) {
                $searchTerm = strtolower($request->search);
                $query->where(function($q) use ($searchTerm) {
                    $q->where(DB::raw('LOWER(nama)'), 'like', "%{$searchTerm}%")
                      ->orWhere(DB::raw('LOWER(nik)'), 'like', "%{$searchTerm}%");
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('nama', function($row) {
                    return $row->nama;
                })
                ->addColumn('nik', function($row) {
                    return $row->nik;
                })
                ->addColumn('village_name', function($row) {
                    return $row->village->name ?? '-';
                })
                ->addColumn('created_at', function($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('status_badge', function($row) {
                    $badgeClass = match($row->status) {
                        'pending' => 'badge bg-warning',
                        'accepted' => 'badge bg-success',
                        'rejected' => 'badge bg-danger',
                        default => 'badge bg-secondary'
                    };
                    return '<span class="' . $badgeClass . '">' . $row->status_label . '</span>';
                })
                ->addColumn('status_raw', function($row) {
                    return $row->status; // For sorting purposes
                })
                ->addColumn('jenis_usulan_badge', function($row) {
                    $badgeClass = $row->jenis_usulan === 'RTLH' ? 'badge bg-primary' : 'badge bg-secondary';
                    return '<span class="' . $badgeClass . '">' . $row->jenis_usulan_label . '</span>';
                })
                ->addColumn('jenis_raw', function($row) {
                    return $row->jenis_usulan; // For sorting purposes
                })
                ->addColumn('foto_preview', function($row) {
                    if ($row->foto_rumah) {
                        return '<img src="' . $row->foto_rumah_url . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;">';
                    }
                    return '<span class="text-muted">Tidak ada foto</span>';
                })
                ->addColumn('action', function($row) {
                    $actions = '';
                    $user = Auth::user();

                    // Tombol Lihat - user bisa lihat usulan milik mereka atau admin bisa lihat semua
                    $isAdminOrTfl = $user->role_id == 1 || $user->role_id == 2 || $user->role_id == 10 ||
                                    $user->hasRole(['Super Admin', 'Admin', 'tfl']);
                    $canView = $isAdminOrTfl || $row->user_id === $user->id;
                    if ($canView && Gate::allows('usulan_show')) {
                        $actions .= '<a href="' . route('usulan.proposals.show', $row->id) . '" class="btn btn-xs btn-info me-1">Lihat</a> ';
                    }

                    if ($row->canBeEditedBy($user)) {
                        $actions .= '<a href="' . route('usulan.proposals.edit', $row->id) . '" class="btn btn-xs btn-primary me-1">Edit</a> ';
                    }

                    // Verifikasi hanya untuk admin/tfl
                    $canVerify = $row->canBeVerifiedBy($user);
                    $isPending = $row->isPending();

                    if ($canVerify && $isPending) {
                        $actions .= '<a href="' . route('usulan.verifikasi', $row->id) . '" class="btn btn-xs btn-warning me-1">Verifikasi</a> ';
                    }

                    if ($row->canBeDeletedBy($user)) {
                        $actions .= '<form method="POST" action="' . route('usulan.proposals.destroy', $row->id) . '" style="display:inline;">';
                        $actions .= csrf_field();
                        $actions .= method_field('DELETE');
                        $actions .= '<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus usulan ini?\')">Hapus</button>';
                        $actions .= '</form>';
                    }

                    return $actions;
                })
                ->rawColumns(['placeholder', 'status_badge', 'jenis_usulan_badge', 'foto_preview', 'action'])
                ->setRowId(function ($row) {
                    return 'row_'.$row->id;
                })
                ->orderColumn('status_badge', 'status $1')
                ->orderColumn('village_name', function($query, $order) {
                    $query->join('villages', 'villages.id', '=', 'usulans.village_id')
                          ->orderBy('villages.name', $order)
                          ->select('usulans.*');
                })
                ->orderColumn('jenis_usulan_badge', 'jenis_usulan $1')
                ->orderColumn('nama', 'nama $1')
                ->smart(false)
                ->toJson();
        }

        return view('usulan.index');
    }

    public function create()
    {
        abort_if(Gate::denies('usulan_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');
        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');
        $jenisUsulanOptions = ['RTLH' => 'Rumah Tidak Layak Huni', 'Rumah Korban Bencana' => 'Rumah Korban Bencana'];

        return view('usulan.create', compact('districts', 'villages', 'jenisUsulanOptions'));
    }

    public function store(StoreUsulanRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            // Handle file upload
            if ($request->hasFile('foto_rumah')) {
                $file = $request->file('foto_rumah');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('usulan/fotos', $filename, 'public');
                $validatedData['foto_rumah'] = $path;
            }

            $usulan = Usulan::create($validatedData);

            // Buat notifikasi untuk admin/TFL
            $this->notifyAdmins($usulan);

            return redirect()->route('usulan.proposals.index')->with('success', 'Usulan berhasil disimpan. Tim kami akan segera memverifikasi usulan Anda.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan usulan: ' . $e->getMessage()]);
        }
    }

    public function show(Usulan $usulan)
    {
        abort_if(Gate::denies('usulan_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check authorization - user hanya bisa lihat usulan miliknya, admin/tfl bisa lihat semua
        $user = Auth::user();

        // Hybrid check: role_id atau Spatie roles
        $isAdminOrTfl = $user->role_id == 1 || $user->role_id == 2 || $user->role_id == 10 ||
                        $user->hasRole(['Super Admin', 'Admin', 'tfl']);

        if (!$isAdminOrTfl && $usulan->user_id !== $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki akses ke usulan ini.');
        }

        $usulan->load(['user', 'district', 'village', 'verifikasi.verifikator']);

        return view('usulan.show', compact('usulan'));
    }

    public function edit(Usulan $usulan)
    {
        abort_if(Gate::denies('usulan_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check authorization menggunakan method dari model
        if (!$usulan->canBeEditedBy(Auth::user())) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak dapat mengedit usulan ini.');
        }

        $districts = District::select('id','name')->pluck('name', 'id')->prepend("Pilih Kecamatan", '');
        $villages = Village::select('id','name', 'district_id')->pluck('name','id')->prepend("Pilih Kelurahan / Desa", '');
        $jenisUsulanOptions = ['RTLH' => 'Rumah Tidak Layak Huni', 'Rumah Korban Bencana' => 'Rumah Korban Bencana'];

        $usulan->load(['district', 'village']);

        return view('usulan.edit', compact('usulan', 'districts', 'villages', 'jenisUsulanOptions'));
    }

    public function update(UpdateUsulanRequest $request, Usulan $usulan)
    {
        // Check authorization menggunakan method dari model
        if (!$usulan->canBeEditedBy(Auth::user())) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak dapat mengedit usulan ini.');
        }

        try {
            $validatedData = $request->validated();

            // Handle file upload
            if ($request->hasFile('foto_rumah')) {
                // Hapus file lama jika ada
                if ($usulan->foto_rumah) {
                    Storage::disk('public')->delete($usulan->foto_rumah);
                }

                $file = $request->file('foto_rumah');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('usulan/fotos', $filename, 'public');
                $validatedData['foto_rumah'] = $path;
            }

            $usulan->update($validatedData);

            return redirect()->route('usulan.proposals.index')->with('success', 'Usulan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui usulan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Usulan $usulan)
    {
        abort_if(Gate::denies('usulan_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Check authorization menggunakan method dari model
        if (!$usulan->canBeDeletedBy(Auth::user())) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak dapat menghapus usulan ini.');
        }

        try {
            // Hapus file foto jika ada
            if ($usulan->foto_rumah) {
                Storage::disk('public')->delete($usulan->foto_rumah);
            }

            $usulan->delete();

            return redirect()->route('usulan.proposals.index')->with('success', 'Usulan berhasil dihapus.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus usulan: ' . $e->getMessage()]);
        }
    }

    public function verifikasi(Usulan $usulan)
    {
        abort_if(Gate::denies('usulan_verify'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if (!$usulan->canBeVerifiedBy(Auth::user())) {
            abort(403, 'Anda tidak dapat memverifikasi usulan ini.');
        }

        if (!$usulan->isPending()) {
            abort(403, 'Usulan ini sudah diverifikasi.');
        }

        $usulan->load(['user', 'district', 'village']);

        return view('usulan.verifikasi', compact('usulan'));
    }

    // API Methods untuk validasi real-time
    public function validateNIK(Request $request)
    {
        $nik = $request->get('nik');
        $usulanId = $request->get('usulan_id');

        $query = Usulan::where('nik', $nik);
        if ($usulanId) {
            $query->where('id', '!=', $usulanId);
        }

        $exists = $query->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'NIK sudah terdaftar dalam sistem.' : 'NIK tersedia.'
        ]);
    }

    public function validateKK(Request $request)
    {
        $kk = $request->get('nomor_kk');
        $usulanId = $request->get('usulan_id');

        $query = Usulan::where('nomor_kk', $kk);
        if ($usulanId) {
            $query->where('id', '!=', $usulanId);
        }

        $exists = $query->exists();

        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'Nomor KK sudah terdaftar dalam sistem.' : 'Nomor KK tersedia.'
        ]);
    }

    public function getVillagesByDistrict(Request $request)
    {
        $districtId = $request->get('district_id');

        $villages = Village::where('district_id', $districtId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($villages);
    }

    private function notifyAdmins($usulan)
    {
        // Ambil semua admin dan TFL
        $admins = \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Super Admin','Admin', 'tfl']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\UsulanBaruNotification($usulan->id));
        }
    }
}
