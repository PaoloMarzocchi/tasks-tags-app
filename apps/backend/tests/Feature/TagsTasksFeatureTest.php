<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TagsTasksFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(): array
    {
        $user = User::factory()->create([
            'email' => 'dev@local',
            'password' => bcrypt('secret1234'),
        ]);
        $login = $this->postJson('/api/auth/login', [
            'email' => 'dev@local',
            'password' => 'secret1234',
        ])->json();
        return ['Authorization' => 'Bearer ' . $login['access_token']];
    }

    public function test_seeder_creates_work_tag(): void
    {
        $this->seed();
        $this->assertTrue(Tag::where('slug', 'work')->exists());
    }

    public function test_create_tag_and_task_crud_and_filters(): void
    {
        $headers = $this->authHeaders();

        // Create parent tag Work (from seeder)
        $this->seed();
        $this->assertTrue(Tag::where('slug', 'work')->exists());

        // Create child tag
        $tagPayload = [
            'name' => 'Backend',
            'slug' => 'backend',
            'color' => '#333333',
            'parent_slug' => 'work'
        ];
        $resTag = $this->withHeaders($headers)->postJson('/api/tags', $tagPayload);
        $resTag->assertStatus(201)->assertJsonFragment([
            'name' => 'Backend',
            'slug' => 'backend',
            'parent_slug' => 'work',
        ]);

        // Create task with tag
        $taskPayload = [
            'title' => 'Implement API',
            'description' => 'Write endpoints',
            'status' => 'todo',
            'assignee' => 'Alice',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'tags' => ['backend']
        ];
        $resTask = $this->withHeaders($headers)->postJson('/api/tasks', $taskPayload);
        $resTask->assertStatus(201)->assertJsonFragment([
            'title' => 'Implement API',
            'status' => 'todo',
        ])->assertJsonFragment(['slug' => 'backend']);
        $taskId = $resTask->json('id') ?? $resTask->json('data.id');

        // Filter by q
        $this->withHeaders($headers)->getJson('/api/tasks?q=API')
            ->assertStatus(200)
            ->assertJson(fn($json) => $json->has(0));

        // Filter by status
        $this->withHeaders($headers)->getJson('/api/tasks?status=todo')
            ->assertStatus(200)
            ->assertJson(fn($json) => $json->has(0));

        // Filter by tag slug
        $this->withHeaders($headers)->getJson('/api/tasks?tag=backend')
            ->assertStatus(200)
            ->assertJson(fn($json) => $json->has(0));

        // Update status
        $this->withHeaders($headers)->patchJson('/api/tasks/' . $taskId, ['status' => 'doing'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'doing']);

        // Delete task
        $this->withHeaders($headers)->deleteJson('/api/tasks/' . $taskId)
            ->assertStatus(204);

        // Auth required checks
        $this->postJson('/api/tags', $tagPayload)->assertStatus(401);
        $this->postJson('/api/tasks', $taskPayload)->assertStatus(401);
    }

    public function test_parent_slug_update_and_render(): void
    {
        $headers = $this->authHeaders();
        $this->seed();

        // Create two tags
        $this->withHeaders($headers)->postJson('/api/tags', [
            'name' => 'UI',
            'slug' => 'ui'
        ])->assertStatus(201);

        $res = $this->withHeaders($headers)->postJson('/api/tags', [
            'name' => 'Components',
            'slug' => 'components',
            'parent_slug' => 'ui'
        ])->assertStatus(201)->assertJsonFragment(['parent_slug' => 'ui']);

        $id = $res->json('id') ?? $res->json('data.id');

        // Change parent
        $this->withHeaders($headers)->patchJson('/api/tags/' . $id, ['parent_slug' => 'work'])
            ->assertStatus(200)->assertJsonFragment(['parent_slug' => 'work']);

        // Remove parent
        $this->withHeaders($headers)->patchJson('/api/tags/' . $id, ['parent_slug' => null])
            ->assertStatus(200)->assertJsonFragment(['parent_slug' => null]);
    }
}
