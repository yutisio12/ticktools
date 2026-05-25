<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Regular user who can create and track tickets',
                'permissions' => json_encode([
                    'tickets.create',
                    'tickets.view_own',
                    'tickets.reopen',
                ]),
            ],
            [
                'name' => 'IT Staff',
                'slug' => 'it-staff',
                'description' => 'IT support staff who handle tickets',
                'permissions' => json_encode([
                    'tickets.view_all',
                    'tickets.claim',
                    'tickets.update',
                    'tickets.set_priority',
                    'tickets.add_activity',
                    'tickets.resolve',
                ]),
            ],
            [
                'name' => 'IT Lead',
                'slug' => 'it-lead',
                'description' => 'IT team lead who reviews and approves tickets',
                'permissions' => json_encode([
                    'tickets.view_all',
                    'tickets.claim',
                    'tickets.update',
                    'tickets.set_priority',
                    'tickets.add_activity',
                    'tickets.resolve',
                    'tickets.review',
                    'tickets.approve',
                    'tickets.return',
                    'kpi.view',
                    'dashboard.lead',
                ]),
            ],
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'System administrator with full access',
                'permissions' => json_encode(['*']),
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
