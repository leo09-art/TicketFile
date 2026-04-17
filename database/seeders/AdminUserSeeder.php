<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
	public function run(): void
	{
		User::query()->updateOrCreate(
			['email' => 'admin@ticketfile.test'],
			[
				'name' => 'Administrateur',
				'password' => 'admin12345',
				'role' => User::ROLE_ADMIN,
				'email_verified_at' => now(),
			]
		);
	}
}

