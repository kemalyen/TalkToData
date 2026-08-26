<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => ConversationFactory::new(),
            'role' => Role::USER,
            'content' => fake()->sentence(),
            'chart_payload' => null,
        ];
    }
}
