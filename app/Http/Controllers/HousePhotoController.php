<?php

namespace App\Http\Controllers;

use App\Http\Requests\HousePhotoControllerStoreRequest;
use App\Http\Requests\HousePhotoControllerUpdateRequest;
use App\Models\HousePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HousePhotoController extends Controller
{
    public function index(Request $request): View
    {
        $housePhotos = HousePhoto::all();

        return view('housePhoto.index', compact('housePhotos'));
    }

    public function create(Request $request): Response
    {
        return view('housePhoto.create');
    }

    public function store(HousePhotoControllerStoreRequest $request): Response
    {
        $housePhoto = HousePhoto::create($request->validated());

        $request->session()->flash('housePhoto.id', $housePhoto->id);

        return redirect()->route('housePhotos.index');
    }

    public function show(Request $request, HousePhoto $housePhoto): Response
    {
        return view('housePhoto.show', compact('housePhoto'));
    }

    public function edit(Request $request, HousePhoto $housePhoto): Response
    {
        return view('housePhoto.edit', compact('housePhoto'));
    }

    public function update(HousePhotoControllerUpdateRequest $request, HousePhoto $housePhoto): Response
    {
        $housePhoto->update($request->validated());

        $request->session()->flash('housePhoto.id', $housePhoto->id);

        return redirect()->route('housePhotos.index');
    }

    public function destroy(Request $request, HousePhoto $housePhoto): Response
    {
        $housePhoto->delete();

        return redirect()->route('housePhotos.index');
    }
}
