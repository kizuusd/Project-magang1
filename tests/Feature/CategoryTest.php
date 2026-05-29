<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_category_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Uang Jajan',
            'type' => 'expense',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Uang Jajan',
            'type' => 'expense',
        ]);
    }

    public function test_create_category_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '',
            'type' => 'expense',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_cannot_edit_other_users_category(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $categoryOfUser2 = Category::create([
            'user_id' => $user2->id,
            'name' => 'Rahasia',
            'type' => 'expense',
        ]);

        $response = $this->actingAs($user1)->get(route('categories.edit', $categoryOfUser2));

        $response->assertStatus(403);
    }
}
