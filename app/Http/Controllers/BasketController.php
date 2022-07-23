<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use App\Models\Order;
use App\Classes\Basket;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function basket()
    {
        //dd('basekt');
        $order = (new Basket())->getOrder();
        return view('basket', compact('order'));
    }

    public function basketConfirm(Request $request)
    {
        $email = Auth::check() ? Auth::user()->email : $request->email;
        if ((new Basket())->saveOrder($request->name, $request->phone, $email)) {
            session()->flash('success', __('basket.you_order_confirmed'));
        } else {
            session()->flash('warning', __('basket.you_cant_order_more'));
        }

        return redirect()->route('index');
    }

    public function basketPlace()
    {
        $basket = new Basket();
        $order = $basket->getOrder();
        if (!$basket->countAvailable()) {
            session()->flash('warning', __('basket.you_cant_order_more'));
            return redirect()->route('basket');
        }
        return view('order', compact('order'));
    }

    public function basketAdd(Sku $sku)
    {
        $result = (new Basket(true))->addSku($sku);

        if ($result) {
            session()->flash('success', __('basket.added').$sku->product->name);
        } else {
            session()->flash('warning', $sku->product->name . __('basket.not_available_more'));
        }

        return redirect()->route('basket');
    }

    public function basketRemove(Sku $sku)
    {
        (new Basket())->removeSku($sku);

        session()->flash('warning', __('basket.removed').$sku->product->name);

        return redirect()->route('basket');
    }
}