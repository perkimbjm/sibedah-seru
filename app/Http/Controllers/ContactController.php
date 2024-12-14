<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactControllerStoreRequest;
use App\Http\Requests\ContactControllerUpdateRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $contacts = Contact::all();

        return view('contact.index', compact('contacts'));
    }

    public function create(Request $request): Response
    {
        return view('contact.create');
    }

    public function store(ContactControllerStoreRequest $request): Response
    {
        $contact = Contact::create($request->validated());

        $request->session()->flash('contact.id', $contact->id);

        return redirect()->route('contacts.index');
    }

    public function show(Request $request, Contact $contact): Response
    {
        return view('contact.show', compact('contact'));
    }

    public function edit(Request $request, Contact $contact): Response
    {
        return view('contact.edit', compact('contact'));
    }

    public function update(ContactControllerUpdateRequest $request, Contact $contact): Response
    {
        $contact->update($request->validated());

        $request->session()->flash('contact.id', $contact->id);

        return redirect()->route('contacts.index');
    }

    public function destroy(Request $request, Contact $contact): Response
    {
        $contact->delete();

        return redirect()->route('app.contacts.index');
    }
}
