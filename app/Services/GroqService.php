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

    public function generateInterviewQuestions(string $title, string $explanation): ?array
    {
        $prompt = $this->buildPrompt($title, $explanation);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model'    => $this->model,
                'response_format' => [
    'type' => 'json_object'
],
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
                
                'temperature' => 0.2,
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
