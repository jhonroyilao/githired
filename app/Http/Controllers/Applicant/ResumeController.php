<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreResumeRequest;
use App\Models\ResumeDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ResumeController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        //Show active and old resumes
        $resumeHistory = $user->resumeDocuments()
            ->where('is_current', false)
            ->latest()
            ->get();
            
        return view('applicant.resume.view', [
            'currentResume' => $user->currentResumeDocument,
            'resumeHistory' => $resumeHistory
        ]);
    }

    //Uploading new resumes
    public function store(StoreResumeRequest $request)
    {
        $uploadedFile = $request->file('resume');
        $user = $request->user();

        //Unique hash to verify the file contents
        $fileHash = hash_file('sha256', $uploadedFile->getRealPath());
        $savedPath = null;

        try {
            //Save file to local storage folder
            $savedPath = $uploadedFile->store("resumes/{$user->id}", 'local');

            DB::transaction(function () use ($user, $uploadedFile, $savedPath, $fileHash) { 
                
                // Fetch model first
                $currentResume = $user->currentResumeDocument;
                if ($currentResume) {
                    $currentResume->update(['is_current' => false]);
                }

                $newResume = new ResumeDocument();
                $newResume->user_id = $user->id;
                $newResume->file_path = $savedPath;
                $newResume->original_name = $uploadedFile->getClientOriginalName();
                $newResume->mime_type = $uploadedFile->getClientMimeType() ?: 'application/pdf';
                $newResume->file_size = $uploadedFile->getSize();
                $newResume->content_hash = $fileHash;
                $newResume->extraction_status = 'pending';
                $newResume->is_current = true; 
                
                $newResume->save();
            });

        } catch (\Exception $e) {
            //Clean up file if database fails
            if ($savedPath && Storage::disk('local')->exists($savedPath)) {
                Storage::disk('local')->delete($savedPath);
            }
            Log::error('Resume upload failed for user ' . $user->id . ': ' . $e->getMessage());
            throw $e;
        }

        return redirect()
            ->route('applicant.resume')
            ->with('status', 'Resume uploaded successfully.');
    }

    //For download of resume file
    public function show(ResumeDocument $resumeDocument)
    {
        Gate::authorize('view', $resumeDocument); 
        
        return Storage::disk('local')->download(
            $resumeDocument->file_path,
            $resumeDocument->original_name ?? 'resume.pdf'
        );
    }

    //Swap old resume to become current one option
    public function setCurrent(ResumeDocument $resumeDocument)
    {
        Gate::authorize('update', $resumeDocument);

        DB::transaction(function () use ($resumeDocument) { 
            // Demote the current resume
            $currentResume = $resumeDocument->user->currentResumeDocument;
            if ($currentResume) {
                $currentResume->update(['is_current' => false]);
            }
            
            // Promote the old version
            $resumeDocument->update(['is_current' => true]);
        });

        return redirect()
            ->route('applicant.resume')
            ->with('status', 'Resume set as current.');
    }

    //Delete completely resume from system
    public function destroy(ResumeDocument $resumeDocument)
    {
        Gate::authorize('delete', $resumeDocument);

        $path = $resumeDocument->file_path;

        //Delete file 
        if (Storage::disk('local')->delete($path)) { 
            $resumeDocument->delete(); //Delete database record
        }
        
        return redirect()
            ->route('applicant.resume')
            ->with('status', 'Resume deleted.');
    }
}