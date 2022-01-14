<?php

namespace App\Http\Controllers\Person;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    
    public function index()
    {
        $orders = Auth::user()->orders()->where('status', 1)->paginate(10);
        //dd($orders);
        return view('auth.orders.index', compact("orders"));
    }

    public function show(Order $order) {
        if (!Auth::user()->orders->contains($order) ) {
            return back();
        }
        return view('auth.orders.show', compact("order") );
    }
}
