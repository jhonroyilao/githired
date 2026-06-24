<?php

namespace App\Http\Controllers\Applicant;

use App\Jobs\ExtractResumeText;
use App\Actions\Applicant\DeleteResumeAction;
use App\Actions\Applicant\SetCurrentResumeAction;
use App\Actions\Applicant\StoreResumeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\StoreResumeRequest;
use App\Models\ResumeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ResumeController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const REDIRECT_ROUTES = [
        'applicant.resume',
        'applicant.onboarding.links',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('applicant.resume.index', [
            'user' => $user,
            'currentResume' => $user->currentResumeDocument,
            'resumeHistory' => $user->resumeDocuments()
                ->where('is_current', false)
                ->latest()
                ->get(),
        ]);
    }

    public function store(StoreResumeRequest $request, StoreResumeAction $storeResume): RedirectResponse
    {
        $resume = $storeResume->handle($request->user(), $request->file('resume'));

        // Only dispatch if the Action actually created a new database row
        if ($resume->wasRecentlyCreated) {
            ExtractResumeText::dispatch($resume);
            $statusMessage = 'Resume uploaded and queued for processing.';
        } else {
            $statusMessage = 'Duplicate resume detected. Using your existing file.';
        }

        $redirect = redirect()
            ->route($this->redirectRoute($request))
            ->with('status', $statusMessage);

        if ($this->redirectRoute($request) === 'applicant.onboarding.links') {
            return $redirect->withInput($request->only('github', 'linkedin', 'website'));
        }

        return $redirect;
    }

    public function show(ResumeDocument $resumeDocument): StreamedResponse
    {
        Gate::authorize('view', $resumeDocument);

        $disk = Storage::disk(config('filesystems.resume_disk', 'local'));

        abort_unless($disk->exists($resumeDocument->file_path), 404);

        return $disk->download(
            $resumeDocument->file_path,
            $resumeDocument->original_name ?? 'resume.pdf',
        );
    }

    public function setCurrent(Request $request, ResumeDocument $resumeDocument, SetCurrentResumeAction $setCurrentResume): RedirectResponse
    {
        Gate::authorize('update', $resumeDocument);

        $setCurrentResume->handle($resumeDocument);

        return redirect()
            ->route($this->redirectRoute($request))
            ->with('status', 'Current resume updated.');
    }

    public function destroy(Request $request, ResumeDocument $resumeDocument, DeleteResumeAction $deleteResume): RedirectResponse
    {
        Gate::authorize('delete', $resumeDocument);

        $deleteResume->handle($resumeDocument);

        return redirect()
            ->route($this->redirectRoute($request))
            ->with('status', 'Resume deleted.');
    }

    private function redirectRoute(Request $request): string
    {
        $route = $request->string('redirect_to')->toString();

        return in_array($route, self::REDIRECT_ROUTES, true)
            ? $route
            : 'applicant.resume';
    }
}