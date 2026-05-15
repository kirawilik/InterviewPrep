<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\InterviewGeneration;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InterviewGenerationController extends Controller
{
     use AuthorizesRequests;
    public function __construct(protected GroqService $groqService) {}

    public function store(Concept $concept): RedirectResponse
    {
        $this->authorize('view', $concept);

        $questions = $this->groqService->generateInterviewQuestions(
            $concept->title,
            $concept->explanation
        );

        if ($questions === null) {
            return back()->with(
                'error',
                'La génération a échoué. Vérifie ta connexion ou réessaie dans quelques instants.'
            );
        }

        InterviewGeneration::create([
            'concept_id' => $concept->id,
            'questions'  => $questions,
        ]);

        return back()->with('success', '5 questions générées avec succès !');
    }

    public function destroy(InterviewGeneration $generation): RedirectResponse
    {
        $this->authorize('delete', $generation->concept);

        $generation->delete();

        return back()->with('success', 'Génération supprimée.');
    }
}
