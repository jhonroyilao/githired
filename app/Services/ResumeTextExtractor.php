<?php

namespace App\Services;

use App\Models\ResumeDocument;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ResumeTextExtractor
{
    public function __construct(
        private readonly Parser $parser
    ) {}

    //Grab text and a fresh hash from the saved PDF
    public function extract(ResumeDocument $resume): array
    {
        $disk = Storage::disk(config('filesystems.resume_disk', 'local'));

        if (!$disk->exists($resume->file_path)) {
            throw new \Exception("Resume file not found on disk for document #{$resume->id}.");
        }

        //Read the file once to grab both the raw bytes and the hash
        $bytes = $disk->get($resume->file_path);
        $hash = hash('sha256', $bytes);

        try {
            $document = $this->parser->parseContent($bytes);
            $text = $document->getText();
        } catch (\Throwable $e) {
            // Catch any weird PDF parser crashes
            throw new \DomainException("PDF parser error: {$e->getMessage()}", 0, $e);
        }

        //Make sure the PDF was not just a giant image or locked behind a password
        if (trim($text) === '') {
            throw new \DomainException('No extractable text found. The PDF may be image-only or password-protected.');
        }

        return [
            'text' => $text,
            'hash' => $hash,
        ];
    }
}