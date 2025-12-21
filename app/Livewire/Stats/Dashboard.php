<?php

namespace App\Livewire\Stats;

use App\Models\BatVersion;
use App\Models\Order;
use App\Services\MongoPropertyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $pendingOrders = Order::where('created_at', '>=', $startDate)->where('status', 'pending')->count();
        $completedOrders = Order::where('created_at', '>=', $startDate)->where('status', 'completed')->count();

        $batsSent = BatVersion::where('sent_at', '>=', $startDate)->count();
        $batsValidated = BatVersion::where('responded_at', '>=', $startDate)->where('status', 'validated')->count();
        $batsRefused = BatVersion::where('responded_at', '>=', $startDate)->where('status', 'refused')->count();
        $batsModifications = BatVersion::where('responded_at', '>=', $startDate)->where('status', 'modifications_requested')->count();

        $validationRate = $batsSent > 0 ? round(($batsValidated / $batsSent) * 100) : 0;

        // MySQL compatible time difference calculation
        $avgValidationTime = BatVersion::where('responded_at', '>=', $startDate)
            ->whereNotNull('responded_at')
            ->whereNotNull('sent_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, sent_at, responded_at)) as avg_hours')
            ->value('avg_hours');
        $avgValidationTime = $avgValidationTime ? round($avgValidationTime, 1) : null;

        $ordersByStatus = Order::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $ordersBySupport = Order::where('orders.created_at', '>=', $startDate)
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('support_types', 'order_items.support_type_id', '=', 'support_types.id')
            ->select('support_types.name', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('support_types.name', 'support_types.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentOrders = Order::with(['items.supportType', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingBats = BatVersion::with(['order'])
            ->where('status', 'pending')
            ->whereHas('activeToken')
            ->latest('sent_at')
            ->limit(5)
            ->get();

        return view('livewire.stats.dashboard', [
            'totalOrders' => $totalOrders,
            'pendingOrders' => $pendingOrders,
            'completedOrders' => $completedOrders,
            'batsSent' => $batsSent,
            'batsValidated' => $batsValidated,
            'batsRefused' => $batsRefused,
            'batsModifications' => $batsModifications,
            'validationRate' => $validationRate,
            'avgValidationTime' => $avgValidationTime,
            'ordersByStatus' => $ordersByStatus,
            'ordersBySupport' => $ordersBySupport,
            'recentOrders' => $recentOrders,
            'pendingBats' => $pendingBats,
        ]);
    }
}
