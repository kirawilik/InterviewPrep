<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $concept->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('domains.concepts.edit', [$concept->domain, $concept]) }}"
                   class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Edit
                </a>
                <form method="POST" action="{{ route('domains.concepts.destroy', [$concept->domain, $concept]) }}"
                      onsubmit="return confirm('Delete this concept?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 dark:bg-emerald-900/20 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-8">
                    <div class="flex gap-2 mb-6">
                        <span class="px-3 py-1 text-sm rounded
                            @if($concept->status === 'mastered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($concept->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            @if($concept->status === 'to_review') À revoir
                            @elseif($concept->status === 'in_progress') En cours
                            @else Maîtrisé @endif
                        </span>
                        <span class="px-3 py-1 text-sm rounded
                            @if($concept->difficulty === 'senior') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                            @elseif($concept->difficulty === 'mid') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($concept->difficulty) }}
                        </span>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($concept->explanation)) !!}
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                        <p>Created: {{ $concept->created_at->format('M d, Y') }}</p>
                        <p>Updated: {{ $concept->updated_at->format('M d, Y') }}</p>
                        <a href="{{ route('domains.concepts.index', $concept->domain) }}"
                           class="text-indigo-600 dark:text-indigo-400 hover:underline mt-2 inline-block">
                            Back to {{ $concept->domain->name }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Interview Questions</h3>
                        <form action="{{ route('generations.store', $concept) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow-md active:scale-95 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Generate Questions
                            </button>
                        </form>
                    </div>

                    @forelse($concept->interviewGenerations as $generation)
                        <div class="mb-6 p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <small class="text-gray-500 dark:text-gray-400">
                                    Generated {{ $generation->created_at->format('d/m/Y à H:i') }}
                                </small>
                                <form action="{{ route('generations.destroy', $generation) }}" method="POST"
                                      onsubmit="return confirm('Delete this generation?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 active:scale-95 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                            <ul class="space-y-3">
                                @foreach($generation->questions as $index => $question)
                                    <li class="flex gap-3">
                                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 text-sm font-semibold rounded-full">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300 pt-1">{{ $question }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">No questions generated yet. Click the button above to generate 5 interview questions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>