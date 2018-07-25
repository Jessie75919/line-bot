<?php

namespace App\Http\Middleware;

use App\Http\Requests\ProductRequest;
use Closure;
use function dd;
use Illuminate\Support\Collection;
use \Illuminate\Http\Request ;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $inputs = $this->sanitizeInput($request)->all();
        $request->replace($inputs);
        return $next($request);
    }


    /**
     * @param ProductRequest|Request $request
     * @return Collection
     */
    private function sanitizeInput(Request $request): Collection
    {
        return collect($request->all())->map(function ($item) {
            if (is_string($item)) {
                return strip_tags($item);
            }
            return $item;
        });
    }
}
