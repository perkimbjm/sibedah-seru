<?php

namespace App\Http\Controllers;

use App\Models\House;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\RenovatedHousePhoto;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\MassDestroyRenovatedHousePhotoRequest;

class RenovatedHousePhotoController extends Controller
{
    public function index(House $house)
    {   
        abort_if(Gate::denies('renovated_house_photo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $photos = $house->renovatedHousePhotos()->paginate(10);
        return view('gallery.index', compact('house', 'photos'));
    }

    public function create(House $house)
    {
        abort_if(Gate::denies('renovated_house_photo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('gallery.create', compact('house'));
    }

    public function store(Request $request, House $house)
    {
        abort_if(Gate::denies('renovated_house_photo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request->validate([
            'photo' => 'required|image|max:6144',
            'progres' => 'required|numeric',
            'description' => 'nullable|string',
            'is_primary' => 'nullable|boolean',
            'is_best' => 'nullable|boolean',
        ]);

        // Ambil file dari request
        $file = $request->file('photo');
        $progres = $request->progres;

        // Konversi nama file sesuai format
        $name = strtoupper(Str::slug($house->name, '-'));
        $extension = $file->getClientOriginalExtension();
        $filename = "{$house->id}_{$name}.{$extension}";

        // Folder berdasarkan progres
        $folder = "bedah/progres-{$progres}";

        // Cek apakah file dengan nama yang sama sudah ada
        if (Storage::disk('public')->exists("{$folder}/{$filename}")) {
            // Tambahkan angka urut jika file sudah ada
            $counter = 2;
            while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                $filename = "{$house->id}_{$name}_{$counter}.{$extension}";
                $counter++;
            }
        }

        $manager = new ImageManager(Driver::class);

        $image = $manager->read($file)->scale(width: 600);

        $image->toWebp(75)->save(storage_path("app/public/{$folder}/{$filename}"));

        RenovatedHousePhoto::create([
            'renovated_house_id' => $house->id,
            'photo_url' => "{$folder}/{$filename}",
            'description' => $request->description,
            'progres' => $request->progres,
            'is_primary' => $request->boolean('is_primary'),
            'is_best' => $request->boolean('is_best'),
        ]);

        return redirect()->route('app.gallery.index', $house)->with('success', 'Photo uploaded successfully.');
    }

    public function destroy(House $house, RenovatedHousePhoto $photo)
    {
        abort_if(Gate::denies('renovated_house_photo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $photo->delete();
        return redirect()->route('app.gallery.index', $house)->with('success', 'Photo successfully removed.');
    }

    public function massDestroy(MassDestroyRenovatedHousePhotoRequest $request)
    {
        RenovatedHousePhoto::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function edit(House $house, RenovatedHousePhoto $photo)
    {
        abort_if(Gate::denies('renovated_house_photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('gallery.edit', compact('house', 'photo'));
    }

    public function update(Request $request, House $house, RenovatedHousePhoto $photo)
    {
        abort_if(Gate::denies('renovated_house_photo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // Jika ada file foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            Storage::disk('public')->delete($photo->photo_url);

            // Proses file baru
            $file = $request->file('photo');
            $progres = $request->progres;

            // Konversi nama file sesuai format
            $name = strtoupper(Str::slug($house->name, '-'));
            $extension = $file->getClientOriginalExtension();
            $filename = "{$house->id}_{$name}.{$extension}";

            // Folder berdasarkan progres
            $folder = "bedah/progres-{$progres}";

            // Cek apakah file dengan nama yang sama sudah ada
            if (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                // Tambahkan angka urut jika file sudah ada
                $counter = 2;
                while (Storage::disk('public')->exists("{$folder}/{$filename}")) {
                    $filename = "{$house->id}_{$name}_{$counter}.{$extension}";
                    $counter++;
                }
            }

            $manager = new ImageManager(Driver::class);

            $image = $manager->read($file)->scale(width: 600);


            $image->toWebp(75)->save(storage_path("app/public/{$folder}/{$filename}"));

            $photo->photo_url = "{$folder}/{$filename}";
        }

        // Update data lainnya
        $photo->update([
            'description' => $request->description,
            'progres' => $request->progres,
            'is_primary' => $request->boolean('is_primary'),
            'is_best' => $request->boolean('is_best'),
        ]);

        return redirect()->route('app.gallery.index', $house)->with('success', 'Photo updated successfully.');
    }
}
