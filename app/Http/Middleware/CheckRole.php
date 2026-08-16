<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * route मा 'role:perm1,perm2' यसरी comma-separated multiple permission दिन मिल्छ —
     * तीमध्ये ANY एउटा भए पनि access मिल्छ (OR logic)।
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!auth()->check()) {
            abort(403, 'माफ गर्नुहोस्, यो page हेर्न login चाहिन्छ।');
        }

        $role = auth()->user()->role;

        // Admin लाई जहिले पनि full access - bypass
        if ($role === 'admin') {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            $hasPermission = RolePermission::where('role', $role)
                                ->where('permission', trim($permission))
                                ->exists();
            if ($hasPermission) {
                return $next($request);
            }
        }

        abort(403, 'माफ गर्नुहोस्, यो feature प्रयोग गर्ने अधिकार तपाईंको role लाई छैन।');
    }
}