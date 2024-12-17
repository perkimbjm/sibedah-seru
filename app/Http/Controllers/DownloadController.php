<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Download;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DownloadStoreRequest;
use App\Http\Requests\DownloadUpdateRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyDownloadRequest;

class DownloadController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('download_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $downloads = Download::all();

        return view('download.index', compact('downloads'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('download_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('download.create');
    }

    public function store(DownloadStoreRequest $request)
    {

        $requestData = $request->all();

        $download = Download::create($requestData);

        $request->session()->flash('download.id', $download->id);

        return redirect()->route('app.downloads.index');
    }

    public function show($id)
    {
        $download = Download::findOrFail($id);
        return view('download.show', compact('download'));
    }

    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('download_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $download = Download::findOrFail($id);
        return view('download.edit', compact('download'));
    }

    public function update(DownloadUpdateRequest $request, download $download): Response
    {
        abort_if(Gate::denies('download_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
   
        $download->update($request->validated());        

        $request->session()->flash('download.id', $download->id);

        return redirect()->route('app.downloads.index')->with('success', 'download berhasil diperbarui!');
    }

    public function destroy(Request $request, Download $download): Response
    {
        $download->delete();

        return redirect()->route('app.downloads.index');
    }

    public function massDestroy(MassDestroyDownloadRequest $request)
    {
        Download::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getData()
    {
        return Download::orderBy('id', 'asc')->get();
    }


}
