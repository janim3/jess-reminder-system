<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Contact::latest()->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20|unique:contacts',
            'email'         => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $contact = Contact::create($data);

        return response()->json($contact, 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        return response()->json($contact);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20|unique:contacts,phone_number,' . $contact->id,
            'email'         => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
        ]);

        $contact->update($data);

        return response()->json($contact);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        return response()->json(null, 204);
    }
}
