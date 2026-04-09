<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoyaltyReward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileLoyaltyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $customers = Customer::withoutGlobalScopes()
            ->where('email', $user->email)
            ->get(['id', 'shop_id', 'loyalty_points', 'total_spent', 'first_name', 'last_name']);

        $shopIds = $customers->pluck('shop_id');

        $rewards = LoyaltyReward::withoutGlobalScopes()
            ->whereIn('shop_id', $shopIds)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'customers' => $customers,
            'rewards' => $rewards,
            'total_points' => $customers->sum('loyalty_points'),
        ]);
    }
}
