<?php

namespace Database\Seeders;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'superadmin@queuecare.local'], [
            'name' => 'Super Administrator',
            'password' => 'password123',
            'role' => User::ROLE_SUPER_ADMIN,
            'is_active' => true,
        ]);
        $admin = User::firstOrCreate(['email' => 'admin@queuecare.local'], [
            'name' => 'Administrator',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
        User::firstOrCreate(['email' => 'reception@queuecare.local'], [
            'name' => 'Receptionist',
            'password' => 'password123',
            'role' => User::ROLE_RECEPTIONIST,
            'is_active' => true,
        ]);

        $general = Service::firstOrCreate(['prefix' => 'G'], [
            'name' => 'General',
            'start_number' => 1,
            'is_active' => true,
        ]);
        $dental = Service::firstOrCreate(['prefix' => 'D'], [
            'name' => 'Dental',
            'start_number' => 1,
            'is_active' => true,
        ]);

        Doctor::firstOrCreate(['name' => 'Dr. Ahmed', 'service_id' => $general->id], [
            'specialization' => 'General Physician',
            'room_no' => '101',
            'is_active' => true,
        ]);
        Doctor::firstOrCreate(['name' => 'Dr. Sara', 'service_id' => $dental->id], [
            'specialization' => 'Dentist',
            'room_no' => '102',
            'is_active' => true,
        ]);

        $c1 = Counter::firstOrCreate(['name' => 'Counter-1'], [
            'room_no' => '101',
            'service_id' => $general->id,
            'is_active' => true,
        ]);
        Counter::firstOrCreate(['name' => 'Counter-2'], [
            'room_no' => '102',
            'service_id' => $dental->id,
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'operator@queuecare.local'], [
            'name' => 'Counter Operator',
            'password' => 'password123',
            'role' => User::ROLE_OPERATOR,
            'counter_id' => $c1->id,
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'staff@queuecare.local'], [
            'name' => 'Service Staff',
            'password' => 'password123',
            'role' => User::ROLE_STAFF,
            'service_id' => $general->id,
            'is_active' => true,
        ]);
        User::firstOrCreate(['email' => 'display@queuecare.local'], [
            'name' => 'Display Operator',
            'password' => 'password123',
            'role' => User::ROLE_DISPLAY_OPERATOR,
            'is_active' => true,
        ]);

        Setting::updateOrCreate(['key' => 'clinic_name'], ['value' => 'Queue-Pro Hospital']);
        Setting::updateOrCreate(['key' => 'clinic_address'], ['value' => 'Main Road']);
        Setting::updateOrCreate(['key' => 'token_footer'], ['value' => 'Please wait in waiting area']);
        Setting::updateOrCreate(['key' => 'display.refresh_secs'], ['value' => '4']);
        Setting::updateOrCreate(['key' => 'display.ticker'], ['value' => 'Please keep your token with you']);
        Setting::updateOrCreate(['key' => 'display.show_patient'], ['value' => '1']);
    }
}
