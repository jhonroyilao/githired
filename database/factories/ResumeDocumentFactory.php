<?php

namespace Database\Factories;

use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResumeDocumentFactory extends Factory
{
    protected $model = ResumeDocument::class;

    //Default state for new uploaded PDF
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'file_path' => 'resumes/' . Str::uuid() . '.pdf',
            'original_name' => $this->faker->word() . '_resume.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(50_000, 4_000_000), //50KB - 4MB
            'extracted_text' => null,
            'content_hash' => Str::random(64),
            'extraction_status' => 'pending',
            'extraction_error' => null,
            'is_current' => true,
        ];
    }

    //Used in tests to generate old resumes
    public function notCurrent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => false,
        ]);
    }
}