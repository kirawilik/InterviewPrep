<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DomainController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $domains = Auth::user()->domains()->orderBy('name')->get();
        return view('domains.index', compact('domains'));
    }

    public function create()
    {   
        return view('domains.create');
    }

    public function store(StoreDomainRequest $request)
    {
        Auth::user()->domains()->create($request->validated());

        return redirect()->route('domains.index');
    }

    public function edit(Domain $domain)
    {
        $this->authorize('update', $domain);

        return view('domains.edit', compact('domain'));
    }

    public function update(UpdateDomainRequest $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $domain->update($request->validated());

        return redirect()->route('domains.index');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);

        $domain->delete();

        return redirect()->route('domains.index');
    }
}
