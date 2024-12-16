<?php namespace Pi\Livestream\Middlewares;

use Closure;

class CheckAccessKey
{
    public function handle($request, Closure $next)
    {
        // Check if the access key is set
        if (!config('pi.livestream::config.access_key')) {
            return response()->json(
                [
                    "message" => "Access key has not bene set, please assign a value to the environment variable 'ALABAPAY_LIVESTREAM_KEY'",
                    "success" => false,
                 ],
                400
            );
        }

        return $next($request);
    }
}
