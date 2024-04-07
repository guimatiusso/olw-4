<?php

use App\Enums\RoleEnum;
use App\Models\Company;
use App\Models\Seller;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\RoleSeeder;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RoleSeeder::class);
    seed(CompanySeeder::class);
});

test('confirm password screen can be rendered', function () {
    $user = User::factory()
        ->state(['role_id' => RoleEnum::SELLER])
        ->has(Seller::factory()->state(['company_id' => Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->get('/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()
        ->state(['role_id' => RoleEnum::SELLER])
        ->has(Seller::factory()->state(['company_id' => Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'password',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()
        ->state(['role_id' => RoleEnum::SELLER])
        ->has(Seller::factory()->state(['company_id' => Company::first()->id]))
        ->create();

    $response = $this->actingAs($user)->post('/confirm-password', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors();
});
