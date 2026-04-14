<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $data = $this->validatedAssignmentData($request);

        $assignment = Assignment::create($data);

        return response()->json($assignment->load(['contact', 'template']), 201);
    }

    public function show(Assignment $assignment): JsonResponse
    {
        return response()->json($assignment->load(['contact', 'template']));
    }

    public function update(Request $request, Assignment $assignment): JsonResponse
    {
        $data = $this->validatedAssignmentData($request);

        $assignment->update($data);

        return response()->json($assignment->load(['contact', 'template']));
    }

    public function destroy(Assignment $assignment): JsonResponse
    {
        $assignment->delete();
        return response()->json(null, 204);
    }

    private function validatedAssignmentData(Request $request): array
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'template_id' => 'required|exists:templates,id',
            'schedule_mode' => ['nullable', Rule::in(['simple', 'advanced'])],
            'frequency_type' => ['nullable', 'required_unless:schedule_mode,advanced', Rule::in(['daily_once', 'daily_twice', 'daily_thrice', 'weekly'])],
            'send_times' => 'required|array|min:1',
            'send_times.*' => 'required|date_format:H:i',
            'channel' => 'required|in:sms,email,both',
            'recurrence_unit' => ['nullable', Rule::in(['seconds', 'minutes', 'hours', 'daily', 'weekly', 'monthly', 'yearly'])],
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_days_of_week' => 'nullable|array|min:1',
            'recurrence_days_of_week.*' => 'integer|between:1,7',
            'recurrence_day_of_month' => 'nullable|integer|between:1,31',
            'recurrence_month_of_year' => 'nullable|integer|between:1,12',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'timezone' => 'nullable|timezone',
        ]);

        $isAdvanced = ($data['schedule_mode'] ?? 'simple') === 'advanced';
        $data['send_times'] = array_values(array_unique($data['send_times']));

        if (!$isAdvanced) {
            $data['is_advanced'] = false;
            $data['frequency_type'] = $data['frequency_type'] ?? 'daily_once';
            $data['recurrence_rule'] = null;
            $data['start_date'] = null;
            $data['end_date'] = null;
            $data['timezone'] = null;

            return $this->cleanAssignmentData($data);
        }

        $request->validate([
            'recurrence_unit' => ['required', Rule::in(['seconds', 'minutes', 'hours', 'daily', 'weekly', 'monthly', 'yearly'])],
            'recurrence_interval' => 'required|integer|min:1|max:365',
            'start_date' => 'required|date',
            'timezone' => 'required|timezone',
            'recurrence_days_of_week' => [
                Rule::requiredIf(fn () => $request->input('recurrence_unit') === 'weekly'),
                'array',
                'min:1',
            ],
            'recurrence_day_of_month' => [
                Rule::requiredIf(fn () => in_array($request->input('recurrence_unit'), ['monthly', 'yearly'], true)),
                'integer',
                'between:1,31',
            ],
            'recurrence_month_of_year' => [
                Rule::requiredIf(fn () => $request->input('recurrence_unit') === 'yearly'),
                'integer',
                'between:1,12',
            ],
        ]);

        $data['is_advanced'] = true;
        $data['frequency_type'] = 'daily_once';
        $data['recurrence_rule'] = [
            'unit' => $data['recurrence_unit'],
            'interval' => (int) $data['recurrence_interval'],
            'days_of_week' => array_values(array_unique(array_map('intval', $data['recurrence_days_of_week'] ?? []))),
            'day_of_month' => isset($data['recurrence_day_of_month']) ? (int) $data['recurrence_day_of_month'] : null,
            'month_of_year' => isset($data['recurrence_month_of_year']) ? (int) $data['recurrence_month_of_year'] : null,
        ];

        return $this->cleanAssignmentData($data);
    }

    private function cleanAssignmentData(array $data): array
    {
        unset(
            $data['schedule_mode'],
            $data['recurrence_unit'],
            $data['recurrence_interval'],
            $data['recurrence_days_of_week'],
            $data['recurrence_day_of_month'],
            $data['recurrence_month_of_year']
        );

        return $data;
    }
}
