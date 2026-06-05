<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonnementSubscribeController extends Controller
{
    public function store(Request $request, SubscriptionService $subscriptionService): JsonResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'methode_paiement' => ['required', 'string', 'max:100'],
            'reference_transaction' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $result = $subscriptionService->subscribe(
            user: $user,
            planId: (int) $validated['plan_id'],
            methodePaiement: (string) $validated['methode_paiement'],
            referenceTransaction: (string) $validated['reference_transaction']
        );

        /** @var array{message:string, subscription:Subscription, payment:Payment} $result */
        return response()->json([
            'message' => $result['message'],
            'subscription' => $result['subscription'],
            'payment' => $result['payment'],
        ]);
    }
}

