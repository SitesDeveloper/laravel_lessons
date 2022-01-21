<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Basket extends Model
{
    //use HasFactory;

    protected $order; 

    public function __construct($createOrder = false)
    {
        $orderId = session("order_id");
        if (is_null($orderId) && $createOrder) {
            $data = [];
            if (Auth::check()) {
                $data['user_id'] = Auth::id();
            }
            $this->order = Order::create($data);
            session(["order_id" => $this->order->id]);
        } else {
            $this->order = Order::findOrFail($orderId);  
        }
    } 

    public function getOrder() {
        return $this->order;
    }

    public function saveOrder($name, $phone) {

        if (!$this->isCountAvailable()) {
            session()->flash("warning", "Недостаточное кол-во товара.");
            return false;
        }
        $res = $this->order->saveOrder($name, $phone);
        if ($res) {
            session()->flash("success", "Заказ создан.");
        } else {
            session()->flash("warning", "Ошибка при сохранении заказа.");
        }

        return $res;
    }

    public function isCountAvailable() {
        foreach ($this->order->products as $orderProduct) {
            if ($orderProduct->count < $this->getPivotRow($orderProduct)->count)
                return false;
        }
        return true;
    }


    protected function getPivotRow(Product $product) {
        return $this->order->products()->where('product_id', $product->id)->first()->pivot;
    }


    public function removeProduct(Product $product) {
        if ($this->order->products->contains($product->id)) {
            $pivotRow = $this->getPivotRow($product); 
            if ($pivotRow->count < 2) {
                $this->order->products()->detach($product->id);
                session()->flash("warning","Товар ".$product->name." удален из корзины");
            } else {
                $pivotRow->count--;
                session()->flash("warning","Товар ".$product->name." кол-во уменьшено");
                $pivotRow->Update();
            }
            Order::changeFullSum(-$product->price);
        }
    }

    public function addProduct(Product $product) {

        if ($this->order->products->contains($product->id)) {
            $pivotRow = $this->getPivotRow($product); 
            $pivotRow->count++;
            if ($pivotRow->count > $product->count) {
                session()->flash("warning","Товара ".$product->name." на складе нехватает, ограничено " .$product->count." шт");    
                return false;
            }
            $pivotRow->Update();
            session()->flash("success","Товар ".$product->name." кол-во увеличено");
        } else {
            $this->order->products()->attach($product->id);
            session()->flash("success","Добавлен товар ".$product->name);
        }
        Order::changeFullSum($product->price);
        return true;
    }
}
