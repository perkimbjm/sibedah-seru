<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\HousePhoto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\MassDestroyHousePhotoRequest;

class HousePhotoController extends Controller
{
    public function index(Rtlh $rtlh)
    {   
        abort_if(Gate::denies('house_photo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $photos = $rtlh->housePhotos()->paginate(10);
        return view('rutilahu.index', compact('rtlh', 'photos'));
    }

    public function create(Rtlh $rtlh)
    {
        abort_if(Gate::denies('house_photo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('rutilahu.create', compact('rtlh'));
    }

    public function store(Request $request, Rtlh $rtlh)
    {
        abort_if(Gate::denies('house_photo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        // Ambil file dari request
        $file = $request->file('photo');

        // Konversi nama file sesuai format; gambar selalu disimpan ulang sebagai WebP
        $name = strtoupper(Str::slug($rtlh->name, '-'));
        $extension = 'webp';
        $filename = "{$rtlh->id}_{$name}.{$extension}";
        $folder = "rtlh";

        // Cek apakah file dengan nama yang sama sudah ada
        if (Storage::disk('public')->exists("{$folder}/{$filename}")) {
            // Tambahkan angka urut jika file sudah ada
            $counter = 2;
            while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                $filename = "{$rtlh->id}_{$name}_{$counter}.{$extension}";
                $counter++;
            }
        }

        $manager = new ImageManager(Driver::class);

        $image = $manager->read($file)->scale(width: 600);

        $image->toWebp(75)->save(storage_path("app/public/{$folder}/{$filename}"));

        HousePhoto::create([
            'house_id' => $rtlh->id,
            'photo_url' => "{$folder}/{$filename}",
            'description' => $request->description,
        ]);

        return redirect()->route('app.rutilahu.index', $rtlh)->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(Rtlh $rtlh, HousePhoto $photo)
    {
        abort_if(Gate::denies('house_photo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $photo->delete();
        return redirect()->route('app.rutilahu.index', $rtlh)->with('success', 'Photo successfully removed.');
    }

    public function massDestroy(MassDestroyHousePhotoRequest $request)
    {
        HousePhoto::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function edit(Rtlh $rtlh, HousePhoto $photo)
    {
        abort_if(Gate::denies('house_photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('rutilahu.edit', compact('rtlh', 'photo'));
    }

    public function update(Request $request, Rtlh $rtlh, HousePhoto $photo)
    {
        abort_if(Gate::denies('house_photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        try {

            if ($request->hasFile('photo')) {

                $file = $request->file('photo');

                if (!$file->isValid()) {
                    return back()->withErrors([
                        'photo' => 'File yang diupload tidak valid.'
                    ]);
                }

                if ($photo->photo_url) {
                    Storage::disk('public')->delete($photo->photo_url);
                }

                $folder = 'rtlh';

                $name = strtoupper(Str::slug($rtlh->name, '-'));

                $filename = "{$rtlh->id}_{$name}.webp";

                $counter = 2;

                while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                    $filename = "{$rtlh->id}_{$name}_{$counter}.webp";
                    $counter++;
                }

                $manager = new ImageManager(new Driver());

                $image = $manager
                    ->read($file)
                    ->scale(width: 600);

                Storage::disk('public')->put(
                    "{$folder}/{$filename}",
                    (string) $image->toWebp(75)
                );

                $photo->photo_url = "{$folder}/{$filename}";
            }

            $photo->description = $request->description;
            $photo->save();

            return redirect()
                ->route('app.rutilahu.index', $rtlh)
                ->with('success', 'Foto berhasil diperbarui.');

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'photo' => 'Gagal memproses gambar.'
                ]);
        }
    }
}
