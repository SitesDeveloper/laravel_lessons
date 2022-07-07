<?php

namespace App\Classes;

use App\Models\Order;
use App\Models\Product;
use App\Mail\OrderCreated;
use App\Services\CurrencyConversion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Basket
{
    protected $order;

    /**
     * Basket constructor.
     * @param  bool  $createOrder
     */
    public function __construct($createOrder = false)
    {
        //session()->forget('order');        
        $order = session('order');

        if (is_null($order) && $createOrder) {
            $data = [];
            if (Auth::check()) {
                $data['user_id'] = Auth::id();
            }
            $data['currency_id'] = CurrencyConversion::getCurrentCurrencyFromSession()->id;

            $this->order = new Order($data);
            session(['order' => $this->order]);
        } else {
            $this->order = $order;
        }
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function countAvailable($updateCount = false)
    {
        $products = collect([]);
        foreach ($this->order->products as $orderProduct)
        {
            $product = Product::find($orderProduct->id);
            if ($orderProduct->countInOrder > $product->count) {
                return false;
            }
            if ($updateCount) {
                $product->count -= $orderProduct->countInOrder;
                $products->push($product);
            }
        }

        if ($updateCount) {
            $products->map->save();
        }

        return true;
    }

    public function saveOrder($name, $phone, $email)
    {
        if (!$this->countAvailable(true)) {
            return false;
        }
        $this->order->saveOrder($name, $phone);
        Mail::to($email)->send(new OrderCreated($name, $this->getOrder()));

        return true;
    }

    public function removeProduct(Product $product)
    {
        if ($this->order->products->contains($product)) {
            $pivotRow = $this->order->products->where('id', $product->id)->first();
            if ($pivotRow->countInOrder < 2) {
                //dd($product, $this->order->products);
                $indx =  $this->order->products->search($pivotRow);
                //dd();
                $this->order->products->pull($indx); //$product
                
            } else {
                $pivotRow->countInOrder--;
            }
        }
    }

    public function addProduct(Product $product)
    {

        if ($this->order->products->contains($product)) {
            $pivotRow = $this->order->products->where('id', $product->id)->first();
            if ($pivotRow->countInOrder >= $product->count) {
                return false;
            }
            $pivotRow->countInOrder++;
        } else {
            if ($product->count == 0) {
                return false;
            }
            $product->countInOrder = 1;
            $this->order->products->push($product);
        }

        return true;
    }
}