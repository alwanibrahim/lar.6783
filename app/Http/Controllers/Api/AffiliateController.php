<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function commissions(Request $request)
    {
        $commissions = AffiliateCommission::where('referrer_id', $request->user()->id)
            ->with(['referred', 'deposit'])
            ->latest()
            ->paginate(15);

        return response()->json($commissions);
    }

    public function referrals(Request $request)
    {
        $referrals = $request->user()
            ->referredUsers()
            ->with(['deposits'])
            ->latest()
            ->paginate(15);

        return response()->json($referrals);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        
        $totalReferrals = $user->referredUsers()->count();
        $totalCommissions = $user->commissions()->sum('commission_amount');
        $thisMonthCommissions = $user->commissions()
            ->whereMonth('created_at', now()->month)
            ->sum('commission_amount');

        return response()->json([
            'referral_code' => $user->referral_code,
            'total_referrals' => $totalReferrals,
            'total_commissions' => $totalCommissions,
            'this_month_commissions' => $thisMonthCommissions,
        ]);
    }
}
