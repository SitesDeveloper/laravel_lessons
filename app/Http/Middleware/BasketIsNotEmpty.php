<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facades\Debugbar;

class BasketIsNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $orderId = session("orderId");
        if (!is_null($orderId) && Order::getFullSum()>0) {
            return $next($request);
        }

        session()->flash('warning', __('basket.cart_is_empty'));
        return redirect()->route("index");
    }
}
