<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;

class KaizenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roles = [
            ['role_name' => 'admin', 'description' => 'ผู้ดูแลระบบ'],
            ['role_name' => 'manager', 'description' => 'หัวหน้าเห็นชอบ'],
            ['role_name' => 'chairman', 'description' => 'ประธานเห็นชอบ'],
            ['role_name' => 'user', 'description' => 'ผู้ใช้งานทั่วไป'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['role_name' => $role['role_name']], 
                ['description' => $role['description']]
            );
        }

        // 2. Create Users
        $adminRole = Role::where('role_name', 'admin')->first();
        $managerRole = Role::where('role_name', 'manager')->first();
        $chairmanRole = Role::where('role_name', 'chairman')->first();
        $userRole = Role::where('role_name', 'user')->first();

        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@kaizen.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('password'),
                'department' => 'IT',
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );

        // Manager User
        User::firstOrCreate(
            ['email' => 'manager@kaizen.com'],
            [
                'name' => 'Manager One',
                'password' => Hash::make('password'),
                'department' => 'Production',
                'role_id' => $managerRole->id,
                'status' => 'active',
            ]
        );

        // Chairman User
        User::firstOrCreate(
            ['email' => 'chairman@kaizen.com'],
            [
                'name' => 'Chairman One',
                'password' => Hash::make('password'),
                'department' => 'Executive',
                'role_id' => $chairmanRole->id,
                'status' => 'active',
            ]
        );

        // Regular User
        User::firstOrCreate(
            ['email' => 'user@kaizen.com'],
            [
                'name' => 'User One',
                'password' => Hash::make('password'),
                'department' => 'Production',
                'role_id' => $userRole->id,
                'status' => 'active',
            ]
        );

        // 3. Create Sample Project
        // $user = User::where('email', 'user@kaizen.com')->first();
        // \App\Models\KaizenProject::create([
        //     'fiscalyear' => date('Y') + 543,
        //     'title' => 'ลดขั้นตอนการเบิกจ่าย',
        //     'problem' => 'ขั้นตอนซับซ้อน ใช้เวลานาน',
        //     'improvement' => 'ใช้ระบบออนไลน์แทนกระดาษ',
        //     'result' => 'ลดเวลาได้ 50%',
        //     'user_id' => $user->id,
        //     'status' => 'pending',
        // ]);
    }
}
