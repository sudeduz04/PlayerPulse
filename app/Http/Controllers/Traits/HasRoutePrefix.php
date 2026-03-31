<?php

namespace App\Http\Controllers\Traits;

trait HasRoutePrefix
{
    private function routePrefix(): string
    {
        $user = auth()->user();

        if ($user->isRole('super_admin')) {
            return 'super_admin';
        }

        return $user->isRole('manager') ? 'manager' : 'coach';
    }
}
