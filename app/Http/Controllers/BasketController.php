<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use App\Models\Order;
use App\Models\Coupon;
use App\Classes\Basket;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\CouponRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddCouponRequest;

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
        $basket = new Basket();
        if ($basket->getOrder()->hasCoupon() && !$basket->getOrder()->coupon->availableForUse()) {
            $basket->clearCoupon();
            session()->flash('warning', __('basket.coupon.not_available'));
            return redirect()->route('basket');
        }

        $email = Auth::check() ? Auth::user()->email : $request->email;
        if ($basket->saveOrder($request->name, $request->phone, $email)) {
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
            session()->flash('warning', $sku->product->name.__('basket.not_available_more'));
        }

        return redirect()->route('basket');
    }

    public function basketRemove(Sku $sku)
    {
        (new Basket())->removeSku($sku);

        session()->flash('warning', __('basket.removed').$sku->product->name);

        return redirect()->route('basket');
    }

    public function setCoupon(AddCouponRequest $request) {
        $coupon = Coupon::where('code', $request->coupon)->first();

        if ($coupon->availableForUse()) {
            (new Basket())->setCoupon($coupon);
            session()->flash('success', __('basket.coupon.coupon_added'));
        } else {
            session()->flash('warning', __('basket.coupon.not_available'));
        }

        return redirect()->route('basket');

    }

}