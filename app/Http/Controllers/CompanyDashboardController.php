<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Company;
use App\Models\QueueEntry;
use App\Models\Shop;
use Carbon\Carbon;
use Inertia\Inertia;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $shop = $user->shop;
        $company = $shop?->company;

        if (!$company) {
            return redirect()->route('dashboard');
        }

        $shopIds = $company->shops()->pluck('id');
        $today = Carbon::today();

        $shops = $company->shops()
            ->withCount([
                'queueEntries as waiting_count' => fn($q) => $q->where('status', 'waiting'),
                'queueEntries as in_service_count' => fn($q) => $q->where('status', 'in_service'),
                'staff as active_staff_count' => fn($q) => $q->where('queue_status', 'active'),
            ])
            ->get();

        $todayStats = [
            'total_check_ins' => QueueEntry::whereIn('shop_id', $shopIds)->whereDate('created_at', $today)->count(),
            'completed' => QueueEntry::whereIn('shop_id', $shopIds)->whereDate('created_at', $today)->where('status', 'completed')->count(),
            'no_shows' => QueueEntry::whereIn('shop_id', $shopIds)->whereDate('created_at', $today)->where('status', 'no_show')->count(),
            'total_waiting' => QueueEntry::whereIn('shop_id', $shopIds)->where('status', 'waiting')->count(),
            'revenue' => (float) Appointment::whereIn('shop_id', $shopIds)->whereDate('starts_at', $today)->where('status', 'completed')->sum('price'),
        ];

        // Revenue by location this week
        $weekStart = Carbon::now()->startOfWeek();
        $revenueByLocation = $company->shops()->get()->map(function ($shop) use ($weekStart) {
            return [
                'name' => $shop->name,
                'revenue' => (float) Appointment::where('shop_id', $shop->id)
                    ->where('status', 'completed')
                    ->where('starts_at', '>=', $weekStart)
                    ->sum('price'),
                'check_ins' => QueueEntry::where('shop_id', $shop->id)
                    ->where('created_at', '>=', $weekStart)
                    ->count(),
            ];
        });

        return Inertia::render('Company/Dashboard', [
            'company' => $company,
            'shops' => $shops,
            'todayStats' => $todayStats,
            'revenueByLocation' => $revenueByLocation,
        ]);
    }
}
