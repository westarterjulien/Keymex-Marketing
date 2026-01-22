<?php

namespace App\Livewire\StandaloneBat;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StandaloneBat;
use App\Models\StorageSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BatShow extends Component
{
    use WithFileUploads;

    public StandaloneBat $bat;
    public $newFile;
    public bool $showUploadForm = false;

    // Conversion modal
    public bool $showConvertModal = false;
    public ?string $orderedAt = null;
    public ?string $expectedDeliveryAt = null;
    public ?int $quantity = null;
    public ?string $price = null;
    public string $deliveryTime = '';

    public function mount(StandaloneBat $bat): void
    {
        $this->bat = $bat;
        $this->orderedAt = now()->format('Y-m-d');
    }

    public function toggleUploadForm(): void
    {
        $this->showUploadForm = !$this->showUploadForm;
        $this->newFile = null;
    }

    public function updateFile(): void
    {
        $this->validate([
            'newFile' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
        ], [
            'newFile.required' => 'Veuillez selectionner un fichier.',
            'newFile.mimes' => 'Le fichier doit etre un PDF ou une image (JPG, PNG).',
            'newFile.max' => 'Le fichier ne doit pas depasser 20 Mo.',
        ]);

        $oldFileName = $this->bat->file_name;
        $oldDisk = $this->bat->storage_disk ?? 'public';
        $newDisk = StorageSetting::getDisk();

        // Delete old file from its original disk
        if ($this->bat->file_path) {
            Storage::disk($oldDisk)->delete($this->bat->file_path);
        }

        // Store new file on current configured disk
        $fileName = 'bat_' . time() . '_' . uniqid() . '.' . $this->newFile->extension();
        $filePath = $this->newFile->storeAs('standalone-bats', $fileName, $newDisk);
        $newFileName = $this->newFile->getClientOriginalName();

        // Update BAT
        $this->bat->update([
            'file_path' => $filePath,
            'file_name' => $newFileName,
            'file_mime' => $this->newFile->getMimeType(),
            'storage_disk' => $newDisk,
            'status' => 'draft', // Reset to draft after update
            'client_comment' => null, // Clear previous comment
            'responded_at' => null,
            'token_used_at' => null,
        ]);

        // Log file update
        $this->bat->logEvent('file_updated', null, [
            'old_file_name' => $oldFileName,
            'new_file_name' => $newFileName,
        ]);

        $this->bat->refresh();
        $this->showUploadForm = false;
        $this->newFile = null;

        session()->flash('success', 'Fichier mis a jour. Le BAT est maintenant en brouillon.');
    }

    public function sendBat(): void
    {
        if ($this->bat->status === 'draft') {
            $this->bat->markAsSent();
            $this->bat->refresh();
            session()->flash('success', 'BAT envoye pour validation.');
        }
    }

    public function resendEmail(): void
    {
        if ($this->bat->advisor_email && in_array($this->bat->status, ['sent', 'modifications_requested', 'refused'])) {
            \Illuminate\Support\Facades\Mail::to($this->bat->advisor_email)
                ->send(new \App\Mail\BatValidationMail($this->bat));

            $this->bat->logEvent('email_resent');
            session()->flash('success', 'Email de validation renvoye a ' . $this->bat->advisor_email);
        }
    }

    public function regenerateToken(): void
    {
        $this->bat->generateNewToken();
        $this->bat->refresh();
        session()->flash('success', 'Nouveau lien de validation genere.');
    }

    public function openConvertModal(): void
    {
        $this->showConvertModal = true;
        $this->orderedAt = now()->format('Y-m-d');
        $this->expectedDeliveryAt = null;
        $this->quantity = $this->bat->quantity ?? 1;
        $this->price = $this->bat->price ? (string) $this->bat->price : null;
        $this->deliveryTime = $this->bat->delivery_time ?? '';
    }

    public function closeConvertModal(): void
    {
        $this->showConvertModal = false;
        $this->orderedAt = now()->format('Y-m-d');
        $this->expectedDeliveryAt = null;
        $this->quantity = null;
        $this->price = null;
        $this->deliveryTime = '';
    }

    public function convertToOrder(): void
    {
        $this->validate([
            'orderedAt' => 'required|date',
            'expectedDeliveryAt' => 'nullable|date|after_or_equal:orderedAt',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'deliveryTime' => 'nullable|string|max:255',
        ], [
            'orderedAt.required' => 'La date de commande est obligatoire.',
            'expectedDeliveryAt.after_or_equal' => 'La date de livraison doit etre apres la date de commande.',
            'quantity.required' => 'La quantite est obligatoire.',
            'quantity.min' => 'La quantite doit etre au moins 1.',
        ]);

        if (!$this->bat->canBeConvertedToOrder()) {
            session()->flash('error', 'Ce BAT ne peut pas etre converti en commande.');
            return;
        }

        // Create the order
        $order = Order::create([
            'advisor_mongo_id' => $this->bat->advisor_mongo_id,
            'advisor_name' => $this->bat->advisor_name,
            'advisor_email' => $this->bat->advisor_email,
            'advisor_agency' => $this->bat->advisor_agency,
            'status' => 'validated',
            'ordered_at' => $this->orderedAt,
            'expected_delivery_at' => $this->expectedDeliveryAt,
            'notes' => $this->bat->description,
            'created_by' => auth()->id(),
        ]);

        // Create order item from BAT details
        OrderItem::create([
            'order_id' => $order->id,
            'support_type_id' => $this->bat->support_type_id,
            'format_id' => $this->bat->format_id,
            'category_id' => $this->bat->category_id,
            'quantity' => $this->quantity,
            'notes' => $this->bat->title,
        ]);

        // Update BAT with order info and conversion details
        $this->bat->update([
            'order_id' => $order->id,
            'status' => 'converted',
            'quantity' => $this->quantity,
            'price' => $this->price ? (float) $this->price : null,
            'delivery_time' => $this->deliveryTime ?: null,
        ]);

        // Log conversion
        $this->bat->logEvent('converted_to_order', null, [
            'order_id' => $order->id,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ]);

        $this->bat->refresh();
        $this->closeConvertModal();

        session()->flash('success', 'BAT converti en commande #' . $order->id . ' avec succes.');
    }

    public function render()
    {
        return view('livewire.standalone-bat.bat-show');
    }
}
