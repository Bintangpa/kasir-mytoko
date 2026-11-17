<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi Administrator.');
        }

        if (!in_array($user->role, $roles, true)) {
            // Jika role tidak sesuai, arahkan ke dashboard masing-masing dengan notifikasi
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('warning', 'Halaman tersebut khusus untuk Kasir.');
            }

            return redirect()->route('kasir.dashboard')->with('warning', 'Akses ditolak: Anda tidak memiliki izin Administrator.');
        }

        return $next($request);
    }
}
