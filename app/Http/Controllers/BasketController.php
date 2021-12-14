<?php

namespace App\Http\Controllers;

use App\Models\Order;

class BasketController extends Controller
{

    public function basket()
    {
        $orderId = session("order_id");
        $order = null;
        if (!is_null($orderId)) {
            $order = Order::findOrFail($orderId);
        }

        return view('basket', compact('order'));
    }

    public function basketPlace()
    {

        return view('order');
    }

    public function basketAdd($productId)
    {
        $orderId = session("order_id");
        if (is_null($orderId)) {
            $order = Order::create();
            session(["order_id" => $order->id]);
        } else {
            $order = Order::find($orderId);
        }

        if ($order->products->contains($productId)) {
            $pivotRow = $order->products()->where('product_id', $productId)->first()->pivot;
            $pivotRow->count++;
            $pivotRow->Update();
        } else {
            $order->products()->attach($productId);
        }

        return redirect()->route("basket");
    }

    public function basketRemove($productId)
    {
        $orderId = session("order_id");
        if (!is_null($orderId)) {
            $order = Order::find($orderId);
            if ($order->products->contains($productId)) {
                $pivotRow = $order->products()->where('product_id', $productId)->first()->pivot;
                if ($pivotRow->count < 2) {
                    $order->products()->detach($productId);
                } else {
                    $pivotRow->count--;
                    $pivotRow->Update();
                }
            }
        }

        return redirect()->route("basket");
    }
}
