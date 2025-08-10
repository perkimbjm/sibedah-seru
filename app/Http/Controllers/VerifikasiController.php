<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Usulan;
use App\Models\Verifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreVerifikasiRequest;
use App\Http\Requests\UpdateVerifikasiRequest;
use Symfony\Component\HttpFoundation\Response;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('verifikasi_access')) {
            abort(403, 'Anda tidak memiliki akses ke modul verifikasi.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            $query = Verifikasi::query()
                ->with([
                    'usulan.user:id,name',
                    'usulan.district:id,name',
                    'usulan.village:id,name',
                    'verifikator:id,name'
                ]);

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('placeholder', '&nbsp;')
                ->addColumn('nama_pengusul', function($row) {
                    return $row->usulan->user->name ?? '-';
                })
                ->addColumn('nama_usulan', function($row) {
                    return $row->usulan->nama ?? '-';
                })
                ->addColumn('nik', function($row) {
                    return $row->usulan->nik ?? '-';
                })
                ->addColumn('jenis_usulan', function($row) {
                    return $row->usulan->jenis_usulan_label ?? '-';
                })
                ->addColumn('status', function($row) {
                    $badgeClass = match($row->usulan->status) {
                        'pending' => 'badge bg-warning',
                        'verified' => 'badge bg-info',
                        'accepted' => 'badge bg-success',
                        'rejected' => 'badge bg-danger',
                        default => 'badge bg-secondary'
                    };
                    return '<span class="' . $badgeClass . '">' . $row->usulan->status_label . '</span>';
                })
                ->addColumn('tanggal_usulan', function($row) {
                    return $row->usulan->created_at->format('d/m/Y H:i');
                })
                ->addColumn('verifikator', function($row) {
                    return $row->verifikator->name ?? '-';
                })
                ->addColumn('hasil_verifikasi', function($row) {
                    if (!$row->hasil_verifikasi) {
                        return '<span class="badge bg-secondary">Belum Diverifikasi</span>';
                    }
                    $badgeClass = $row->isAccepted() ? 'badge bg-success' : 'badge bg-danger';
                    return '<span class="' . $badgeClass . '">' . $row->hasil_verifikasi_label . '</span>';
                })
                ->addColumn('tanggal_verifikasi', function($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('action', function($row) {
                    $actions = '';

                    if ($row->usulan->canBeVerifiedBy(Auth::user()) && $row->usulan->isPending()) {
                        $actions .= '<a href="' . route('usulan.verifikasi', $row->usulan->id) . '" class="btn btn-xs btn-warning">Verifikasi</a> ';
                    }

                    if ($row->canBeEditedBy(Auth::user())) {
                        $actions .= '<a href="' . route('usulan.verifikasi-management.verifikasi.edit', $row->id) . '" class="btn btn-xs btn-primary">Edit</a> ';
                    }

                    if ($row->canBeDeletedBy(Auth::user())) {
                        $actions .= '<form method="POST" action="' . route('usulan.verifikasi-management.verifikasi.destroy', $row->id) . '" style="display:inline;">';
                        $actions .= csrf_field();
                        $actions .= method_field('DELETE');
                        $actions .= '<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus verifikasi ini?\')">Hapus</button>';
                        $actions .= '</form>';
                    }

                    // Button "Masukkan ke Data RTLH" untuk data yang diterima
                    if ($row->isAccepted() && $row->usulan->status === 'accepted') {
                        // Cek apakah NIK sudah ada di data RTLH
                        $nikExists = \App\Models\Rtlh::where('nik', $row->usulan->nik)->exists();
                        if (!$nikExists) {
                            $actions .= '<a href="' . route('usulan.verifikasi-management.add-to-rtlh', $row->id) . '" class="btn btn-xs btn-success">Masukkan ke Data RTLH</a> ';
                        }
                    }

                    return $actions;
                })
                ->rawColumns(['placeholder', 'status', 'hasil_verifikasi', 'action'])
                ->setRowId(function ($row) {
                    return 'row_'.$row->id;
                })
                ->smart(true)
                ->toJson();
        }

        return view('verifikasi.index');
    }

    public function create()
    {
        if (!Gate::allows('verifikasi_create')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat verifikasi.');
        }

        $usulans = Usulan::pending()
            ->with(['user', 'district', 'village'])
            ->get();

        return view('verifikasi.create', compact('usulans'));
    }

    public function store(StoreVerifikasiRequest $request, Usulan $usulan)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['usulan_id'] = $usulan->id;
            $validatedData['verifikator_id'] = Auth::id();

            // Fix: Map form value "ditolak" to database enum value "belum_memenuhi_syarat"
            if (isset($validatedData['hasil_verifikasi']) && $validatedData['hasil_verifikasi'] === 'ditolak') {
                $validatedData['hasil_verifikasi'] = 'belum_memenuhi_syarat';
            }

            // Buat verifikasi
            $verifikasi = Verifikasi::create($validatedData);

            // Update status usulan
            $usulan->update(['status' => 'verified']);

            // Jika diterima, update status menjadi accepted
            if ($verifikasi->isAccepted()) {
                $usulan->update(['status' => 'accepted']);

                // Buat notifikasi untuk user
                $usulan->user->notify(new \App\Notifications\UsulanDiterimaNotification($usulan->id));
            } else {
                $usulan->update(['status' => 'rejected']);

                // Buat notifikasi untuk user
                $usulan->user->notify(new \App\Notifications\UsulanDitolakNotification($usulan->id, $verifikasi->catatan_verifikator));
            }

            // Buat notifikasi untuk admin/TFL lain
            $this->notifyOtherAdmins($usulan, $verifikasi);

            DB::commit();

            // Cek jika diterima dan redirect ke form RTLH
            if ($verifikasi->isAccepted() && $usulan->jenis_usulan == 'RTLH') {
                $usulan->load('district'); // Eager load district
                $usulanData = [
                    'nik' => $usulan->nik,
                    'kk' => $usulan->nomor_kk,
                    'name' => $usulan->nama,
                    'address' => $usulan->alamat_lengkap,
                    'village_id' => $usulan->village_id,
                    'district_id' => $usulan->district_id,
                    'district_name' => $usulan->district->name ?? '',
                    'lat' => $usulan->latitude,
                    'lng' => $usulan->longitude,
                ];

                return redirect()->route('app.rtlh.create', $usulanData)
                                 ->with('success', 'Verifikasi berhasil disimpan. Silakan lengkapi data RTLH berikut.');
            }

            return redirect()->route('usulan.proposals.index')->with('success', 'Verifikasi berhasil disimpan.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan verifikasi: ' . $e->getMessage()]);
        }
    }

    public function show(Verifikasi $verifikasi)
    {
        if (!Gate::allows('verifikasi_show')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat verifikasi.');
        }

        $verifikasi->load(['usulan.user', 'usulan.district', 'usulan.village', 'verifikator']);

        return view('verifikasi.show', compact('verifikasi'));
    }

    public function edit(Verifikasi $verifikasi)
    {
        if (!Gate::allows('verifikasi_edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit verifikasi.');
        }

        // Hanya verifikator yang bisa edit verifikasinya sendiri
        if ($verifikasi->verifikator_id !== Auth::id()) {
            abort(403, 'Anda hanya dapat mengedit verifikasi yang Anda buat.');
        }

        $verifikasi->load(['usulan.user', 'usulan.district', 'usulan.village']);

        return view('verifikasi.edit', compact('verifikasi'));
    }

    public function update(UpdateVerifikasiRequest $request, Verifikasi $verifikasi)
    {
        if (!Gate::allows('verifikasi_edit')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit verifikasi.');
        }

        // Hanya verifikator yang bisa edit verifikasinya sendiri
        if ($verifikasi->verifikator_id !== Auth::id()) {
            abort(403, 'Anda hanya dapat mengedit verifikasi yang Anda buat.');
        }



        try {
            DB::beginTransaction();

            // Debug: Log semua data yang diterima
            Log::info('Verifikasi Update - Raw Request Data', [
                'all_data' => $request->all(),
                'verifikasi_id' => $verifikasi->id
            ]);

            // Gunakan validated data dari UpdateVerifikasiRequest
            $validatedData = $request->validated();

            $criteriaFields = [
                'kesesuaian_tata_ruang',
                'tidak_dalam_sengketa',
                'memiliki_alas_hak',
                'satu_satunya_rumah',
                'belum_pernah_bantuan',
                'berpenghasilan_rendah'
            ];
            foreach ($criteriaFields as $field) {
                $validatedData[$field] = $request->input($field) == '1';

                // Debug: Log setiap kriteria
                Log::info("Kriteria {$field}", [
                    'input_value' => $request->input($field),
                    'final_value' => $validatedData[$field]
                ]);
            }

            // Fill kriteria ke model, tapi belum save
            $verifikasi->fill($validatedData);

            // Cek apakah semua kriteria terpenuhi
            $kriteria = [
                $verifikasi->kesesuaian_tata_ruang,
                $verifikasi->tidak_dalam_sengketa,
                $verifikasi->memiliki_alas_hak,
                $verifikasi->satu_satunya_rumah,
                $verifikasi->belum_pernah_bantuan,
                $verifikasi->berpenghasilan_rendah,
            ];
            $semuaKriteriaTerpenuhi = !in_array(false, $kriteria, true);

            $usulan = $verifikasi->usulan;
            if ($semuaKriteriaTerpenuhi) {
                $verifikasi->hasil_verifikasi = 'diterima';
                $usulan->update(['status' => 'accepted']);
                $usulan->user->notify(new \App\Notifications\UsulanDiterimaNotification($usulan->id));
            } else {
                // Debug: Log sebelum validasi catatan
                Log::info('Validasi catatan verifikator', [
                    'catatan_verifikator' => $validatedData['catatan_verifikator'],
                    'is_empty' => empty($validatedData['catatan_verifikator'])
                ]);

                if (empty($validatedData['catatan_verifikator'])) {
                    Log::info('Return back karena catatan kosong');
                    return back()->withErrors(['catatan_verifikator' => 'Catatan verifikator wajib diisi jika usulan ditolak.']);
                }
                $verifikasi->hasil_verifikasi = 'belum_memenuhi_syarat';
                $usulan->update(['status' => 'rejected']);
                $usulan->user->notify(new \App\Notifications\UsulanDitolakNotification($usulan->id, $verifikasi->catatan_verifikator));
            }

            // Simpan semua perubahan sekaligus
            $verifikasi->save();

            // Debug: Log data setelah update
            Log::info('Verifikasi Update - After Save', [
                'updated_verifikasi' => $verifikasi->fresh()->toArray(),
                'semua_kriteria_terpenuhi' => $semuaKriteriaTerpenuhi
            ]);

            DB::commit();

            $statusMessage = $semuaKriteriaTerpenuhi ? 'DITERIMA' : 'DITOLAK';
            return redirect()->route('usulan.verifikasi-management.verifikasi.index')
                ->with('success', "Verifikasi berhasil diperbarui. Status: {$statusMessage}");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui verifikasi: ' . $e->getMessage()]);
        }
    }

    public function destroy(Verifikasi $verifikasi)
    {
        if (!Gate::allows('verifikasi_delete')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus verifikasi.');
        }

        try {
            DB::beginTransaction();

            // Reset status usulan ke pending
            $usulan = $verifikasi->usulan;
            $usulan->update(['status' => 'pending']);

            // Hapus verifikasi
            $verifikasi->delete();

            DB::commit();

            return redirect()->route('usulan.verifikasi-management.verifikasi.index')->with('success', 'Verifikasi berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus verifikasi: ' . $e->getMessage()]);
        }
    }

    // API Methods
    public function getVerifikasiStats()
    {
        $stats = [
            'total_usulan' => Usulan::count(),
            'pending_usulan' => Usulan::pending()->count(),
            'verified_usulan' => Usulan::verified()->count(),
            'accepted_usulan' => Usulan::accepted()->count(),
            'rejected_usulan' => Usulan::rejected()->count(),
            'older_than_5_days' => Usulan::pending()->olderThanDays(5)->count(),
        ];

        return response()->json($stats);
    }

    public function getVerifikasiByUsulan(Usulan $usulan)
    {
        $verifikasi = $usulan->verifikasi;

        if (!$verifikasi) {
            return response()->json(['message' => 'Verifikasi tidak ditemukan.'], 404);
        }

        $verifikasi->load(['verifikator']);

        return response()->json([
            'success' => true,
            'data' => $verifikasi
        ]);
    }

    public function addToRtlh(Verifikasi $verifikasi)
    {
        if (!Gate::allows('verifikasi_access')) {
            abort(403, 'Anda tidak memiliki akses ke modul verifikasi.');
        }

        // Cek apakah verifikasi sudah diterima
        if (!$verifikasi->isAccepted()) {
            return redirect()->route('usulan.verifikasi-management.verifikasi.index')
                ->with('error', 'Hanya data yang diterima yang dapat dimasukkan ke Data RTLH.');
        }

        // Cek apakah NIK sudah ada di data RTLH
        $nikExists = \App\Models\Rtlh::where('nik', $verifikasi->usulan->nik)->exists();
        if ($nikExists) {
            return redirect()->route('usulan.verifikasi-management.verifikasi.index')
                ->with('error', 'Data dengan NIK ' . $verifikasi->usulan->nik . ' sudah ada di Data RTLH.');
        }

        // Redirect ke halaman tambah data RTLH dengan data yang sudah di-parse
        return redirect()->route('app.rtlh.create', [
            'from_verifikasi' => $verifikasi->id,
            'nik' => $verifikasi->usulan->nik,
            'kk' => $verifikasi->usulan->nomor_kk,
            'name' => $verifikasi->usulan->nama,
            'address' => $verifikasi->usulan->alamat_lengkap,
            'district_id' => $verifikasi->usulan->district_id,
            'village_id' => $verifikasi->usulan->village_id,
            'lat' => $verifikasi->usulan->latitude,
            'lng' => $verifikasi->usulan->longitude,
        ]);
    }

    private function notifyOtherAdmins($usulan, $verifikasi)
    {
        // Ambil semua admin dan TFL kecuali yang melakukan verifikasi
        $admins = \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'tfl']);
        })->where('id', '!=', Auth::id())->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\VerifikasiSelesaiNotification($usulan->id, $verifikasi->hasil_verifikasi));
        }
    }

}
