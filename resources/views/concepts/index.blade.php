<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $domain->name }} - Concepts
            </h2>
            <a href="{{ route('domains.concepts.create', $domain) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Add Concept
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Status</label>
                            <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">All</option>
                                <option value="to_review" {{ request('status') == 'to_review' ? 'selected' : '' }}>À revoir</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="mastered" {{ request('status') == 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Difficulty</label>
                            <select name="difficulty" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">All</option>
                                <option value="junior" {{ request('difficulty') == 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="mid" {{ request('difficulty') == 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="senior" {{ request('difficulty') == 'senior' ? 'selected' : '' }}>Senior</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Sort</label>
                            <select name="sort" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>A-Z</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                            Filter
                        </button>
                        <a href="{{ route('domains.concepts.index', $domain) }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm hover:underline">
                            Clear
                        </a>
                    </form>

                    @if($concepts->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No concepts found.</p>
                            <a href="{{ route('domains.concepts.create', $domain) }}"
                               class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                                Create your first concept
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($concepts as $concept)
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg">
                                                <a href="{{ route('domains.concepts.show', [$domain, $concept]) }}"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                    {{ $concept->title }}
                                                </a>
                                            </h3>
                                            <div class="flex gap-2 mt-2">
                                                <span class="px-2 py-1 text-xs rounded
                                                    @if($concept->status === 'mastered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($concept->status === 'in_progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                    @if($concept->status === 'to_review') À revoir
                                                    @elseif($concept->status === 'in_progress') En cours
                                                    @else Maîtrisé @endif
                                                </span>
                                                <span class="px-2 py-1 text-xs rounded
                                                    @if($concept->difficulty === 'senior') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                                    @elseif($concept->difficulty === 'mid') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                    {{ ucfirst($concept->difficulty) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex gap-2 ml-4">
                                            <form method="POST" action="{{ route('domains.concepts.updateStatus', [$domain, $concept]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()"
                                                        class="text-sm border-gray-300 dark:border-gray-700 rounded">
                                                    <option value="to_review" {{ $concept->status === 'to_review' ? 'selected' : '' }}>À revoir</option>
                                                    <option value="in_progress" {{ $concept->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                                    <option value="mastered" {{ $concept->status === 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
                                                </select>
                                            </form>
                                            <a href="{{ route('domains.concepts.edit', [$domain, $concept]) }}"
                                               class="text-blue-600 hover:underline text-sm">Edit</a>
                                            <form method="POST" action="{{ route('domains.concepts.destroy', [$domain, $concept]) }}"
                                                  onsubmit="return confirm('Delete this concept?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
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

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('domains.concepts.archived', $domain) }}"
                       class="text-gray-600 dark:text-gray-400 hover:underline text-sm">
                        View archived concepts
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>