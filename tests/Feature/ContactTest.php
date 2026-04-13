<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_list_page_loads(): void
    {
        $response = $this->get(route('contacts.index'));
        $response->assertStatus(200);
        $response->assertViewIs('contacts.index');
    }

    public function test_can_create_contact(): void
    {
        $response = $this->post(route('contacts.store'), [
            'name'         => 'Jess Smith',
            'phone_number' => '+1234567890',
            'email'        => 'jess@example.com',
        ]);

        $response->assertRedirect(route('contacts.index'));
        $this->assertDatabaseHas('contacts', [
            'name'         => 'Jess Smith',
            'phone_number' => '+1234567890',
        ]);
    }

    public function test_contact_creation_requires_name_and_phone(): void
    {
        $response = $this->post(route('contacts.store'), []);
        $response->assertSessionHasErrors(['name', 'phone_number']);
    }

    public function test_phone_number_must_be_unique(): void
    {
        Contact::factory()->create(['phone_number' => '+1234567890']);

        $response = $this->post(route('contacts.store'), [
            'name'         => 'Another Person',
            'phone_number' => '+1234567890',
        ]);

        $response->assertSessionHasErrors(['phone_number']);
    }

    public function test_can_update_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->put(route('contacts.update', $contact), [
            'name'         => 'Updated Name',
            'phone_number' => $contact->phone_number,
        ]);

        $response->assertRedirect(route('contacts.index'));
        $this->assertDatabaseHas('contacts', ['name' => 'Updated Name']);
    }

    public function test_can_delete_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('contacts.index'));
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
