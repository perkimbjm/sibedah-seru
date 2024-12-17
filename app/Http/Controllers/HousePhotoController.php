<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\HousePhoto;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        // Ambil file dari request
        $file = $request->file('photo');

        // Konversi nama file sesuai format
        $name = strtoupper(Str::slug($rtlh->name, '-'));
        $extension = $file->getClientOriginalExtension();
        $filename = "{$rtlh->id}_{$name}.{$extension}";
        $folder = "rtlh";

        // Cek apakah file dengan nama yang sama sudah ada
        if (Storage::disk('public')->exists("{$folder}/{$filename}")) {
            // Tambahkan angka urut jika file sudah ada
            $counter = 1;
            while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                $filename = "{$rtlh->id}_{$name}_{$counter}.{$extension}";
                $counter++;
            }
        }

        // Simpan file ke folder storage/app/public/rtlh
        $file->storeAs($folder, $filename, 'public');

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

        // Jika ada file foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            Storage::disk('public')->delete($photo->photo_url);

            // Proses file baru
            $file = $request->file('photo');

            // Konversi nama file sesuai format
            $name = strtoupper(Str::slug($house->name, '-'));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$house->id}_{$name}.{$extension}";
            $folder = "rtlh";

            // Cek apakah file dengan nama yang sama sudah ada
            if (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                // Tambahkan angka urut jika file sudah ada
                $counter = 1;
                while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                    $filename = "{$house->id}_{$name}_{$counter}.{$extension}";
                    $counter++;
                }
            }

            // Simpan file baru
            $file->storeAs($folder, $filename, 'public');
            
            // Update path foto di database
            $photo->photo_url = "{$folder}/{$filename}";
        }

        // Update data lainnya
        $photo->update([
            'description' => $request->description,
        ]);

        return redirect()->route('app.rutilahu.index', $rtlh)->with('success', 'Photo updated successfully.');
    }
}
