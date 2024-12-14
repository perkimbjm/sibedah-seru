<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Inertia\Inertia;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\FaqStoreRequest;
use App\Http\Requests\FaqUpdateRequest;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        abort_if(Gate::denies('faq_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $faqs = Faq::all();

        return view('faq.index', compact('faqs'));
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('faq_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('faq.create');
    }

    public function store(FaqStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['order'] = Faq::max('id') + 1;
        $faq = Faq::create($validated);

        $request->session()->flash('faq.id', $faq->id);

        return redirect()->route('app.faqs.index');
    }

    public function show($id)
    {
        $faq = Faq::findOrFail($id);
        return view('faq.show', compact('faq'));
    }

    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('faq_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $faq = Faq::findOrFail($id);
        return view('faq.edit', compact('faq'));
    }

    public function update(FaqUpdateRequest $request, Faq $faq): Response
    {
        abort_if(Gate::denies('faq_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
   
        $faq->update($request->validated());        

        $request->session()->flash('faq.id', $faq->id);

        return redirect()->route('app.faqs.index')->with('success', 'FAQ berhasil diperbarui!');
    }

    public function destroy(Request $request, Faq $faq): Response
    {
        abort_if(Gate::denies('faq_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $faq->delete();

        return redirect()->route('app.faqs.index');
    }

    public function getData()
    {
        return Faq::orderBy('order', 'asc')->get();
    }
}
