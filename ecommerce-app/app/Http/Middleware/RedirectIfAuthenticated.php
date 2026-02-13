<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Redirige según el guard autenticado. Si la petición trae ?redirect= (ej. /login?redirect=/checkout)
     * y la URL es segura, se usa esa en lugar de HOME para que el usuario no pierda el destino.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $redirect = ($guard === 'admin')
                    ? route('admin.dashboard')
                    : (($guard === 'vendor')
                        ? route('vendor.dashboard')
                        : RouteServiceProvider::HOME);

                // Si ya está autenticado pero llegó con ?redirect= (ej. desde enlace /login?redirect=/checkout)
                $intended = $request->query('redirect');
                if ($intended && Str::startsWith($intended, '/') && ! Str::startsWith($intended, '//')) {
                    $redirect = $intended;
                }

                return redirect($redirect);
            }
        }

        return $next($request);
    }
}
