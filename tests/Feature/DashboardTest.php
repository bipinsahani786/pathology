<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users are redirected to their specific dashboard', function () {
    // Ensure the role exists
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    
    $user = User::factory()->create();
    $user->assignRole($role); 
    
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('lab.dashboard'));
});