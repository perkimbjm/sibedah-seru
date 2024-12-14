<?php

namespace App\Http\Controllers;

use App\Http\Requests\RenovatedHousePhotoControllerStoreRequest;
use App\Http\Requests\RenovatedHousePhotoControllerUpdateRequest;
use App\Models\RenovatedHousePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RenovatedHousePhotoController extends Controller
{
    public function index(Request $request): View
    {
        $renovatedHousePhotos = RenovatedHousePhoto::all();

        return view('renovatedHousePhoto.index', compact('renovatedHousePhotos'));
    }

    public function create(Request $request): Response
    {
        return view('renovatedHousePhoto.create');
    }

    public function store( RenovatedHousePhotoControllerStoreRequest $request): Response
    {
        $renovatedHousePhoto =  RenovatedHousePhoto::create($request->validated());

        $request->session()->flash('renovatedHousePhoto.id', $renovatedHousePhoto->id);

        return redirect()->route('renovatedHousePhotos.index');
    }

    public function show(Request $request,  RenovatedHousePhoto $renovatedHousePhoto): Response
    {
        return view('renovatedHousePhoto.show', compact('renovatedHousePhoto'));
    }

    public function edit(Request $request,  RenovatedHousePhoto $renovatedHousePhoto): Response
    {
        return view('renovatedHousePhoto.edit', compact('renovatedHousePhoto'));
    }

    public function update( RenovatedHousePhotoControllerUpdateRequest $request, HousePhoto $renovatedHousePhoto): Response
    {
        $renovatedHousePhoto->update($request->validated());

        $request->session()->flash('renovatedHousePhoto.id', $renovatedHousePhoto->id);

        return redirect()->route('renovatedHousePhotos.index');
    }

    public function destroy(Request $request,  RenovatedHousePhoto $renovatedHousePhoto): Response
    {
        $renovatedHousePhoto->delete();

        return redirect()->route('app.renovatedHousePhotos.index');
    }
}
