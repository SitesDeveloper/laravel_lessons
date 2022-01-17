<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function basketAdd($productId)
    {
        $orderId = session("order_id");
        if (is_null($orderId)) {
            $order = Order::create();
            session(["order_id" => $order->id]);
        } else {
            $order = Order::find($orderId);
        }

        $product = Product::find($productId);
        if ($order->products->contains($productId)) {
            $pivotRow = $order->products()->where('product_id', $productId)->first()->pivot;
            $pivotRow->count++;
            $pivotRow->Update();
            session()->flash("success","Товар ".$product->name." кол-во увеличено");
        } else {
            $order->products()->attach($productId);
            session()->flash("success","Добавлен товар ".$product->name);
        }
        Order::changeFullSum($product->price);

        if (Auth::check()) {
            $order->user_id = Auth::id();
            $order->save();
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
                $product = Product::find($productId);
                                
                if ($pivotRow->count < 2) {
                    $order->products()->detach($productId);
                    session()->flash("warning","Товар ".$product->name." удален из корзины");
                } else {
                    $pivotRow->count--;
                    session()->flash("warning","Товар ".$product->name." кол-во уменьшено");
                    $pivotRow->Update();
                }
                Order::changeFullSum(-$product->price);
            }
        }

        return redirect()->route("basket");
    }

    public function basketPlace()
    {
        $orderId = session("order_id");
        if (is_null($orderId)) {
            return redirect()->route("index");
        }
        $order = Order::find($orderId);

        return view('order', compact("order"));
    }

    public function basketConfirm(Request $request)
    {
        //dd($request->name);
        $orderId = session("order_id");
        if (is_null($orderId)) {
            return redirect()->route("index");
        }
        $order = Order::find($orderId);
        $isOk = $order->saveOrder($request->name, $request->phone);
        if ($isOk) {
            session()->flash("success", "Заказ создан.");
            Order::eraseFullSum();
        } else {
            session()->flaash("warning", "Ошибка при сохранении заказа.");
        }

        return redirect()->route("index");
    }

}
