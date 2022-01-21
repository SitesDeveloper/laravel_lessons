<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Basket;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;

class BasketController extends Controller
{

    public function basket()
    {
        $order = (new Basket())->getOrder();

        return view('basket', compact('order'));
    }

    public function basketConfirm(Request $request)
    {
        if ( (new Basket())->saveOrder($request->name, $request->phone) ) {
            Order::eraseFullSum();
        } 

        return redirect()->route("index");
    }    


    public function basketPlace()
    {
        $basket = new Basket();
        $order = $basket->getOrder();
        if (!$basket->isCountAvailable()) {
            session()->flash("warning", "Недостаточное кол-во товара на складе.");
            return redirect()->route("basket");
        }

        return view('order', compact("order"));
    }



    public function basketAdd(Product $product)
    {
        (new Basket(true))->addProduct($product);

        return redirect()->route("basket");
    }

    public function basketRemove(Product $product)
    {
        (new Basket())->removeProduct($product);

        return redirect()->route("basket");
    }

    

}
