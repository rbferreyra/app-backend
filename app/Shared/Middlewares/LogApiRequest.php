<?php

namespace App\Shared\Middlewares;

use App\Shared\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('request_logs.enabled')) {
            return $next($request);
        }

        if ($this->isExcluded($request)) {
            return $next($request);
        }

        $startTime = microtime(true);

        $response = $next($request);

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        $this->log($request, $response, $responseTime);

        return $response;
    }

    private function log(Request $request, Response $response, int $responseTime): void
    {
        try {
            $user = Auth::user();

            RequestLog::create([
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => optional($request->route())->getName(),
                'status' => $response->getStatusCode(),
                'response_time_ms' => $responseTime,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_type' => $user ? get_class($user) : null,
                'user_id' => $user?->id,
                'payload' => $this->sanitizePayload($request->except([])),
                'response' => $this->sanitizeResponse($response),
            ]);
        } catch (Throwable) {
            // Nunca deixar o log quebrar a request
        }
    }

    private function sanitizePayload(array $payload): ?array
    {
        if (empty($payload)) {
            return null;
        }

        $masked = config('request_logs.masked_fields', []);

        array_walk_recursive($payload, function (&$value, $key) use ($masked) {
            if (in_array($key, $masked, true)) {
                $value = '***';
            }
        });

        $encoded = json_encode($payload);

        if (strlen($encoded) > config('request_logs.max_body_length')) {
            return ['_truncated' => true];
        }

        return $payload;
    }

    private function sanitizeResponse(Response $response): ?array
    {
        $status = $response->getStatusCode();
        $isError = $status >= 400;

        // Salva response obrigatoriamente em erros, opcional em sucesso
        if (! $isError && ! config('request_logs.log_response_body')) {
            return null;
        }

        $content = $response->getContent();

        if (! $content) {
            return null;
        }

        if (strlen($content) > config('request_logs.max_body_length')) {
            return ['_truncated' => true, '_status' => $status];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function isExcluded(Request $request): bool
    {
        $path = $request->path();

        foreach (config('request_logs.excluded_routes', []) as $pattern) {
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
