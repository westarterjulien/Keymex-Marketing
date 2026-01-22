<?php

namespace App\Livewire\StandaloneBat;

use App\Models\Category;
use App\Models\Format;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StandaloneBat;
use App\Models\SupportType;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class BatIndex extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    // Modal send
    public bool $showSendModal = false;
    public ?int $sendingBatId = null;

    // Modal convert to order
    public bool $showConvertModal = false;
    public ?StandaloneBat $convertingBat = null;
    public ?int $selectedSupportTypeId = null;
    public ?int $selectedFormatId = null;
    public ?int $selectedCategoryId = null;
    public int $quantity = 1;
    public string $orderNotes = '';

    // Delete
    public bool $showDeleteModal = false;
    public ?int $deletingBatId = null;

    public function sendBat(int $batId): void
    {
        $bat = StandaloneBat::find($batId);

        if ($bat && $bat->status === 'draft') {
            $bat->markAsSent();
            session()->flash('success', 'BAT envoye avec succes. Le lien de validation a ete genere.');
        }
    }

    public function copyValidationLink(int $batId): void
    {
        $bat = StandaloneBat::find($batId);

        if ($bat) {
            $this->dispatch('copy-to-clipboard', url: $bat->validation_url);
        }
    }

    public function regenerateToken(int $batId): void
    {
        $bat = StandaloneBat::find($batId);

        if ($bat) {
            $bat->generateNewToken();
            session()->flash('success', 'Nouveau lien de validation genere.');
        }
    }

    public function openConvertModal(int $batId): void
    {
        $this->convertingBat = StandaloneBat::find($batId);

        if ($this->convertingBat && $this->convertingBat->canBeConvertedToOrder()) {
            $this->showConvertModal = true;
            $this->resetConvertForm();
        }
    }

    public function closeConvertModal(): void
    {
        $this->showConvertModal = false;
        $this->convertingBat = null;
        $this->resetConvertForm();
    }

    protected function resetConvertForm(): void
    {
        $this->selectedSupportTypeId = null;
        $this->selectedFormatId = null;
        $this->selectedCategoryId = null;
        $this->quantity = 1;
        $this->orderNotes = '';
    }

    public function convertToOrder(): void
    {
        if (!$this->convertingBat || !$this->convertingBat->canBeConvertedToOrder()) {
            return;
        }

        $this->validate([
            'selectedSupportTypeId' => 'required|exists:support_types,id',
            'selectedFormatId' => 'required|exists:formats,id',
            'selectedCategoryId' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'selectedSupportTypeId.required' => 'Le type de support est obligatoire.',
            'selectedFormatId.required' => 'Le format est obligatoire.',
            'selectedCategoryId.required' => 'La categorie est obligatoire.',
        ]);

        // Create order
        $order = Order::create([
            'advisor_mongo_id' => $this->convertingBat->advisor_mongo_id,
            'advisor_name' => $this->convertingBat->advisor_name,
            'advisor_email' => $this->convertingBat->advisor_email,
            'advisor_agency' => $this->convertingBat->advisor_agency,
            'status' => 'validated', // Already validated via BAT
            'notes' => $this->orderNotes ?: "Converti depuis BAT #{$this->convertingBat->id}",
            'created_by' => auth()->id(),
        ]);

        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'support_type_id' => $this->selectedSupportTypeId,
            'format_id' => $this->selectedFormatId,
            'category_id' => $this->selectedCategoryId,
            'quantity' => $this->quantity,
            'notes' => $this->convertingBat->description,
        ]);

        // Update BAT
        $this->convertingBat->update([
            'status' => 'converted',
            'order_id' => $order->id,
        ]);

        $this->closeConvertModal();
        session()->flash('success', "Commande #{$order->id} creee avec succes a partir du BAT.");
    }

    public function confirmDelete(int $batId): void
    {
        $this->deletingBatId = $batId;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingBatId = null;
    }

    public function deleteBat(): void
    {
        if (!$this->deletingBatId) {
            return;
        }

        $bat = StandaloneBat::find($this->deletingBatId);

        // Only allow deletion for draft or sent BATs
        if ($bat && in_array($bat->status, ['draft', 'sent'])) {
            // Delete file
            if ($bat->file_path) {
                Storage::disk('public')->delete($bat->file_path);
            }
            $bat->delete();
            session()->flash('success', 'BAT supprime avec succes.');
        } elseif ($bat) {
            session()->flash('error', 'Impossible de supprimer un BAT qui a recu une reponse client.');
        }

        $this->showDeleteModal = false;
        $this->deletingBatId = null;
    }

    public function render()
    {
        $query = StandaloneBat::with('creator', 'order')
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('advisor_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('advisor_email', 'like', "%{$search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        } else {
            // Par défaut, exclure les BATs convertis (ils sont dans l'historique)
            $query->where('status', '!=', 'converted');
        }

        return view('livewire.standalone-bat.bat-index', [
            'bats' => $query->paginate(15),
            'supportTypes' => SupportType::active()->orderBy('sort_order')->get(),
            'formats' => Format::active()->orderBy('sort_order')->get(),
            'categories' => Category::active()->orderBy('sort_order')->get(),
        ]);
    }
}
