<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Staff;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        $todaysAppointments = Appointment::with(['customer', 'staff', 'service'])
            ->whereDate('starts_at', $today)
            ->orderBy('starts_at')
            ->get();

        $weekRevenue = Appointment::where('status', 'completed')
            ->where('starts_at', '>=', $weekStart)
            ->sum('price');

        $monthRevenue = Appointment::where('status', 'completed')
            ->where('starts_at', '>=', Carbon::now()->startOfMonth())
            ->sum('price');

        $topStylist = Staff::withCount(['appointments' => function ($q) use ($weekStart) {
            $q->where('status', 'completed')->where('starts_at', '>=', $weekStart);
        }])->orderByDesc('appointments_count')->first();

        $recentCustomers = Customer::orderByDesc('last_visit')->take(5)->get();

        // Revenue by day for last 7 days
        $revenueByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueByDay[] = [
                'date' => $date->format('M d'),
                'revenue' => (float) Appointment::where('status', 'completed')
                    ->whereDate('starts_at', $date)
                    ->sum('price'),
            ];
        }

        $stats = [
            'todaysAppointmentCount' => $todaysAppointments->count(),
            'weekRevenue' => (float) $weekRevenue,
            'monthRevenue' => (float) $monthRevenue,
            'totalCustomers' => Customer::count(),
            'pendingAppointments' => Appointment::whereIn('status', ['pending', 'confirmed'])->where('starts_at', '>=', now())->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'todaysAppointments' => $todaysAppointments,
            'topStylist' => $topStylist,
            'recentCustomers' => $recentCustomers,
            'revenueByDay' => $revenueByDay,
        ]);
    }
}
