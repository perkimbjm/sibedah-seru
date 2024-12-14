<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentControllerStoreRequest;
use App\Http\Requests\DocumentControllerUpdateRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = Document::all();

        return view('document.index', compact('documents'));
    }

    public function create(Request $request): Response
    {
        return view('document.create');
    }

    public function store(DocumentControllerStoreRequest $request): Response
    {
        $document = Document::create($request->validated());

        $request->session()->flash('document.id', $document->id);

        return redirect()->route('documents.index');
    }

    public function show(Request $request, Document $document): Response
    {
        return view('document.show', compact('document'));
    }

    public function edit(Request $request, Document $document): Response
    {
        return view('document.edit', compact('document'));
    }

    public function update(DocumentControllerUpdateRequest $request, Document $document): Response
    {
        $document->update($request->validated());

        $request->session()->flash('document.id', $document->id);

        return redirect()->route('documents.index');
    }

    public function destroy(Request $request, Document $document): Response
    {
        $document->delete();

        return redirect()->route('app.documents.index');
    }
}
