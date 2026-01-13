<div class="space-y-6">
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6 shadow-xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z\" fill=\"rgba(255,255,255,0.07)\"%3E%3C/path%3E%3C/svg%3E')] opacity-60"></div>
        <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-white/5"></div>

        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Assistant IA</h1>
                    <p class="mt-0.5 text-sm text-white/80">Posez vos questions sur les performances social media</p>
                </div>
            </div>
            <a href="{{ route('social-media.dashboard') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white hover:bg-white/20 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au dashboard
            </a>
        </div>
    </div>

    {{-- Configuration requise --}}
    @if(!$isConfigured)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-amber-800">API OpenAI non configuree</h3>
                    <p class="mt-1 text-sm text-amber-700">Ajoutez votre cle API OpenAI dans le fichier .env :</p>
                    <pre class="mt-3 rounded-lg bg-amber-100 p-3 text-xs text-amber-900">OPENAI_API_KEY=sk-votre-cle-api</pre>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Sidebar suggestions --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Suggestions</h3>
                    <div class="space-y-2">
                        @foreach($suggestions as $suggestion)
                            <button wire:click="askSuggestion('{{ $suggestion }}')"
                                    wire:loading.attr="disabled"
                                    class="w-full text-left rounded-lg bg-gray-50 px-3 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors disabled:opacity-50">
                                {{ $suggestion }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Actions</h3>
                    <button wire:click="clearHistory"
                            wire:confirm="Etes-vous sur de vouloir effacer l'historique ?"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-50 px-3 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Effacer l'historique
                    </button>
                </div>
            </div>

            {{-- Zone de chat --}}
            <div class="lg:col-span-3">
                <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 flex flex-col h-[600px]">
                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-4" id="chat-messages">
                        @if($chatHistory->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100">
                                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-gray-900">Commencez une conversation</h3>
                                <p class="mt-1 text-sm text-gray-500 max-w-sm">
                                    Posez une question sur vos performances Facebook ou Instagram, et l'IA vous repondra en analysant vos donnees.
                                </p>
                            </div>
                        @else
                            @foreach($chatHistory as $message)
                                <div class="flex {{ $message->isUserMessage() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[80%] {{ $message->isUserMessage() ? 'order-2' : 'order-1' }}">
                                        <div class="rounded-2xl px-4 py-3 {{ $message->isUserMessage() ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                                            <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-400 {{ $message->isUserMessage() ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at->diffForHumans() }}
                                            @if($message->tokens_used)
                                                &bull; {{ $message->tokens_used }} tokens
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="askQuestion, askSuggestion" class="flex justify-start">
                            <div class="bg-gray-100 rounded-2xl px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                                    </div>
                                    <span class="text-sm text-gray-500">L'IA reflechit...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Erreur --}}
                    @if($error)
                        <div class="px-6 pb-2">
                            <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                                <p class="text-sm text-red-700">{{ $error }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Input --}}
                    <div class="border-t border-gray-200 p-4">
                        <form wire:submit="askQuestion" class="flex items-center gap-3">
                            <div class="relative flex-1">
                                <input type="text"
                                       wire:model="question"
                                       placeholder="Posez votre question sur vos stats social media..."
                                       class="w-full rounded-xl border-gray-300 pr-12 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       @disabled($isLoading)>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-xs text-gray-400">{{ strlen($question) }}/1000</span>
                                </div>
                            </div>
                            <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center rounded-xl bg-indigo-600 p-3 text-white hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg wire:loading.remove wire:target="askQuestion" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <svg wire:loading wire:target="askQuestion" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </form>
                        @error('question')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('scroll-to-bottom', () => {
        const container = document.getElementById('chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    $wire.on('chat-updated', () => {
        setTimeout(() => {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });
</script>
@endscript
