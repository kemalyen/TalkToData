<?php

use App\Models\User;

test('authenticated users get a successful response on home', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('home'));

    $response->assertOk();
});
