<?php

namespace App\Livewire\Orders;

use App\Models\BatVersion;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderShow extends Component
{
    use WithFileUploads;

    public Order $order;
    public bool $showUploadModal = false;
    public bool $showStatusModal = false;
    public bool $showTrackingModal = false;
    public $batFile;
    public string $newStatus = '';
    public string $statusComment = '';
    public ?string $trackingUrl = null;

    protected $listeners = ['refreshOrder' => '$refresh'];

    public function mount(Order $order): void
    {
        $this->order = $order->load([
            'items.supportType',
            'items.format',
            'items.category',
            'batVersions.tokens',
            'batVersions.sentBy',
            'creator',
            'standaloneBat.logs',
        ]);
        $this->trackingUrl = $order->tracking_url;
    }

    public function uploadBat(): void
    {
        $this->validate([
            'batFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ], [
            'batFile.required' => 'Veuillez sélectionner un fichier.',
            'batFile.mimes' => 'Le fichier doit être un PDF ou une image (JPG, PNG).',
            'batFile.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
        ]);

        $versionNumber = $this->order->batVersions->count() + 1;
        $fileName = "bat_v{$versionNumber}_{$this->order->id}." . $this->batFile->extension();
        $filePath = $this->batFile->storeAs("bats/{$this->order->id}", $fileName, 'public');

        $batVersion = $this->order->batVersions()->create([
            'version_number' => $versionNumber,
            'file_path' => $filePath,
            'file_name' => $this->batFile->getClientOriginalName(),
            'file_mime' => $this->batFile->getMimeType(),
            'status' => 'pending',
            'sent_at' => now(),
            'sent_by' => Auth::id(),
        ]);

        $batVersion->createToken(30);

        if ($this->order->status === 'pending' || $this->order->status === 'in_progress') {
            $this->order->update(['status' => 'bat_sent']);
        }

        $this->reset(['batFile', 'showUploadModal']);
        $this->order->refresh();

        session()->flash('success', "BAT v{$versionNumber} envoyé avec succès.");
    }

    public function openStatusModal(): void
    {
        $this->showStatusModal = true;
        $this->newStatus = $this->order->status;
    }

    public function updateOrderStatus(): void
    {
        $this->validate([
            'newStatus' => 'required|in:pending,in_progress,bat_sent,validated,refused,modifications_requested,completed',
        ]);

        $this->order->update(['status' => $this->newStatus]);
        $this->showStatusModal = false;
        $this->order->refresh();

        session()->flash('success', 'Statut mis à jour avec succès.');
    }

    public function openTrackingModal(): void
    {
        $this->trackingUrl = $this->order->tracking_url;
        $this->showTrackingModal = true;
    }

    public function updateTrackingUrl(): void
    {
        $this->validate([
            'trackingUrl' => 'nullable|url|max:500',
        ], [
            'trackingUrl.url' => 'Veuillez entrer une URL valide.',
        ]);

        $this->order->update(['tracking_url' => $this->trackingUrl ?: null]);
        $this->showTrackingModal = false;
        $this->order->refresh();

        session()->flash('success', 'Lien de suivi mis à jour.');
    }

    public function copyValidationLink(int $batVersionId): void
    {
        $batVersion = BatVersion::with('activeToken')->find($batVersionId);

        if ($batVersion && $batVersion->activeToken) {
            $this->dispatch('copy-to-clipboard', url: route('bat.validation', $batVersion->activeToken->token));
        }
    }

    public function regenerateToken(int $batVersionId): void
    {
        $batVersion = BatVersion::find($batVersionId);

        if ($batVersion) {
            $batVersion->tokens()->whereNull('used_at')->update(['expires_at' => now()]);
            $batVersion->createToken(30);
            $this->order->refresh();

            session()->flash('success', 'Nouveau lien de validation généré.');
        }
    }

    public function deleteBatVersion(int $batVersionId): void
    {
        $batVersion = BatVersion::find($batVersionId);

        if ($batVersion && $batVersion->order_id === $this->order->id) {
            if ($batVersion->file_path) {
                Storage::disk('public')->delete($batVersion->file_path);
            }
            $batVersion->tokens()->delete();
            $batVersion->delete();
            $this->order->refresh();

            session()->flash('success', 'Version BAT supprimée.');
        }
    }

    public function render()
    {
        return view('livewire.orders.order-show', [
            'statuses' => [
                'pending' => 'En attente',
                'in_progress' => 'En cours',
                'bat_sent' => 'BAT envoyé',
                'validated' => 'Validé',
                'refused' => 'Refusé',
                'modifications_requested' => 'Modifications',
                'completed' => 'Terminé',
            ],
        ]);
    }
}
