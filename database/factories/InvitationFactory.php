<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'invited_email' => fake()->unique()->safeEmail(),
            'invited_by_user_id' => User::factory(),
            'invite_token' => Invitation::generateToken(),
            'invite_token_expires_at' => now()->addDays(7),
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the invitation has been accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the invitation token is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'invite_token_expires_at' => now()->subDay(),
        ]);
    }
}
