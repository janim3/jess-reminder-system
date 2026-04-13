<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Contact;
use App\Models\Template;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with(['contact', 'template'])->latest()->paginate(20);
        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $contacts  = Contact::orderBy('name')->get();
        $templates = Template::orderBy('name')->get();
        return view('assignments.create', compact('contacts', 'templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id'     => 'required|exists:contacts,id',
            'template_id'    => 'required|exists:templates,id',
            'frequency_type' => 'required|in:daily_once,daily_twice,daily_thrice,weekly',
            'send_times'     => 'required|array|min:1',
            'send_times.*'   => 'required|date_format:H:i',
            'channel'        => 'required|in:sms,email,both',
        ]);

        $data['send_times'] = array_values(array_unique($data['send_times']));

        Assignment::create($data);

        return redirect()->route('assignments.index')->with('success', 'Assignment created.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['contact', 'template']);
        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $contacts  = Contact::orderBy('name')->get();
        $templates = Template::orderBy('name')->get();
        return view('assignments.edit', compact('assignment', 'contacts', 'templates'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $data = $request->validate([
            'contact_id'     => 'required|exists:contacts,id',
            'template_id'    => 'required|exists:templates,id',
            'frequency_type' => 'required|in:daily_once,daily_twice,daily_thrice,weekly',
            'send_times'     => 'required|array|min:1',
            'send_times.*'   => 'required|date_format:H:i',
            'channel'        => 'required|in:sms,email,both',
        ]);

        $data['send_times'] = array_values(array_unique($data['send_times']));

        $assignment->update($data);

        return redirect()->route('assignments.index')->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success', 'Assignment deleted.');
    }
}
