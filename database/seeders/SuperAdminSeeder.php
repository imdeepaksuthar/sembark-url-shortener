<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('superadmin.email');
        $password = config('superadmin.password');

        if (!$email || !$password) {
            throw new RuntimeException(
                'Configure SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD.'
            );
        }

        // Make the seeder safe to run more than once.
        if (DB::table('users')->where('email', $email)->exists()) {
            return;
        }

        // The account is inserted using parameterized raw SQL.
        DB::insert(
            'INSERT INTO users
                (name, email, password, role, company_id, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                $email,
                Hash::make($password),
                'super_admin',
                null,
                now(),
                now(),
            ]
        );
    }
}
