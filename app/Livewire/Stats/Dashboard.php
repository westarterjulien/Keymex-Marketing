<?php

namespace App\Livewire\Stats;

use App\Models\BatVersion;
use App\Models\Order;
use App\Models\StandaloneBat;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public string $period = 'month';

    protected $queryString = [
        'period' => ['except' => 'month'],
    ];

    public function render()
    {
        $startDate = match ($this->period) {
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        // Commandes
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();

        // BAT validés (BatVersion + StandaloneBat validés/convertis)
        $batVersionsValidated = BatVersion::where('responded_at', '>=', $startDate)
            ->where('status', 'validated')
            ->count();
        $standaloneBatsValidated = StandaloneBat::where('responded_at', '>=', $startDate)
            ->whereIn('status', ['validated', 'converted'])
            ->count();
        $batsValidated = $batVersionsValidated + $standaloneBatsValidated;

        // BAT refusés/modifications (pour le taux)
        $batsRefused = BatVersion::where('responded_at', '>=', $startDate)->where('status', 'refused')->count()
            + StandaloneBat::where('responded_at', '>=', $startDate)->where('status', 'refused')->count();
        $batsModifications = BatVersion::where('responded_at', '>=', $startDate)->where('status', 'modifications_requested')->count()
            + StandaloneBat::where('responded_at', '>=', $startDate)->where('status', 'modifications_requested')->count();

        // Taux de validation
        $batsWithResponse = $batsValidated + $batsRefused + $batsModifications;
        $validationRate = $batsWithResponse > 0 ? round(($batsValidated / $batsWithResponse) * 100) : 0;

        // BAT en attente (tous types)
        $pendingBatVersions = BatVersion::where('status', 'pending')
            ->whereHas('activeToken')
            ->count();
        $pendingStandaloneBats = StandaloneBat::whereIn('status', ['pending', 'sent'])
            ->where('token_expires_at', '>', now())
            ->whereNull('token_used_at')
            ->count();
        $batsPending = $pendingBatVersions + $pendingStandaloneBats;

        return view('livewire.stats.dashboard', [
            'totalOrders' => $totalOrders,
            'batsValidated' => $batsValidated,
            'batsPending' => $batsPending,
            'validationRate' => $validationRate,
        ]);
    }
}
