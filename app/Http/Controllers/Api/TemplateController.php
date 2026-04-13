<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Template::latest()->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template = Template::create($data);

        return response()->json($template, 201);
    }

    public function show(Template $template): JsonResponse
    {
        return response()->json($template);
    }

    public function update(Request $request, Template $template): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $template->update($data);

        return response()->json($template);
    }

    public function destroy(Template $template): JsonResponse
    {
        $template->delete();
        return response()->json(null, 204);
    }
}
