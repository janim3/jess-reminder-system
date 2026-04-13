<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Assignment::with(['contact', 'template'])->latest()->paginate(20)
        );
    }

    public function store(Request $request): JsonResponse
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

        $assignment = Assignment::create($data);

        return response()->json($assignment->load(['contact', 'template']), 201);
    }

    public function show(Assignment $assignment): JsonResponse
    {
        return response()->json($assignment->load(['contact', 'template']));
    }

    public function update(Request $request, Assignment $assignment): JsonResponse
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

        return response()->json($assignment->load(['contact', 'template']));
    }

    public function destroy(Assignment $assignment): JsonResponse
    {
        $assignment->delete();
        return response()->json(null, 204);
    }
}
