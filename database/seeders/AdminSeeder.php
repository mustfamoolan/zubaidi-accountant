<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة المدير الرئيسي
        User::create([
            'name' => 'admin',
            'email' => 'admin@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // إضافة مدير ثاني
        User::create([
            'name' => 'مدير واحد',
            'email' => 'manager1@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // إضافة مدير ثالث
        User::create([
            'name' => 'مدير اثنين',
            'email' => 'manager2@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // إضافة زائر أول
        User::create([
            'name' => 'زائر واحد',
            'email' => 'visitor1@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('180370'),
            'role' => 'visitor',
        ]);

        // إضافة زائر ثاني
        User::create([
            'name' => 'زائر اثنين',
            'email' => 'visitor2@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('180370'),
            'role' => 'visitor',
        ]);

        // إضافة زائر ثالث
        User::create([
            'name' => 'زائر ثلاثة',
            'email' => 'visitor3@alzubaidi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('180370'),
            'role' => 'visitor',
        ]);
    }
}
