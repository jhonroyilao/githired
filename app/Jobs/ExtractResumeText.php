<?php

namespace App\Jobs;

use App\Actions\Applicant\PrepareAiJobMatchAction;
use App\Models\ResumeDocument;
use App\Services\ResumeTextExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExtractResumeText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Try 3 times if something unexpected breaks
    public int $tries = 3;

    // Wait 30 seconds before trying again
    public int $backoff = 30;

    public function __construct(
        public readonly ResumeDocument $resume
    ) {}

    public function handle(ResumeTextExtractor $extractor, PrepareAiJobMatchAction $prepareAiJobMatch): void
    {
        try {
            // Attempt to grab the text from the PDF
            $result = $extractor->extract($this->resume);
        } catch (\DomainException $e) {
            // If the PDF is corrupted or unreadable, mark it failed immediately and stop
            $this->markFailed($e->getMessage());
            $this->prepareApplicationMatches($prepareAiJobMatch);

            return;
        }

        // Save the extracted text back to the database
        $this->resume->update([
            'extracted_text' => $result['text'],
            'content_hash' => $result['hash'],
            'extraction_status' => 'ready',
            'extraction_error' => null,
        ]);

        $this->prepareApplicationMatches($prepareAiJobMatch);
    }

    // Triggered by Laravel when all retry attempts run out
    public function failed(\Throwable $e): void
    {
        Log::error(
            "ExtractResumeText permanently failed for document #{$this->resume->id}: {$e->getMessage()}"
        );

        $this->markFailed('Job failed after maximum retries: '.$e->getMessage());
        $this->prepareApplicationMatches(app(PrepareAiJobMatchAction::class));
    }

    // Helper to cleanly update the database status when things go wrong
    private function markFailed(string $reason): void
    {
        $this->resume->update([
            'extraction_status' => 'failed',
            'extraction_error' => $reason,
        ]);
    }

    private function prepareApplicationMatches(PrepareAiJobMatchAction $prepareAiJobMatch): void
    {
        $this->resume->refresh();

        $this->resume->applications()
            ->with(['jobListing', 'user.profile'])
            ->get()
            ->each(function ($application) use ($prepareAiJobMatch): void {
                $prepareAiJobMatch->handle(
                    $application->user,
                    $application->jobListing,
                    $this->resume,
                );
            });
    }
}
