<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MainController extends Controller
{
    
    public function index() 
    {
        $products = Product::get();
        return view('index')->with(['products'=>$products]);
    }

    public function categories() 
    {
        $categories = Category::get();
        return view('categories')->with(['categories' => $categories]);
    }

    public function category($code=null) 
    {
        $category = Category::where("code",$code)->first();
        return view('category')->with([
            'category' => $category
        ]);
    }

    public function product($category, $product=null) 
    {
        $product = Product::where("code", $product)->first();
        return view('product')->with([
            'category' => $category,
            'product' => $product
        ]);
    }


}
