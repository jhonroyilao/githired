<x-layouts.app>
    {{-- SAMPLE VIEW BLADE FOR ISSUE #13, NOT FINAL YET --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Resume
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Upload New Resume</h3>

                    <form action="{{ route('applicant.resume.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <input
                                type="file"
                                name="resume"
                                accept="application/pdf"
                                class="block w-full text-sm text-gray-500
                                       file:mr-4 file:py-2 file:px-4
                                       file:rounded file:border-0
                                       file:text-sm file:font-semibold
                                       file:bg-blue-50 file:text-blue-700
                                       hover:file:bg-blue-100">
                            
                            @error('resume')
                                <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Upload PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if ($currentResume)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Current Resume</h3>
                        <div class="flex items-center justify-between">
                            <p>{{ $currentResume->original_name }}</p>
                            <div class="flex gap-4">
                                <a href="{{ route('applicant.resume.show', $currentResume) }}"
                                   class="text-blue-500 underline">
                                    Download
                                </a>
                                <form action="{{ route('applicant.resume.destroy', $currentResume) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="text-red-500 underline"
                                        onclick="return confirm('Delete your current resume? This cannot be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($resumeHistory->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Resume History</h3>
                        <ul>
                            @foreach ($resumeHistory as $oldResume)
                                <li class="mb-2 flex justify-between items-center border-b pb-2">
                                    <span>
                                        {{ $oldResume->original_name }}
                                        (Uploaded: {{ $oldResume->created_at->format('M d, Y') }})
                                    </span>
                                    <div class="flex gap-4">
                                        <a href="{{ route('applicant.resume.show', $oldResume) }}"
                                           class="text-blue-500 underline">
                                            Download
                                        </a>
                                        <form action="{{ route('applicant.resume.set-current', $oldResume) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 underline">
                                                Set as Current
                                            </button>
                                        </form>
                                        <form action="{{ route('applicant.resume.destroy', $oldResume) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="text-red-500 underline"
                                                onclick="return confirm('Delete this resume? This cannot be undone.')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.app>