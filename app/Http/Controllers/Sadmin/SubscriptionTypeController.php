<?php

namespace App\Http\Controllers\Sadmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionType;
use Illuminate\Http\Request;

class SubscriptionTypeController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:255', 'unique:subscription_types,type'],
        ]);

        SubscriptionType::create([
            'type' => $validated['type'],
            'status' => 'active',
        ]);

        return back()->with('success', "Type d'abonnement créé : {$validated['type']}");
    }

    public function destroy(SubscriptionType $subscriptionType)
    {
        $subscriptionType->delete();

        return back()->with('success', 'Type d\'abonnement supprimé avec succès.');
    }
}

