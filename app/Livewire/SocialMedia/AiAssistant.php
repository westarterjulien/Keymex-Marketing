<?php

namespace App\Livewire\SocialMedia;

use App\Services\OpenAiAnalyticsService;
use App\Models\SocialMediaChat;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Assistant IA - Social Media')]
class AiAssistant extends Component
{
    public string $question = '';
    public bool $isLoading = false;
    public ?string $error = null;

    protected OpenAiAnalyticsService $aiService;

    protected $rules = [
        'question' => 'required|min:3|max:1000',
    ];

    protected $messages = [
        'question.required' => 'Veuillez saisir une question.',
        'question.min' => 'La question doit contenir au moins 3 caracteres.',
        'question.max' => 'La question ne peut pas depasser 1000 caracteres.',
    ];

    public function boot(OpenAiAnalyticsService $aiService): void
    {
        $this->aiService = $aiService;
    }

    public function askQuestion(): void
    {
        $this->validate();

        if (!$this->aiService->isConfigured()) {
            $this->error = "L'API OpenAI n'est pas configuree.";
            return;
        }

        $this->isLoading = true;
        $this->error = null;

        try {
            $this->aiService->chat(auth()->id(), $this->question);
            $this->question = '';
            $this->dispatch('chat-updated');
            $this->dispatch('scroll-to-bottom');
        } catch (\Exception $e) {
            $this->error = "Erreur: " . $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function clearHistory(): void
    {
        SocialMediaChat::clearForUser(auth()->id());
        $this->dispatch('notify', message: 'Historique efface');
    }

    public function askSuggestion(string $suggestion): void
    {
        $this->question = $suggestion;
        $this->askQuestion();
    }

    public function render()
    {
        $chatHistory = SocialMediaChat::historyForUser(auth()->id(), 50);
        $isConfigured = $this->aiService->isConfigured();

        $suggestions = [
            "Quelles sont mes meilleures publications ce mois-ci ?",
            "Comment ameliorer mon taux d'engagement ?",
            "Quel est le meilleur moment pour publier ?",
            "Compare mes performances Facebook et Instagram",
            "Quels types de contenus fonctionnent le mieux ?",
        ];

        return view('livewire.social-media.ai-assistant', [
            'chatHistory' => $chatHistory,
            'isConfigured' => $isConfigured,
            'suggestions' => $suggestions,
        ]);
    }
}
