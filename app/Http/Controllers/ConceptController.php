<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConceptRequest;
use App\Http\Requests\UpdateConceptRequest;
use App\Models\Concept;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ConceptController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request, Domain $domain)
    {
        $this->authorizeDomain($domain);

        $query = $domain->concepts();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'alphabetical' => $query->orderBy('title'),
            default => $query->latest(),
        };

        $concepts = $query->paginate(10);

        return view('concepts.index', compact('domain', 'concepts'));
    }

    public function create(Request $request, Domain $domain)
    {
        $this->authorizeDomain($domain);

        return view('concepts.create', compact('domain'));
    }

    public function store(StoreConceptRequest $request, Domain $domain)
    {
        $this->authorizeDomain($domain);

        $domain->concepts()->create($request->validated());

        return redirect()->route('domains.concepts.index', $domain);
    }

    public function show(Request $request, Domain $domain, Concept $concept)
    {
        $this->authorizeDomain($domain);
        $this->authorize('view', $concept);

        return view('concepts.show', compact('concept'));
    }

    public function edit(Request $request, Domain $domain, Concept $concept)
    {
        $this->authorizeDomain($domain);
        $this->authorize('update', $concept);

        return view('concepts.edit', compact('concept', 'domain'));
    }

    public function update(UpdateConceptRequest $request, Domain $domain, Concept $concept)
    {
        $this->authorizeDomain($domain);
        $this->authorize('update', $concept);

        $concept->update($request->validated());

        return redirect()->route('domains.concepts.index', $domain);
    }

    public function destroy(Request $request, Domain $domain, Concept $concept)
    {
        $this->authorizeDomain($domain);
        $this->authorize('delete', $concept);

        $concept->delete();

        return redirect()->route('domains.concepts.index', $domain);
    }

    public function updateStatus(Request $request, Domain $domain, Concept $concept)
    {
        $this->authorizeDomain($domain);
        $this->authorize('update', $concept);

        $request->validate([
            'status' => 'required|in:to_review,in_progress,mastered',
        ]);

        $concept->update(['status' => $request->status]);

        return back();
    }

    public function archived(Request $request, Domain $domain)
    {
        $this->authorizeDomain($domain);

        $concepts = $domain->concepts()->onlyTrashed()->latest('deleted_at')->paginate(10);

        return view('concepts.archived', compact('domain', 'concepts'));
    }

    public function restore(Request $request, Concept $concept)
    {
        $this->authorize('restore', $concept);

        $concept->restore();

        return back();
    }

    public function forceDelete(Request $request, Concept $concept)
    {
        $this->authorize('forceDelete', $concept);

        $concept->forceDelete();

        return back();
    }

    private function authorizeDomain(Domain $domain): void
    {
        if ($domain->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
