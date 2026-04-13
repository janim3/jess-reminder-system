<?php

namespace Tests\Feature;

use App\Models\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_list_page_loads(): void
    {
        $response = $this->get(route('templates.index'));
        $response->assertStatus(200);
        $response->assertViewIs('templates.index');
    }

    public function test_can_create_template(): void
    {
        $response = $this->post(route('templates.store'), [
            'name'    => 'Birthday Wish',
            'content' => 'Happy birthday, {name}!',
        ]);

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseHas('templates', [
            'name'    => 'Birthday Wish',
            'content' => 'Happy birthday, {name}!',
        ]);
    }

    public function test_template_creation_requires_name_and_content(): void
    {
        $response = $this->post(route('templates.store'), []);
        $response->assertSessionHasErrors(['name', 'content']);
    }

    public function test_can_update_template(): void
    {
        $template = Template::factory()->create();

        $response = $this->put(route('templates.update', $template), [
            'name'    => 'Updated Name',
            'content' => 'Hello {name}, this is updated.',
        ]);

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseHas('templates', ['name' => 'Updated Name']);
    }

    public function test_can_delete_template(): void
    {
        $template = Template::factory()->create();

        $response = $this->delete(route('templates.destroy', $template));

        $response->assertRedirect(route('templates.index'));
        $this->assertDatabaseMissing('templates', ['id' => $template->id]);
    }

    public function test_parse_content_replaces_name_variable(): void
    {
        $template = new Template(['content' => 'Hello, {name}!']);
        $contact  = new \App\Models\Contact(['name' => 'Jess']);

        $this->assertSame('Hello, Jess!', $template->parseContent($contact));
    }
}
