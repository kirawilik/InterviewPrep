<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Concept in {{ $domain->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('domains.concepts.store', $domain) }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" value="Title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                          :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="explanation" value="Explanation" />
                            <textarea id="explanation" name="explanation" rows="10"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      required>{{ old('explanation') }}</textarea>
                            <x-input-error :messages="$errors->get('explanation')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="difficulty" value="Difficulty" />
                                <select id="difficulty" name="difficulty"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>
                                    <option value="junior" {{ old('difficulty') == 'junior' ? 'selected' : '' }}>Junior</option>
                                    <option value="mid" {{ old('difficulty') == 'mid' ? 'selected' : '' }}>Mid</option>
                                    <option value="senior" {{ old('difficulty') == 'senior' ? 'selected' : '' }}>Senior</option>
                                </select>
                                <x-input-error :messages="$errors->get('difficulty')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Status" />
                                <select id="status" name="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>
                                    <option value="to_review" {{ old('status') == 'to_review' ? 'selected' : '' }}>À revoir</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                    <option value="mastered" {{ old('status') == 'mastered' ? 'selected' : '' }}>Maîtrisé</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('domains.concepts.index', $domain) }}"
                               class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:underline">
                                Cancel
                            </a>
                            <x-primary-button>Create Concept</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>