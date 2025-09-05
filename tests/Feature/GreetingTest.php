<?php

namespace Tests\Feature;

use App\Models\Greeting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GreetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_loads(): void
    {
        $response = $this->get('/greetings');
        $response->assertStatus(200)->assertSee('Gửi Lời Chúc');
    }

    public function test_can_post_root_greeting(): void
    {
        $response = $this->post('/greetings', [
            'name' => 'Tester',
            'message' => 'Xin chào và chúc mừng!',
        ]);

        $response->assertRedirect(route('greetings.index'));
        $this->assertDatabaseHas('greetings', [
            'name' => 'Tester',
            'message' => 'Xin chào và chúc mừng!',
            'parent_id' => null,
        ]);
    }

    public function test_can_reply_nested(): void
    {
        $parent = Greeting::factory()->create();

        $response = $this->post('/greetings', [
            'name' => 'Child',
            'message' => 'Reply here',
            'parent_id' => $parent->id,
        ]);

        $response->assertRedirect(route('greetings.index'));
        $this->assertDatabaseHas('greetings', [
            'name' => 'Child',
            'message' => 'Reply here',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_youtube_link_is_embedded(): void
    {
        $g = Greeting::factory()->create([
            'name' => 'Video User',
            'message' => 'Xem video này: https://youtu.be/dQw4w9WgXcQ',
        ]);

        $response = $this->get('/greetings');
        $response->assertStatus(200)
            ->assertSee('iframe', false)
            ->assertSee('dQw4w9WgXcQ', false);
    }
}

