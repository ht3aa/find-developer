<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserService;
use App\Enums\UserType;
use Illuminate\Database\Seeder;

class UserAppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing clients and developers
        $clients = User::where('user_type', UserType::CLIENT)->get();
        $developers = Developer::all();
        $services = UserService::all();

        if ($clients->isEmpty() || $developers->isEmpty()) {
            $this->command->warn('No clients or developers found. Please seed users and developers first.');
            return;
        }

        // Create appointments with realistic data
        $appointments = [
            // Pending appointments (no start_datetime)
            [
                'status' => AppointmentStatus::PENDING,
                'start_datetime' => null,
                'notes' => 'Initial consultation request. Waiting for confirmation.',
            ],
            [
                'status' => AppointmentStatus::PENDING,
                'start_datetime' => null,
                'notes' => 'Need to discuss project requirements.',
            ],
            [
                'status' => AppointmentStatus::PENDING,
                'start_datetime' => null,
                'notes' => null,
            ],

            // Confirmed appointments (with future start_datetime)
            [
                'status' => AppointmentStatus::CONFIRMED,
                'start_datetime' => now()->addDays(3)->setTime(10, 0),
                'notes' => 'Confirmed for technical consultation.',
            ],
            [
                'status' => AppointmentStatus::CONFIRMED,
                'start_datetime' => now()->addDays(7)->setTime(14, 30),
                'notes' => 'Code review session scheduled.',
            ],
            [
                'status' => AppointmentStatus::CONFIRMED,
                'start_datetime' => now()->addWeeks(2)->setTime(9, 0),
                'notes' => 'Architecture design meeting.',
            ],

            // Completed appointments (with past start_datetime)
            [
                'status' => AppointmentStatus::COMPLETED,
                'start_datetime' => now()->subDays(5)->setTime(11, 0),
                'notes' => 'Successfully completed. Great session!',
            ],
            [
                'status' => AppointmentStatus::COMPLETED,
                'start_datetime' => now()->subWeeks(2)->setTime(15, 0),
                'notes' => 'Project planning completed.',
            ],

            // Cancelled appointments
            [
                'status' => AppointmentStatus::CANCELLED,
                'start_datetime' => now()->subDays(2)->setTime(10, 0),
                'notes' => 'Cancelled due to scheduling conflict.',
            ],
            [
                'status' => AppointmentStatus::CANCELLED,
                'start_datetime' => null,
                'notes' => 'Cancelled by client request.',
            ],
        ];

        // Create appointments
        foreach ($appointments as $appointmentData) {
            $client = $clients->random();
            $developer = $developers->random();
            $service = $services->isNotEmpty() && fake()->boolean(70) 
                ? $services->random() 
                : null;

            UserAppointment::create([
                'user_id' => $client->id,
                'developer_id' => $developer->id,
                'user_service_id' => $service?->id,
                'status' => $appointmentData['status'],
                'start_datetime' => $appointmentData['start_datetime'],
                'notes' => $appointmentData['notes'],
            ]);
        }

        // Create additional random appointments using factory
        UserAppointment::factory(15)->create();
    }
}
