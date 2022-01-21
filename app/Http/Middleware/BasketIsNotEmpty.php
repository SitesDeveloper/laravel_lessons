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

        $orderId = session("order_id");
        if (!is_null($orderId) && Order::getFullSum()>0) {
            //$order = Order::findOrFail($orderId);
            //if ($order->products->count() > 0) {
                //return $next($request);
            //}

            return $next($request);
        }

        session()->flash("warning", "Ваша корзина пуста");
        return redirect()->route("index");
    }
}
