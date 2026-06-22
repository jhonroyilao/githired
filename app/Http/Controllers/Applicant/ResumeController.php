<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreResumeRequest;
use App\Models\Profile;
use App\Models\ResumeDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeController extends Controller
{
    private const DISK = 'local';

    public function index(): View
    {
        $user = request()->user();

        //Show active and old resumes
        $resumeHistory = $user->resumeDocuments()->where('is_current', false)->latest()->get();

        return view('applicant.resume.view', [
            'currentResume' => $user->currentResumeDocument,
            'resumeHistory' => $resumeHistory,
        ]);
    }

    //Uploading new resumes
    public function store(StoreResumeRequest $request): RedirectResponse
{
    $uploadedFile = $request->file('resume');
    $user = $request->user();
    
    $fileHash = hash_file('sha256', $uploadedFile->getRealPath()); //Unique hash to verify the file contents
    $savedPath = null;

    $cleanName = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $uploadedFile->getClientOriginalName()); //Sanitize filename

    try {
        //Save file to local storage folder
        $savedPath = $uploadedFile->store("resumes/{$user->id}", self::DISK);

        DB::transaction(function () use ($user, $uploadedFile, $savedPath, $fileHash, $cleanName) {

            //Fetch model first and demote the current resume
            $user->resumeDocuments()->current()->update(['is_current' => false]);

            $user->resumeDocuments()->create([
                'file_path' => $savedPath,
                'original_name' => $cleanName,
                'mime_type' => $uploadedFile->getClientMimeType() ?: 'application/pdf',
                'file_size' => $uploadedFile->getSize(),
                'content_hash' => $fileHash,
                'extraction_status' => 'pending',
                'is_current' => true,
            ]);

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['resume_path' => $savedPath]
            );
        });

        } catch (\Exception $e) {
            //Clean up file if database fails
            if ($savedPath && Storage::disk(self::DISK)->exists($savedPath)) {
                Storage::disk(self::DISK)->delete($savedPath);
            }
            Log::error('Resume upload failed for user ' . $user->id . ': ' . $e->getMessage());
            throw $e;
        }

        return redirect()->route('applicant.resume')->with('status', 'Resume uploaded successfully.');
    }

    //For download of resume file
    public function show(ResumeDocument $resumeDocument): StreamedResponse
    {
        Gate::authorize('view', $resumeDocument);

        return Storage::disk(self::DISK)->download(
            $resumeDocument->file_path,
            $resumeDocument->original_name ?? 'resume.pdf'
        );
    }

    //Swap old resume to become current one option
    public function setCurrent(ResumeDocument $resumeDocument): RedirectResponse
    {
    Gate::authorize('update', $resumeDocument);

    DB::transaction(function () use ($resumeDocument) {

        //Query resumes using user ID
        ResumeDocument::where('user_id', $resumeDocument->user_id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        //Promote the old version
        $resumeDocument->update(['is_current' => true]);

        //Update the profile directly
        Profile::updateOrCreate(
            ['user_id' => $resumeDocument->user_id],
            ['resume_path' => $resumeDocument->file_path]
        );
    });

    return redirect()->route('applicant.resume')->with('status', 'Resume set as current.');
    }

    //Delete completely resume from system
    public function destroy(ResumeDocument $resumeDocument): RedirectResponse
    {
        Gate::authorize('delete', $resumeDocument);

        $path = $resumeDocument->file_path;
        $disk = Storage::disk(self::DISK);

        //Delete file
        $fileAlreadyGone = !$disk->exists($path);
        $fileRemoved     = $fileAlreadyGone || $disk->delete($path);

        if (!$fileRemoved) {
            Log::warning("Could not delete resume file [{$path}] for user {$resumeDocument->user_id}.");

            return redirect()->route('applicant.resume')->with('error', 'The resume file could not be removed. Please try again or contact support.');
        }

        $resumeDocument->user->profile()
            ->where('resume_path', $path)
            ->update(['resume_path' => null]);

        //Delete database record
        $resumeDocument->delete();

        /*Optional: If deleted resume was current, set the recent previous resume as current one.
        $latestOldResume = $resumeDocument->user->resumeDocuments()
            ->where('id', '!=', $resumeDocument->id)
            ->latest()
            ->first();

        if ($latestOldResume) {
            $latestOldResume->update(['is_current' => true]);
            $resumeDocument->user->profile()->updateOrCreate(
                ['user_id' => $resumeDocument->user_id],
                ['resume_path' => $latestOldResume->file_path]
            );
        }
        */

        return redirect()->route('applicant.resume')->with('status', 'Resume deleted.');
    }
}