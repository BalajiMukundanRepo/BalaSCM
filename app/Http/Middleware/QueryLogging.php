<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class QueryLogging
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        if (config('app.debug')) {
            $queries = DB::getQueryLog();
            Log::info('Query Log', ['queries' => $queries, 'count' => count($queries)]);
        }

        return $response;
    }
}
