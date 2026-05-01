<?php

test('guests are redirected to sign in from dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect('/signin');
});

test('the sign in page returns a successful response', function () {
    $response = $this->get('/signin');

    $response->assertStatus(200);
});
