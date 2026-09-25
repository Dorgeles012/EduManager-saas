<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

beforeEach(function () {
    config(['session.driver' => 'database']);
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    // Factory par défaut crée un rôle SADMIN.
    $response->assertRedirect(route('sadmin.dashboard', absolute: false));

});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('different users can authenticate in independent sessions', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $this->post('/login', [
        'email' => $userA->email,
        'password' => 'password',
    ])->assertRedirect();

    app('session')->flush();
    Auth::forgetGuards();
    $this->withCookie(config('session.cookie'), Str::random(40))
        ->post('/login', [
            'email' => $userB->email,
            'password' => 'password',
        ])
        ->assertRedirect();

    expect(DB::table('sessions')->whereIn('user_id', [$userA->id, $userB->id])->count())->toBe(2);
});

test('a user cannot create a second active session and the original remains active', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect();

    $originalSessionId = DB::table('sessions')->where('user_id', $user->id)->value('id');

    app('session')->flush();
    Auth::forgetGuards();
    $this->withCookie(config('session.cookie'), Str::random(40))
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertSessionHasErrors(['email']);

    expect(DB::table('sessions')->where('user_id', $user->id)->pluck('id')->all())
        ->toContain($originalSessionId)
        ->toHaveCount(1);
});

test('a user can log in again after logging out', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect();

    $this->post('/logout')->assertRedirect('/');

    $this->withCookie(config('session.cookie'), Str::random(40))
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect();

    expect(DB::table('sessions')->where('user_id', $user->id)->count())->toBe(1);
});

test('an expired session does not block a new login', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect();

    DB::table('sessions')
        ->where('user_id', $user->id)
        ->update(['last_activity' => now()->subMinutes(config('session.lifetime') + 1)->timestamp]);

    app('session')->flush();
    Auth::forgetGuards();
    $this->withCookie(config('session.cookie'), Str::random(40))
        ->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect();

    expect(DB::table('sessions')->where('user_id', $user->id)->count())->toBe(1);
});
