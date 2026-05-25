<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $itLeadRole = Role::where('slug', 'it-lead')->first();
        $itStaffRole = Role::where('slug', 'it-staff')->first();
        $userRole = Role::where('slug', 'user')->first();

        // Administrator
        User::updateOrCreate(
            ['badge_id' => 'ADM001'],
            [
                'name' => 'Administrator',
                'email' => 'admin@company.com',
                'date_of_birth' => '1990-01-01',
                'role_id' => $adminRole->id,
                'department' => 'IT',
                'position' => 'System Administrator',
                'is_active' => true,
                'password' => bcrypt('admin123'),
            ]
        );

        // IT Lead
        User::updateOrCreate(
            ['badge_id' => 'ITL001'],
            [
                'name' => 'IT Lead',
                'email' => 'itlead@company.com',
                'date_of_birth' => '1988-05-15',
                'role_id' => $itLeadRole->id,
                'department' => 'IT',
                'position' => 'IT Team Lead',
                'is_active' => true,
                'password' => bcrypt('itlead123'),
            ]
        );

        // IT Staff
        User::updateOrCreate(
            ['badge_id' => 'ITS001'],
            [
                'name' => 'IT Support 1',
                'email' => 'itsupport1@company.com',
                'date_of_birth' => '1992-03-20',
                'role_id' => $itStaffRole->id,
                'department' => 'IT',
                'position' => 'IT Support',
                'is_active' => true,
                'password' => bcrypt('itstaff123'),
            ]
        );

        User::updateOrCreate(
            ['badge_id' => 'ITS002'],
            [
                'name' => 'IT Support 2',
                'email' => 'itsupport2@company.com',
                'date_of_birth' => '1993-07-10',
                'role_id' => $itStaffRole->id,
                'department' => 'IT',
                'position' => 'IT Support',
                'is_active' => true,
                'password' => bcrypt('itstaff123'),
            ]
        );

        // Regular Users
        User::updateOrCreate(
            ['badge_id' => 'USR001'],
            [
                'name' => 'John Doe',
                'email' => 'john@company.com',
                'date_of_birth' => '1995-11-25',
                'role_id' => $userRole->id,
                'department' => 'Finance',
                'position' => 'Accountant',
                'is_active' => true,
                'password' => bcrypt('user123'),
            ]
        );

        User::updateOrCreate(
            ['badge_id' => 'USR002'],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@company.com',
                'date_of_birth' => '1994-08-30',
                'role_id' => $userRole->id,
                'department' => 'HR',
                'position' => 'HR Staff',
                'is_active' => true,
                'password' => bcrypt('user123'),
            ]
        );
    }
}
