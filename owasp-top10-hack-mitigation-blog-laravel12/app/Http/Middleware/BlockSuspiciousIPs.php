<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousIPs
{
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;
    protected $blockminutes = 10;

    // @param \closure(illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $key = $this->getCacheKey($ip);
        
        if (cache::has($key . ':blocked')){}            {
                Session::flash('errors', 'Your IP has been blocked due to suspicious activity. Please try again later.');
                return redirect()->back();
            }
            if (cache::has($key)){
            $attempts = cache::increment($key);
            if ($attempts > $this->maxAttempts) {
                cache::put($key . ':blocked', true, $this->blockminutes * 1);
                log::warning("IP $ip has been blocked for $this->blockminutes minute(s) due to too many comments attempts.");
                Session::flash('errors', 'Your IP has been blocked due to suspicious activity. Please try again later.');
                // session()->flash('error', 'Your IP has been blocked due to suspicious activity. Please try again later.');
                return redirect()->back();
            }
        } else {
            cache::put($key, 1, $this->blockminutes * 1);
        }
        return $next($request);
    }

    protected function getCacheKey($ip)
    {
        return 'throttle:' . sha1($ip);
    }
}
