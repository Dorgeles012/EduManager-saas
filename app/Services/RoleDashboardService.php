<?php

namespace App\Services;

use App\Models\User;

class RoleDashboardService
{
    public function routeNameFor(?User $user): ?string
    {
        return match (strtolower(trim((string) $user?->role))) {
            'sadmin' => 'sadmin.dashboard',
            'client' => 'client.dashboard',
            'personnel' => 'personnel.dashboard',
            'enseignant' => 'enseignant.dashboard',
            'parent' => 'parent.dashboard',
            default => null,
        };
    }
}
