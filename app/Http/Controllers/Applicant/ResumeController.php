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
            $savedPath = $uploadedFile->store("resumes/{$user->id}", 'local'); //Save file to local storage folder
            DB::transaction(function () use ($user, $uploadedFile, $savedPath, $fileHash) { //Update database
               
                $user->resumeDocuments()->current()->update(['is_current' => false]); //Remove current status on old resume

                //Save new resume and make it current one
                $user->resumeDocuments()->create([
                    'file_path'         => $savedPath,
                    'original_name'     => $uploadedFile->getClientOriginalName(),
                    'mime_type'         => $uploadedFile->getClientMimeType() ?: 'application/pdf',
                    'file_size'         => $uploadedFile->getSize(),
                    'content_hash'      => $fileHash,
                    'extraction_status' => 'pending',
                    'is_current'        => true,
                ]);
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
        Gate::authorize('view', $resumeDocument); //Verify the person trying to download is the owner
        return Storage::disk('local')->download(
            $resumeDocument->file_path,
            $resumeDocument->original_name ?? 'resume.pdf'
        );
    }

    //Swap old resume to become current one option
    public function setCurrent(ResumeDocument $resumeDocument)
    {
        Gate::authorize('view', $resumeDocument); //Verify the person trying to swap is the owner

        DB::transaction(function () use ($resumeDocument) { //Demote current resume and promote the old version
            $resumeDocument->user->resumeDocuments()->current()->update(['is_current' => false]);
            $resumeDocument->update(['is_current' => true]);
        });
        return redirect()
            ->route('applicant.resume')
            ->with('status', 'Resume set as current.');
    }

    //Delete completely resume from system
    public function destroy(ResumeDocument $resumeDocument)
    {
        Gate::authorize('delete', $resumeDocument); //Verify the person trying to delete is the owner

        $path = $resumeDocument->file_path;
        
        if ($resumeDocument->delete()) { //Remove database record completely
            Storage::disk('local')->delete($path);
        }

        return redirect()
            ->route('applicant.resume')
            ->with('status', 'Resume deleted.');
    }
}