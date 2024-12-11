<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkStoreRequest;
use App\Http\Requests\LinkUpdateRequest;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('link_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $links = Link::all();

        return view('link.index', compact('links'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('link_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('link.create');
    }

    public function store(LinkStoreRequest $request)
    {
        $validatedData = $request->validated();

        if (!filter_var($validatedData['url'], FILTER_VALIDATE_URL)) {
            return redirect()->back()->withErrors(['url' => 'URL tidak valid']);
        }

        $link = Link::create($validatedData);

        $request->session()->flash('link.id', $link->id);

        return redirect()->route('app.links.index');
    }


    public function show(Request $request, Link $link): Response
    {
        return view('link.show', compact('link'));
    }

    public function edit(Request $request, Link $link): Response
    {
        return view('link.edit', compact('link'));
    }

    public function update(LinkControllerUpdateRequest $request, Link $link): Response
    {
        $link->update($request->validated());

        $request->session()->flash('link.id', $link->id);

        return redirect()->route('links.index');
    }

    public function destroy(Request $request, Link $link): Response
    {
        $link->delete();

        return redirect()->route('app.links.index');
    }

    public function getData()
    {
        return Link::orderBy('id', 'asc')->get();
    }
}
