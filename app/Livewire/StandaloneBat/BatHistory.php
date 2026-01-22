<?php

namespace App\Livewire\StandaloneBat;

use App\Models\StandaloneBat;
use Livewire\Component;
use Livewire\WithPagination;

class BatHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StandaloneBat::with('creator', 'order', 'supportType', 'format', 'category')
            ->where('status', 'converted')
            ->orderBy('updated_at', 'desc');

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('advisor_name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('advisor_email', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%");
                    });
            });
        }

        return view('livewire.standalone-bat.bat-history', [
            'bats' => $query->paginate(20),
        ]);
    }
}
