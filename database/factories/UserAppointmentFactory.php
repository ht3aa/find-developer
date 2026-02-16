<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Enums\UserType;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAppointment>
 */
class UserAppointmentFactory extends Factory
{
    protected $model = UserAppointment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(AppointmentStatus::cases());

        // If status is pending, start_datetime should be null
        // Otherwise, set a future datetime
        $startDatetime = $status === AppointmentStatus::PENDING
            ? null
            : fake()->dateTimeBetween('now', '+3 months');

        return [
            'user_id' => User::factory()->state([
                'user_type' => UserType::DEVELOPER,
            ]),
            'developer_id' => Developer::factory(),
            'user_service_id' => fake()->boolean(60) ? UserService::factory() : null,
            'status' => $status,
            'start_datetime' => $startDatetime,
            'notes' => fake()->boolean(40) ? fake()->paragraph(2) : null,
        ];
    }

    /**
     * Indicate that the appointment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::PENDING,
            'start_datetime' => null,
        ]);
    }

    /**
     * Indicate that the appointment is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::CONFIRMED,
            'start_datetime' => fake()->dateTimeBetween('now', '+2 months'),
        ]);
    }

    /**
     * Indicate that the appointment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::COMPLETED,
            'start_datetime' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the appointment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::CANCELLED,
            'start_datetime' => fake()->boolean(50) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ]);
    }

    /**
     * Indicate that the appointment has a specific start datetime.
     */
    public function withStartDatetime(\DateTime $datetime): static
    {
        return $this->state(fn (array $attributes) => [
            'start_datetime' => $datetime,
            'status' => AppointmentStatus::CONFIRMED,
        ]);
    }
}
