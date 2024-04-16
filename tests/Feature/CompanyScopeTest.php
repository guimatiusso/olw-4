<?php

use App\Enums\RoleEnum;
use App\Models\Client;
use App\Models\Company;
use App\Models\Seller;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\RoleSeeder;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(AddressSeeder::class);
    seed(RoleSeeder::class);
});

test('seller user can only see clients on his tenant', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company1->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    $user2 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company2->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    Client::factory()
        ->count(10)
        ->create(['company_id' => $company1->id]);

    Client::factory()
        ->count(10)
        ->create(['company_id' => $company2->id]);

    $this->assertSame(20, Client::count());
    auth()->loginUsingId($user1->id);
    $this->assertSame(10, Client::count());
});

test('seller user can only see sellers on his tenant', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company1->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    $user2 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company2->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    Seller::factory()
        ->count(10)
        ->create(['company_id' => $company1->id]);

    Seller::factory()
        ->count(10)
        ->create(['company_id' => $company2->id]);

    $this->assertSame(22, Seller::count());
    auth()->loginUsingId($user1->id);
    $this->assertSame(11, Seller::count());
});

test('only allows admin users to impersonate users from the same company', function () {
    $company1 = Company::factory()->create();
    $company2 = Company::factory()->create();

    $user1 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company1->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    $admin = User::factory()
        ->create(['role_id' => RoleEnum::ADMIN]);

    $user2 = User::factory()
        ->has(Seller::factory()->state(['company_id' => $company2->id]))
        ->create(['role_id' => RoleEnum::SELLER]);

    // Simulate trying to impersonate a user from a different company
    $this->actingAs($user1)
        ->get('/impersonate/' . $user2->id . '/login')
        ->assertStatus(403); // 403 Forbidden

    $this->actingAs($user2)
        ->get('/impersonate/' . $user1->id . '/login')
        ->assertStatus(403);

    // Simulate admin user impersonating a user from the same company
    $response = $this->actingAs($admin)
        ->get('/impersonate/' . $user1->id . '/login')
        ->assertStatus(302); // 302 Redirect (to the home page or dashboard, for example)
});