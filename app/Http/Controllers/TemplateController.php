<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(20);
        return view('templates.index', compact('templates'));
    }

    public function create()
    {
        return view('templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Template::create($data);

        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function show(Template $template)
    {
        return view('templates.show', compact('template'));
    }

    public function edit(Template $template)
    {
        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update($data);

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(Template $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted.');
    }
}
