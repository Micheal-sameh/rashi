<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwaggerBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->getUser();
        $password = $request->getPassword();

        $validUser = config('l5-swagger-auth.username');
        $validPassword = config('l5-swagger-auth.password');

        if ($user === $validUser && $password === $validPassword) {
            return $next($request);
        }

        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Swagger Documentation"',
        ]);
    }
}
