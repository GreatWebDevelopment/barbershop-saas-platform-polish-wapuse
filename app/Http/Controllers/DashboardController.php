<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // === Stats Cards ===
        $todaysRevenue = (float) Appointment::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereDate('starts_at', $today)
            ->sum('price');

        $todaysAppointments = Appointment::with(['customer', 'staff', 'service'])
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $todayBreakdown = [
            'total' => $todaysAppointments->count(),
            'completed' => $todaysAppointments->where('status', 'completed')->count(),
            'upcoming' => $todaysAppointments->whereIn('status', ['pending', 'confirmed'])->count(),
            'cancelled' => $todaysAppointments->where('status', 'cancelled')->count(),
        ];

        $newCustomersThisMonth = Customer::where('created_at', '>=', $monthStart)->count();

        // === Revenue Chart (last 30 days) ===
        $revenueByDay = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueByDay[] = [
                'date' => $date->format('M d'),
                'revenue' => (float) Appointment::where('status', 'completed')
                    ->whereDate('starts_at', $date)
                    ->sum('price'),
            ];
        }

        // Revenue by payment method
        $revenueByMethod = Appointment::where('status', 'completed')
            ->where('starts_at', '>=', $monthStart)
            ->select('payment_method', DB::raw('SUM(price) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->payment_method ?? 'unknown' => [
                    'total' => (float) $row->total,
                    'count' => $row->count,
                ],
            ]);

        // === Stylist Performance ===
        $staffMembers = Staff::where('status', 'active')->get();
        $stylistPerformance = $staffMembers->map(function ($staff) use ($monthStart, $lastMonthStart, $lastMonthEnd) {
            $thisMonthAppts = Appointment::where('staff_id', $staff->id)
                ->where('status', 'completed')
                ->where('starts_at', '>=', $monthStart)
                ->get();

            $lastMonthAppts = Appointment::where('staff_id', $staff->id)
                ->where('status', 'completed')
                ->whereBetween('starts_at', [$lastMonthStart, $lastMonthEnd])
                ->get();

            $thisMonthRevenue = $thisMonthAppts->sum('price');
            $lastMonthRevenue = $lastMonthAppts->sum('price');

            $avgServiceMinutes = $thisMonthAppts->count() > 0
                ? $thisMonthAppts->avg(fn ($a) => $a->ends_at && $a->starts_at
                    ? Carbon::parse($a->ends_at)->diffInMinutes(Carbon::parse($a->starts_at))
                    : 0)
                : 0;

            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'avatar_url' => $staff->avatar_url,
                'appointments_completed' => $thisMonthAppts->count(),
                'last_month_appointments' => $lastMonthAppts->count(),
                'revenue' => (float) $thisMonthRevenue,
                'last_month_revenue' => (float) $lastMonthRevenue,
                'avg_service_minutes' => round($avgServiceMinutes),
                'commission_earned' => round($thisMonthRevenue * ($staff->commission_percent / 100), 2),
            ];
        })->sortByDesc('revenue')->values();

        // === Popular Services ===
        $popularServices = Appointment::where('status', 'completed')
            ->where('starts_at', '>=', $monthStart)
            ->select('service_id', DB::raw('COUNT(*) as booking_count'), DB::raw('SUM(price) as total_revenue'))
            ->groupBy('service_id')
            ->orderByDesc('booking_count')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $service = Service::find($row->service_id);
                return [
                    'name' => $service?->name ?? 'Unknown',
                    'booking_count' => $row->booking_count,
                    'revenue' => (float) $row->total_revenue,
                ];
            });

        // === Peak Hours Heatmap ===
        // 7 days x hours (7am-9pm)
        $heatmapData = [];
        $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        // Use database-agnostic approach instead of MySQL-specific DAYOFWEEK
        $appointments = Appointment::whereIn('status', ['completed', 'confirmed', 'pending'])
            ->where('starts_at', '>=', Carbon::now()->subWeeks(4))
            ->select('starts_at')
            ->get();

        $appointmentCounts = collect();
        foreach ($appointments as $apt) {
            $date = Carbon::parse($apt->starts_at);
            $dow = $date->dayOfWeekIso; // 1=Mon, 7=Sun
            $hour = $date->hour;

            $key = "{$dow}_{$hour}";
            if (!$appointmentCounts->has($key)) {
                $appointmentCounts->put($key, (object)['dow' => $dow, 'hour' => $hour, 'count' => 0]);
            }
            $appointmentCounts->get($key)->count++;
        }

        foreach ($dayNames as $dayIndex => $dayName) {
            $dayData = [];
            for ($h = 7; $h <= 21; $h++) {
                $dow = $dayIndex + 1; // 1=Mon, 7=Sun
                $match = $appointmentCounts->get("{$dow}_{$h}");
                $dayData[] = $match ? $match->count : 0;
            }
            $heatmapData[] = [
                'day' => $dayName,
                'hours' => $dayData,
            ];
        }

        // === Customer Metrics ===
        $totalCustomers = Customer::count();
        $newThisMonth = $newCustomersThisMonth;
        $customersWithMultipleVisits = Customer::where('visit_count', '>', 1)->count();
        $returningRate = $totalCustomers > 0
            ? round(($customersWithMultipleVisits / $totalCustomers) * 100, 1)
            : 0;

        $topCustomers = Customer::orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'name' => $c->first_name . ' ' . $c->last_name,
                'total_spent' => (float) $c->total_spent,
                'visit_count' => $c->visit_count,
                'loyalty_points' => $c->loyalty_points,
            ]);

        $stats = [
            'todaysRevenue' => $todaysRevenue,
            'todayBreakdown' => $todayBreakdown,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'monthRevenue' => (float) Appointment::where('status', 'completed')
                ->where('starts_at', '>=', $monthStart)
                ->sum('price'),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'todaysAppointments' => $todaysAppointments,
            'revenueByDay' => $revenueByDay,
            'revenueByMethod' => $revenueByMethod,
            'stylistPerformance' => $stylistPerformance,
            'popularServices' => $popularServices,
            'heatmapData' => $heatmapData,
            'customerMetrics' => [
                'total' => $totalCustomers,
                'newThisMonth' => $newThisMonth,
                'returningRate' => $returningRate,
                'topCustomers' => $topCustomers,
            ],
        ]);
    }
}
