<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(20);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20|unique:contacts',
            'email'         => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        Contact::create($data);

        return redirect()->route('contacts.index')->with('success', 'Contact created.');
    }

    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20|unique:contacts,phone_number,' . $contact->id,
            'email'         => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    }
}
