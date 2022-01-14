<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\ProductFilterRequest;

class MainController extends Controller
{
    
    public function index(ProductFilterRequest $request) 
    {
        //dd(get_class_methods($request));
        $productsQuery = Product::query();
        if ($request->filled("price_from")) {
            $productsQuery->where("price",">=",$request->price_from);
        }
        if ($request->filled("price_to")) {
            $productsQuery->where("price","<=",$request->price_to);
        }

        foreach(["hit","new","recomend"] as $field)
            if ($request->has($field)) {
                $productsQuery->where($field,1);
            }

        $products = $productsQuery->paginate(3)->withPath("?".$request->getQueryString());
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
