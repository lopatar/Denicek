<?php

test('unauthenticated returns redirect', function () {
    $this->get('/')->assertRedirect('/login');
});

test('login returns success', function () {
    $this->get('/login')->assertStatus(200);
});

test('register returns success', function() {
    $this->get('/register')->assertStatus(200);
});