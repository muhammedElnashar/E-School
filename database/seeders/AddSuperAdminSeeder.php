<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\SessionYear;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AddSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $user = User::updateOrCreate(['id' => 1], [
            'name' => 'admin',
            'email' => 'superadmin@gmail.com',
            'user_code' => '12525',
            'role' => RoleEnum::Admin->value,
            'password' => Hash::make('superadmin'),
            'image' => 'logo.svg',
        ]);


    }
}
