<?php

namespace App\Http\Controllers\Api;

use App\Models\Sku;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SkusController extends Controller
{
    public function getSkus()
    {
        //header('Content-type: application/json');
        return Sku::with('product')
            ->available()
            ->get()
            ->append('product_name') 
            //->toJson(JSON_PRETTY_PRINT);
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
