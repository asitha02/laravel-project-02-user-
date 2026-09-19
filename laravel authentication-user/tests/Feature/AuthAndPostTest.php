<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_for_guests()
    {
        $this->get('/')->assertOk()->assertSee('Register')->assertSee('Login');
    }

    public function test_user_can_register_and_see_logged_in_home()
    {
        $this->post('/register', [
            'name' => 'asitha',
            'email' => 'asitha@example.com',
            'password' => 'password1',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->get('/')->assertOk()->assertSee('Congrats you are logged in.');
    }

    public function test_user_can_login_and_logout()
    {
        $user = User::factory()->create([
            'name' => 'asitha',
            'password' => bcrypt('password1'),
        ]);

        $this->post('/login', [
            'loginname' => 'asitha',
            'loginpassword' => 'password1',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_invalid_login_shows_error()
    {
        $this->from('/')->post('/login', [
            'loginname' => 'nobody',
            'loginpassword' => 'wrongpass',
        ])->assertRedirect('/')->assertSessionHasErrors('loginname');

        $this->assertGuest();
    }

    public function test_guests_cannot_create_or_edit_posts()
    {
        $owner = User::factory()->create(['name' => 'owner']);
        $post = Post::create([
            'title' => 'Secret',
            'body' => 'Hidden body',
            'user_id' => $owner->id,
        ]);

        $this->post('/create-post', ['title' => 'Nope', 'body' => 'Nope'])->assertRedirect('/');
        $this->get('/edit-post/'.$post->id)->assertRedirect('/');
        $this->put('/edit-post/'.$post->id, ['title' => 'Hacked', 'body' => 'Hacked'])->assertRedirect('/');
        $this->delete('/delete-post/'.$post->id)->assertRedirect('/');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Secret']);
    }

    public function test_user_can_create_update_and_delete_own_post()
    {
        $user = User::factory()->create(['name' => 'writer']);

        $this->actingAs($user)->post('/create-post', [
            'title' => 'Hello',
            'body' => 'World',
        ])->assertRedirect('/');

        $post = Post::first();
        $this->assertNotNull($post);
        $this->assertSame($user->id, $post->user_id);

        $this->actingAs($user)->get('/edit-post/'.$post->id)
            ->assertOk()
            ->assertSee('Edit Post')
            ->assertSee('Hello');

        $this->actingAs($user)->put('/edit-post/'.$post->id, [
            'title' => 'Updated',
            'body' => 'New body',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Updated', 'body' => 'New body']);

        $this->actingAs($user)->delete('/delete-post/'.$post->id)->assertRedirect('/');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_edit_someone_elses_post()
    {
        $owner = User::factory()->create(['name' => 'owner']);
        $other = User::factory()->create(['name' => 'other']);
        $post = Post::create([
            'title' => 'Mine',
            'body' => 'Keep out',
            'user_id' => $owner->id,
        ]);

        $this->actingAs($other)->get('/edit-post/'.$post->id)->assertRedirect('/');
        $this->actingAs($other)->put('/edit-post/'.$post->id, [
            'title' => 'Stolen',
            'body' => 'Stolen',
        ])->assertRedirect('/');
        $this->actingAs($other)->delete('/delete-post/'.$post->id)->assertRedirect('/');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Mine']);
    }
}
