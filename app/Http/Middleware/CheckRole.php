<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            abort(403, 'माफ गर्नुहोस्, यो page हेर्न login चाहिन्छ।');
        }

        $role = auth()->user()->role;

        // Admin lai jahile pani full access - bypass
        if ($role === 'admin') {
            return $next($request);
        }

        $hasPermission = RolePermission::where('role', $role)
                            ->where('permission', $permission)
                            ->exists();

        if (!$hasPermission) {
            abort(403, 'माफ गर्नुहोस्, यो feature प्रयोग गर्ने अधिकार तपाईंको role लाई छैन।');
        }

        return $next($request);
    }
}