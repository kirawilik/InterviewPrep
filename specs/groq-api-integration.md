# specs/groq-api-integration.md

> **Feature :** Génération de questions d'entretien via l'API Groq  
> **User Stories couvertes :** US11, US12, US13  
> **Agent utilisé :** Claude Code / OpenCode  
> **Statut :** À construire

---

## 1. Vue d'ensemble

Cette feature permet de générer automatiquement **5 questions d'entretien techniques** à partir du titre et de l'explication d'un concept, via l'API Groq. Les résultats sont sauvegardés en base de données avant affichage, et l'historique des générations est consultable depuis la page détail du concept.

---

## 2. Configuration de l'environnement

### 2.1 Clé API dans `.env`

```env
GROQ_API_KEY=gsk_xxxxxxxxxxxxxxxxxxxxxxxxxxxx
GROQ_API_URL=https://api.groq.com/openai/v1/chat/completions
GROQ_MODEL=llama3-8b-8192
```

> ⚠️ Ne jamais commiter la clé API. Ajouter `.env` au `.gitignore` (déjà fait par défaut avec Laravel).

### 2.2 Accès via `config/services.php`

```php
'groq' => [
    'api_key' => env('GROQ_API_KEY'),
    'api_url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions'),
    'model'   => env('GROQ_MODEL', 'llama3-8b-8192'),
],
```

---

## 3. Migration — Table `interview_generations`

```bash
php artisan make:migration create_interview_generations_table
```

```php
Schema::create('interview_generations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('concept_id')->constrained()->cascadeOnDelete();
    $table->json('questions');        // tableau de 5 questions
    $table->timestamps();
});
```

---

## 4. Modèle `InterviewGeneration`

```bash
php artisan make:model InterviewGeneration
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewGeneration extends Model
{
    protected $fillable = ['concept_id', 'questions'];

    protected $casts = [
        'questions' => 'array',
    ];

    public function concept()
    {
        return $this->belongsTo(Concept::class);
    }
}
```

Ajouter la relation inverse dans `Concept` :

```php
public function interviewGenerations()
{
    return $this->hasMany(InterviewGeneration::class)->latest();
}
```

---

## 5. Service `GroqService`

```bash
php artisan make:class App/Services/GroqService
```

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model  = config('services.groq.model');
    }

    /**
     * Génère 5 questions d'entretien pour un concept donné.
     *
     * @return array<string>|null  Tableau de 5 questions, ou null en cas d'erreur
     */
    public function generateInterviewQuestions(string $title, string $explanation): ?array
    {
        $prompt = $this->buildPrompt($title, $explanation);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model'    => $this->model,
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'Tu es un recruteur technique senior spécialisé en développement web backend. Tu génères uniquement des questions d\'entretien pertinentes, précises et réalistes. Réponds exclusivement en JSON valide, sans texte autour.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens'  => 1024,
            ]);

            if ($response->failed()) {
                Log::error('Groq API error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $content = $response->json('choices.0.message.content');
            return $this->parseQuestions($content);

        } catch (\Exception $e) {
            Log::error('Groq API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function buildPrompt(string $title, string $explanation): string
    {
        return <<<PROMPT
Génère exactement 5 questions d'entretien technique pour le concept suivant.

**Concept :** {$title}

**Explication :**
{$explanation}

Réponds UNIQUEMENT avec un objet JSON dans ce format exact :
{
  "questions": [
    "Question 1 ?",
    "Question 2 ?",
    "Question 3 ?",
    "Question 4 ?",
    "Question 5 ?"
  ]
}
PROMPT;
    }

    private function parseQuestions(string $content): ?array
    {
        // Nettoyage des backticks markdown si présents
        $content = preg_replace('/```json|```/', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);

        if (
            json_last_error() === JSON_ERROR_NONE &&
            isset($decoded['questions']) &&
            is_array($decoded['questions']) &&
            count($decoded['questions']) === 5
        ) {
            return $decoded['questions'];
        }

        Log::warning('Groq: impossible de parser les questions', ['raw' => $content]);
        return null;
    }
}
```

---

## 6. Controller `InterviewGenerationController`

```bash
php artisan make:controller InterviewGenerationController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Concept;
use App\Models\InterviewGeneration;
use App\Services\GroqService;
use Illuminate\Http\RedirectResponse;

class InterviewGenerationController extends Controller
{
    public function __construct(protected GroqService $groqService) {}

    /**
     * US11 — Générer des questions pour un concept.
     */
    public function store(Concept $concept): RedirectResponse
    {
        $this->authorize('view', $concept); // ou gate manuel si pas de policy

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

    /**
     * US13 — Supprimer une génération.
     */
    public function destroy(InterviewGeneration $generation): RedirectResponse
    {
        $this->authorize('delete', $generation->concept);

        $generation->delete();

        return back()->with('success', 'Génération supprimée.');
    }
}
```

---

## 7. Routes

Dans `routes/web.php` :

```php
use App\Http\Controllers\InterviewGenerationController;

Route::middleware('auth')->group(function () {

    // ... autres routes

    // Génération AI
    Route::post(
        '/concepts/{concept}/generations',
        [InterviewGenerationController::class, 'store']
    )->name('generations.store');

    Route::delete(
        '/generations/{generation}',
        [InterviewGenerationController::class, 'destroy']
    )->name('generations.destroy');
});
```

---

## 8. Vue — Page détail du concept

### 8.1 Bouton de génération (US11)

```blade
{{-- resources/views/concepts/show.blade.php --}}

{{-- Messages flash --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Bouton générer --}}
<form action="{{ route('generations.store', $concept) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary">
        ✨ Générer des questions d'entretien
    </button>
</form>
```

### 8.2 Historique des générations (US12 + US13)

```blade
@forelse ($concept->interviewGenerations as $generation)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Généré le {{ $generation->created_at->format('d/m/Y à H:i') }}
            </small>

            {{-- US13 : Supprimer --}}
            <form action="{{ route('generations.destroy', $generation) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Supprimer cette génération ?')">
                    Supprimer
                </button>
            </form>
        </div>

        <ul class="list-group list-group-flush">
            @foreach ($generation->questions as $index => $question)
                <li class="list-group-item">
                    <strong>Q{{ $index + 1 }}.</strong> {{ $question }}
                </li>
            @endforeach
        </ul>
    </div>
@empty
    <p class="text-muted">Aucune question générée pour ce concept.</p>
@endforelse
```

---

## 9. Checklist d'implémentation

- [ ] Ajouter `GROQ_API_KEY` dans `.env` (et `.env.example` sans valeur)
- [ ] Ajouter le bloc `groq` dans `config/services.php`
- [ ] Créer et lancer la migration `interview_generations`
- [ ] Créer le modèle `InterviewGeneration` avec cast `questions → array`
- [ ] Ajouter la relation `interviewGenerations()` dans `Concept`
- [ ] Créer `GroqService` dans `app/Services/`
- [ ] Créer `InterviewGenerationController`
- [ ] Ajouter les routes dans `routes/web.php`
- [ ] Intégrer le bouton et l'historique dans `concepts/show.blade.php`
- [ ] Tester manuellement : génération réussie, API down (clé invalide), parsing échoué
- [ ] Vérifier que les erreurs affichent un message propre (pas de page blanche)

---

## 10. Gestion des erreurs — Comportements attendus

| Scénario | Comportement |
|---|---|
| Clé API invalide / absente | Message flash d'erreur, redirection back |
| Timeout réseau (> 30s) | Message flash d'erreur, redirection back |
| JSON mal formé dans la réponse | Message flash d'erreur, log Laravel, redirection back |
| Génération réussie | Message flash de succès, questions affichées |
| Suppression réussie | Message flash de succès, génération retirée de la liste |

---

## 11. Modèles Groq disponibles (free tier)

| Modèle | Tokens/min | Remarque |
|---|---|---|
| `llama3-8b-8192` | 30 000 | Recommandé — rapide et suffisant |
| `llama3-70b-8192` | 6 000 | Plus précis, plus lent |
| `mixtral-8x7b-32768` | 5 000 | Bon pour les longues explications |

> Changer le modèle dans `.env` via `GROQ_MODEL` sans modifier le code.

---

## 12. Commandes utiles

```bash
# Lancer la migration
php artisan migrate

# Vider le cache de config après modification de .env
php artisan config:clear

# Tester l'appel API depuis Tinker
php artisan tinker
>>> app(App\Services\GroqService::class)->generateInterviewQuestions('Eloquent N+1', 'Le problème N+1 survient quand...')
```
