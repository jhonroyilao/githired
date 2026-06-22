<?php

namespace Database\Factories;

use App\Models\ResumeDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ResumeDocument>
 */
class ResumeDocumentFactory extends Factory
{
    protected $model = ResumeDocument::class;

    /**
     * Default state — simulates a freshly uploaded, current PDF resume.
     */
    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'file_path'         => 'resumes/' . Str::uuid() . '.pdf',
            'original_name'     => $this->faker->word() . '_resume.pdf',
            'mime_type'         => 'application/pdf',
            'file_size'         => $this->faker->numberBetween(50_000, 4_000_000), // 50 KB – 4 MB
            'extracted_text'    => null,
            'content_hash'      => Str::random(64),
            'extraction_status' => 'pending',
            'extraction_error'  => null,
            'is_current'        => true,
        ];
    }

    /**
     * Mark this resume as a superseded (non-current) upload.
     * Used to seed resume history records in tests.
     */
    public function notCurrent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => false,
        ]);
    }
}