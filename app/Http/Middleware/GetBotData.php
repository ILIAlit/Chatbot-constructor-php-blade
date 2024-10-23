<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetBotData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    private $pattern = '/\/api\/telegram\/([^\/]+)\/webhook-bot-flow/';
    
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->url();

		if (preg_match($this->pattern, $url, $matches)) {
            $token = $matches[1];
			$request->merge([
                'token' => $token,
            ]);
        } else {
            return response()->json(['message' => 'Токен не найден']);
        }

        return $next($request);
    }
}