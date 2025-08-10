<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Requests\AduanStoreRequest;
use App\Http\Requests\AduanUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Models\User;
use App\Notifications\AduanBaruNotification;
use App\Notifications\AduanDitanggapiNotification;

class AduanController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role_id, [1, 2]);

        // Base query
        if ($isAdmin) {
            $query = Aduan::with(['district', 'village', 'user']);
        } else {
            $query = Aduan::with(['district', 'village', 'user'])
                ->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere(function($subQ) use ($user) {
                          $subQ->where('email', $user->email)
                               ->whereNull('user_id');
                      });
                });
        }

        // Clone query untuk statistik sebelum filter
        $statsQuery = clone $query;
        $allComplaints = $statsQuery->get();
        $stats = [
            'total' => $allComplaints->count(),
            'pending' => $allComplaints->where('status', 'pending')->count(),
            'process' => $allComplaints->where('status', 'process')->count(),
            'completed' => $allComplaints->where('status', 'completed')->count(),
        ];

        // Cek apakah ada pengaduan lama yang bisa dikaitkan
        $hasLinkableComplaints = false;
        if (!$isAdmin) {
            $hasLinkableComplaints = Aduan::where('email', $user->email)
                ->whereNull('user_id')
                ->exists();
        }

        // Apply filters to the main query
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kode_tiket', 'like', "%{$search}%");
            });
        }

        // Paginate and get other data
        $aduan = $query->orderBy('created_at', 'desc')->paginate(20);
        $districts = District::select('id', 'name')->orderBy('name')->get();

        return view('aduan.index', compact('aduan', 'districts', 'isAdmin', 'stats', 'hasLinkableComplaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Jika user sudah login, cek permission
        if (Auth::check()) {
            if (!Gate::allows('aduan_create')) {
                abort(403, 'Anda tidak memiliki izin untuk membuat aduan.');
            }
        }

        $districts = District::select('id', 'name')->orderBy('name')->get();
        return view('aduan.create', compact('districts'));
    }

    /**
     * Validate email for complaint form
     */
    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $isLoggedIn = Auth::check();

        if ($isLoggedIn) {
            return response()->json([
                'valid' => true,
                'message' => 'Email valid untuk user yang sudah login'
            ]);
        }

        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return response()->json([
                'valid' => false,
                'message' => 'Email yang dimasukkan sudah terdaftar dan anda harus login untuk menggunakannya.'
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Email valid untuk pengaduan'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AduanStoreRequest $request)
    {
        // Jika user sudah login, cek permission
        if (Auth::check()) {
            if (!Gate::allows('aduan_create')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk membuat aduan.'
                ], 403);
            }
        }

        // Pastikan session dimulai


        // Lewati validasi captcha untuk user yang sudah login
        if (!Auth::check()) {
            // Debug logging
            $captchaAnswer = Session::get('captcha_answer');
            Log::info('Store captcha validation:', [
                'session_id' => session()->getId(),
                'captcha_answer_in_session' => $captchaAnswer,
                'captcha_input' => $request->captcha,
                'session_data' => session()->all()
            ]);

            // Validasi captcha dengan fallback yang lebih baik
            $inputAnswer = (int)$request->captcha;

            if (!$captchaAnswer) {
                Log::warning('Store: Captcha answer not found in session. Using fallback validation.');

                // Fallback: Validasi rentang matematik sederhana
                if ($inputAnswer < 2 || $inputAnswer > 20) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Captcha salah. Silakan coba lagi.',
                        'debug' => [
                            'expected' => 'session_not_found',
                            'received' => $request->captcha,
                            'session_id' => session()->getId(),
                            'fallback_used' => true
                        ]
                    ], 422);
                }

                // Jika dalam rentang yang masuk akal, kita terima
                Log::info('Store: Captcha validated using fallback method', [
                    'input' => $inputAnswer,
                    'session_id' => session()->getId()
                ]);

            } else if ($inputAnswer !== (int)$captchaAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha salah. Silakan coba lagi.',
                    'debug' => [
                        'expected' => $captchaAnswer,
                        'received' => $request->captcha,
                        'session_id' => session()->getId(),
                        'fallback_used' => false
                    ]
                ], 422);
            }
        } else {
            Log::info('Store: Skipping captcha for authenticated user', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);
        }

        $validated = $request->validated();

        // Hapus captcha dari data yang akan disimpan
        unset($validated['captcha']);

        // Handle upload foto jika ada
        if ($request->hasFile('foto')) {
            try {
                $foto = $request->file('foto');
                $fotoName = 'aduan_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('aduan', $fotoName, 'public');
                $validated['foto'] = $fotoPath;
            } catch (\Exception $e) {
                Log::error('Error uploading foto:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengupload foto. Silakan coba lagi.'
                ], 500);
            }
        }

        // Tambahkan user_id dan email jika user login
        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
            // Jika email kosong, gunakan email user yang login
            if (empty($validated['email'])) {
                $validated['email'] = Auth::user()->email;
            }
        }

        try {
            $aduan = Aduan::create($validated);

            // Clear captcha dari session
            Session::forget('captcha_answer');

            // Kirim notifikasi ke admin/TFL terkait adanya aduan baru
            try {
                $this->notifyAdminsOfNewComplaint($aduan->id);
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim notifikasi aduan baru', [
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dikirim!',
                'data' => [
                    'kode_tiket' => $aduan->kode_tiket,
                    'pesan' => 'Simpan kode tiket ini untuk melacak pengaduan Anda: ' . $aduan->kode_tiket
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating aduan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan pengaduan. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Kirim notifikasi ke admin/TFL saat ada aduan baru.
     */
    private function notifyAdminsOfNewComplaint(int $aduanId): void
    {
        // Ambil semua admin dan TFL (hybrid: role_id dan Spatie roles)
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'tfl']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new AduanBaruNotification($aduanId));
        }
    }

    /**
     * Notifikasi ke pemilik aduan saat ditanggapi.
     */
    private function notifyComplaintOwnerOfResponse(Aduan $aduan, ?string $responsePreview = null): void
    {
        // Jika terkait user langsung
        if ($aduan->user) {
            $aduan->user->notify(new AduanDitanggapiNotification($aduan->id, $responsePreview));
            return;
        }

        // Fallback: coba cari user berdasarkan email aduan lama
        if (!empty($aduan->email)) {
            $user = User::where('email', $aduan->email)->first();
            if ($user) {
                $user->notify(new AduanDitanggapiNotification($aduan->id, $responsePreview));
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Aduan $aduan)
    {
        // Cek permission untuk melihat aduan
        if (!Gate::allows('aduan_show')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat aduan.');
        }

        $user = Auth::user();
        $isAdmin = in_array($user->role_id, [1, 2]);

        // User biasa hanya boleh melihat pengaduan miliknya sendiri
        // Cek berdasarkan user_id atau email jika user_id null (untuk aduan lama)
        if (!$isAdmin) {
            $canAccess = ($aduan->user_id === $user->id) ||
                        ($aduan->user_id === null && $aduan->email === $user->email);

            if (!$canAccess) {
                abort(403, 'Anda tidak memiliki akses untuk melihat pengaduan ini.');
            }
        }

        $aduan->load(['district', 'village', 'user']);
        return view('aduan.show', compact('aduan', 'isAdmin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aduan $aduan)
    {
        // Cek permission untuk mengedit aduan
        if (!Gate::allows('aduan_edit')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit aduan.');
        }

        $user = Auth::user();
        $isAdmin = in_array($user->role_id, [1, 2]);

        // Hanya admin yang boleh mengedit/merespon pengaduan
        if (!$isAdmin) {
            abort(403, 'Hanya admin yang dapat memberikan tanggapan pengaduan.');
        }

        $aduan->load(['district', 'village']);
        $districts = District::select('id', 'name')->orderBy('name')->get();
        $villages = Village::where('district_id', $aduan->district_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('aduan.edit', compact('aduan', 'districts', 'villages', 'isAdmin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AduanUpdateRequest $request, Aduan $aduan)
    {
        // Cek permission untuk mengedit aduan
        if (!Gate::allows('aduan_edit')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit aduan.'
            ], 403);
        }

        $user = Auth::user();

        // Debug: pastikan user ada
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi.'
            ], 401);
        }

        // Debug: pastikan role_id ada
        if (!isset($user->role_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Role user tidak ditemukan.'
            ], 403);
        }

        $isAdmin = in_array($user->role_id, [1, 2]);

        // Hanya admin yang boleh update/merespon pengaduan
        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat memberikan tanggapan pengaduan.'
            ], 403);
        }

        try {
            $validated = $request->validated();

            // Validasi: Admin tidak boleh mengubah status ke completed
            if (isset($validated['status']) && $validated['status'] === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin tidak dapat mengubah status ke "Selesai". Status tersebut hanya dapat diubah oleh pengadu.'
                ], 422);
            }

            // Update data - filter hanya field yang diizinkan untuk admin
            $allowedFields = ['respon', 'respon2', 'respon3', 'status'];
            $updateData = array_intersect_key($validated, array_flip($allowedFields));

            // Pastikan setidaknya ada field respon yang diisi (kecuali hanya update status)
            $hasResponse = false;
            $onlyStatusUpdate = count($updateData) === 1 && isset($updateData['status']);

            foreach(['respon', 'respon2', 'respon3'] as $field) {
                if (isset($updateData[$field]) && !empty(trim($updateData[$field]))) {
                    $hasResponse = true;
                    break;
                }
            }

            if (!$hasResponse && !$onlyStatusUpdate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggapan tidak boleh kosong.'
                ], 422);
            }

            // Update data
            $aduan->update($updateData);

            // Auto-update status berdasarkan respon (hanya dari pending ke process)
            if (!empty($validated['respon']) && $aduan->status === 'pending') {
                $aduan->update(['status' => 'process']);
            }

            // Kirim notifikasi ke pemilik aduan jika ada tanggapan baru
            $newResponse = null;
            foreach (['respon', 'respon2', 'respon3'] as $field) {
                if (array_key_exists($field, $updateData) && !empty(trim((string)$updateData[$field]))) {
                    $newResponse = (string)$updateData[$field];
                    break;
                }
            }
            if ($newResponse !== null) {
                $this->notifyComplaintOwnerOfResponse($aduan, $newResponse);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tanggapan berhasil dikirim!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aduan $aduan)
    {
        // Cek permission untuk menghapus aduan
        if (!Gate::allows('aduan_delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus aduan.'
            ], 403);
        }

        try {
            $aduan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }

    /**
     * Tracking pengaduan berdasarkan kode tiket
     */
    public function track(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required|string|max:20'
        ]);

        $aduan = Aduan::with(['district', 'village'])
            ->where('kode_tiket', $request->kode_tiket)
            ->first();

        if (!$aduan) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tiket tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aduan->id,
                'kode_tiket' => $aduan->kode_tiket,
                'name' => $aduan->name,
                'created_at' => $aduan->created_at->toISOString(),
                'status' => $aduan->status,
                'district' => [
                    'id' => $aduan->district->id,
                    'name' => $aduan->district->name
                ],
                'village' => [
                    'id' => $aduan->village->id,
                    'name' => $aduan->village->name
                ],
                'complain' => $aduan->complain,
                'foto_url' => $aduan->foto_url,
                'complain2' => $aduan->complain2,
                'complain3' => $aduan->complain3,
                'complain2_at' => $aduan->complain2_at ? $aduan->complain2_at->toISOString() : null,
                'complain3_at' => $aduan->complain3_at ? $aduan->complain3_at->toISOString() : null,
                'respon' => $aduan->respon,
                'respon2' => $aduan->respon2,
                'respon3' => $aduan->respon3,
                'respon_at' => $aduan->respon_at ? $aduan->respon_at->toISOString() : null,
                'respon2_at' => $aduan->respon2_at ? $aduan->respon2_at->toISOString() : null,
                'respon3_at' => $aduan->respon3_at ? $aduan->respon3_at->toISOString() : null,
                'expect' => $aduan->expect,
                'can_add_complaint' => $aduan->canAddComplaint(),
                'can_evaluate' => $aduan->status === 'completed' && !$aduan->expect
            ]
        ]);
    }

    /**
     * Menambah pengaduan lanjutan dari user
     */
    public function addComplaint(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required|string|exists:aduan,kode_tiket',
            'complaint_text' => 'required|string|max:2000|min:10',
            'captcha' => 'required|integer'
        ]);

        // Pastikan session dimulai


        // Debug logging
        $captchaAnswer = Session::get('captcha_answer');
        Log::info('Captcha validation:', [
            'session_id' => session()->getId(),
            'captcha_answer_in_session' => $captchaAnswer,
            'captcha_input' => $request->captcha,
            'session_data' => session()->all()
        ]);

        // Validasi captcha dengan fallback yang lebih baik
        $inputAnswer = (int)$request->captcha;

        if (!$captchaAnswer) {
            Log::warning('Captcha answer not found in session. Using fallback validation.');

            // Fallback: Validasi rentang matematik sederhana
            if ($inputAnswer < 2 || $inputAnswer > 20) {
                return response()->json([
                    'success' => false,
                    'message' => 'Captcha salah. Silakan coba lagi.',
                    'debug' => [
                        'expected' => 'session_not_found',
                        'received' => $request->captcha,
                        'session_id' => session()->getId(),
                        'fallback_used' => true
                    ]
                ], 422);
            }

            // Jika dalam rentang yang masuk akal, kita terima
            Log::info('Captcha validated using fallback method', [
                'input' => $inputAnswer,
                'session_id' => session()->getId()
            ]);

        } else if ($inputAnswer !== (int)$captchaAnswer) {
            return response()->json([
                'success' => false,
                'message' => 'Captcha salah. Silakan coba lagi.',
                'debug' => [
                    'expected' => $captchaAnswer,
                    'received' => $request->captcha,
                    'session_id' => session()->getId(),
                    'fallback_used' => false
                ]
            ], 422);
        }

        $aduan = Aduan::where('kode_tiket', $request->kode_tiket)->first();

        if (!$aduan->canAddComplaint()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menambah pengaduan lagi.'
            ], 422);
        }

        $nextField = $aduan->getNextComplaintField();
        $timestampField = $nextField . '_at';

        $aduan->update([
            $nextField => htmlspecialchars(strip_tags($request->complaint_text), ENT_QUOTES, 'UTF-8'),
            $timestampField => now()
        ]);

        Session::forget('captcha_answer');

        return response()->json([
            'success' => true,
            'message' => 'Pengaduan lanjutan berhasil ditambahkan!'
        ]);
    }

    /**
     * Menambah evaluasi dari user
     */
    public function addEvaluation(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required|string|exists:aduan,kode_tiket',
            'rating' => 'required|integer|min:1|max:4'
        ]);

        $aduan = Aduan::where('kode_tiket', $request->kode_tiket)->first();

        if ($aduan->status !== 'completed' || $aduan->expect) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memberikan evaluasi saat ini.'
            ], 422);
        }

        $aduan->update(['expect' => $request->rating]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas evaluasi Anda!'
        ]);
    }

    /**
     * Menyelesaikan pengaduan dari user tanpa login
     */
    public function completeComplaint(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required|string|exists:aduan,kode_tiket'
        ]);

        $aduan = Aduan::where('kode_tiket', $request->kode_tiket)->first();

        // Validasi: hanya bisa diselesaikan jika sudah ada respon dari admin dan belum completed
        if ($aduan->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan sudah dalam status selesai.'
            ], 422);
        }

        if (!$aduan->respon && !$aduan->respon2 && !$aduan->respon3) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaduan belum mendapat tanggapan dari admin.'
            ], 422);
        }

        try {
            $aduan->update(['status' => 'completed']);

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan berhasil diselesaikan!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyelesaikan pengaduan.'
            ], 500);
        }
    }

    /**
     * Generate captcha untuk form
     */
    public function generateCaptcha()
    {
        try {
            // Pastikan session dimulai
            if (!session()->isStarted()) {
                session()->start();
            }

            $num1 = rand(1, 10);
            $num2 = rand(1, 10);
            $answer = $num1 + $num2;

            Session::put('captcha_answer', $answer);

            // Debug logging
            Log::info('Captcha generated:', [
                'session_id' => session()->getId(),
                'question' => "{$num1} + {$num2} = ?",
                'answer' => $answer,
                'session_data' => session()->all()
            ]);

            return response()->json([
                'question' => "{$num1} + {$num2} = ?",
                'num1' => $num1,
                'num2' => $num2,
                'session_id' => session()->getId() // Debug info
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating captcha:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error generating captcha',
                'message' => 'Please try again'
            ], 500);
        }
    }

    /**
     * Get villages by district for AJAX
     */
    public function getVillagesByDistrict(Request $request)
    {
        $villages = Village::where('district_id', $request->district_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($villages);
    }

    /**
     * Rekap pengaduan untuk user yang login
     */
    public function myComplaints()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Ambil pengaduan berdasarkan user_id ATAU email untuk pengaduan lama
        $complaints = Aduan::with(['district', 'village'])
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('email', $user->email)
                           ->whereNull('user_id');
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $complaints->count(),
            'pending' => $complaints->where('status', 'pending')->count(),
            'process' => $complaints->where('status', 'process')->count(),
            'completed' => $complaints->where('status', 'completed')->count(),
        ];

        return Inertia::render('Aduan/MyComplaints', [
            'complaints' => $complaints,
            'stats' => $stats
        ]);
    }

    /**
     * Continue complaint conversation
     */
    public function continueComplaint(Request $request, Aduan $aduan)
    {
        $request->validate([
            'complain' => 'required|string|max:2000|min:10'
        ]);

        // Check authorization - user must own this complaint
        if (!Auth::check() || Auth::id() !== $aduan->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk melanjutkan pengaduan ini.'
            ], 403);
        }

        try {
            $nextField = $aduan->getNextComplaintField();
            $timestampField = $nextField . '_at';

            $aduan->update([
                $nextField => htmlspecialchars(strip_tags($request->complain), ENT_QUOTES, 'UTF-8'),
                $timestampField => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengaduan lanjutan berhasil ditambahkan!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambah pengaduan.'
            ], 500);
        }
    }

    /**
     * Link previous complaints with user account based on email
     */
    public function linkPreviousComplaints(?User $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return ['success' => false, 'message' => 'User tidak terautentikasi.'];
        }

        try {
            // Cari pengaduan lama berdasarkan email yang belum terkait dengan user_id
            $previousComplaints = Aduan::where('email', $user->email)
                ->whereNull('user_id')
                ->get();

            if ($previousComplaints->isEmpty()) {
                return [
                    'success' => true,
                    'message' => 'Tidak ada pengaduan lama yang perlu dikaitkan.',
                    'linked_count' => 0
                ];
            }

            // Update pengaduan lama dengan user_id
            $linkedCount = Aduan::where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            Log::info('Previous complaints linked to user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'linked_count' => $linkedCount
            ]);

            return [
                'success' => true,
                'message' => "Berhasil mengaitkan {$linkedCount} pengaduan sebelumnya dengan akun Anda.",
                'linked_count' => $linkedCount
            ];

        } catch (\Exception $e) {
            Log::error('Error linking previous complaints:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengaitkan pengaduan lama.',
                'linked_count' => 0
            ];
        }
    }

    /**
     * API endpoint to manually link previous complaints
     */
    public function linkPreviousComplaintsApi(Request $request)
    {
        $result = $this->linkPreviousComplaints();

        return response()->json($result);
    }

    /**
     * Evaluate complaint service
     */
    public function evaluate(Request $request, Aduan $aduan)
    {
        $request->validate([
            'expect' => 'required|integer|min:1|max:4'
        ]);

        // Check authorization - user must own this complaint
        if (!Auth::check() || Auth::id() !== $aduan->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk mengevaluasi pengaduan ini.'
            ], 403);
        }

        // Check if can evaluate
        if ($aduan->expect || ($aduan->status !== 'completed' && !$aduan->respon)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat memberikan evaluasi saat ini.'
            ], 422);
        }

        try {
            $aduan->update(['expect' => $request->expect]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas evaluasi Anda!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan evaluasi.'
            ], 500);
        }
    }
}
