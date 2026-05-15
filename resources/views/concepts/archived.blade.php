<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Archived Concepts - {{ $domain->name }}
            </h2>
            <a href="{{ route('domains.concepts.index', $domain) }}"
               class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                Back to Concepts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($concepts->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No archived concepts.</p>
                            <a href="{{ route('domains.concepts.index', $domain) }}"
                               class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                Back to active concepts
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($concepts as $concept)
                                <div class="border dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-600 dark:text-gray-300">
                                                {{ $concept->title }}
                                            </h3>
                                            <div class="flex gap-2 mt-2">
                                                <span class="px-2 py-1 text-xs rounded
                                                    @if($concept->status === 'mastered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($concept->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300 @endif">
                                                    @if($concept->status === 'to_review') À revoir
                                                    @elseif($concept->status === 'in_progress') En cours
                                                    @else Maîtrisé @endif
                                                </span>
                                                <span class="px-2 py-1 text-xs rounded
                                                    @if($concept->difficulty === 'senior') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                    @elseif($concept->difficulty === 'mid') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @else bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300 @endif">
                                                    {{ ucfirst($concept->difficulty) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                                Deleted: {{ $concept->deleted_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="flex gap-2 ml-4">
                                            <form method="POST" action="{{ route('concepts.restore', $concept) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                                    Restore
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('concepts.forceDelete', $concept) }}"
                                                  onsubmit="return confirm('Permanently delete this concept? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                                    Force Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $concepts->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>