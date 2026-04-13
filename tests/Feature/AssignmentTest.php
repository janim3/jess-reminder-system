<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Contact;
use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_list_page_loads(): void
    {
        $response = $this->get(route('assignments.index'));
        $response->assertStatus(200);
        $response->assertViewIs('assignments.index');
    }

    public function test_can_create_assignment(): void
    {
        $contact  = Contact::factory()->create();
        $template = Template::factory()->create();

        $response = $this->post(route('assignments.store'), [
            'contact_id'     => $contact->id,
            'template_id'    => $template->id,
            'frequency_type' => 'daily_once',
            'send_times'     => ['09:00'],
            'channel'        => 'sms',
        ]);

        $response->assertRedirect(route('assignments.index'));
        $this->assertDatabaseHas('assignments', [
            'contact_id'     => $contact->id,
            'template_id'    => $template->id,
            'frequency_type' => 'daily_once',
            'channel'        => 'sms',
        ]);
    }

    public function test_assignment_creation_requires_all_fields(): void
    {
        $response = $this->post(route('assignments.store'), []);
        $response->assertSessionHasErrors(['contact_id', 'template_id', 'frequency_type', 'send_times', 'channel']);
    }

    public function test_can_delete_assignment(): void
    {
        $assignment = Assignment::factory()->create();

        $response = $this->delete(route('assignments.destroy', $assignment));

        $response->assertRedirect(route('assignments.index'));
        $this->assertDatabaseMissing('assignments', ['id' => $assignment->id]);
    }

    public function test_deleting_contact_cascades_to_assignments(): void
    {
        $assignment = Assignment::factory()->create();
        $contactId  = $assignment->contact_id;

        $assignment->contact->delete();

        $this->assertDatabaseMissing('assignments', ['contact_id' => $contactId]);
    }
}
