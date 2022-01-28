<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\ProductFilterRequest;

class MainController extends Controller
{
    
    public function index(ProductFilterRequest $request) 
    {
        //Debugbar::info($request);
        //Log::channel("single")->debug($request->ip());
        $productsQuery = Product::with("category"); // query();
        //Debugbar::info($request->has("price_from"));
        if ($request->filled("price_from")) {
            //Debugbar::info($request->price_from);
            $productsQuery->where("price",">=",$request->price_from);
        }
        if ($request->filled("price_to")) {
            $productsQuery->where("price","<=",$request->price_to);
        }

        foreach(["hit","new","recommend"] as $field)
            if ($request->has($field)) {
                $productsQuery->$field();
                //$productsQuery->where($field,1);

            }

        $products = $productsQuery->paginate(6)->withPath("?".$request->getQueryString());
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

    public function product($category, $productCode) 
    {
        $product = Product::withTrashed()->byCode($productCode)->firstOrFail();
        return view('product')->with([
            'category' => $category,
            'product' => $product
        ]);
    }

    public function subscribe(SubscriptionRequest $request, Product $product)
    {
        Subscription::create([
            'email' => $request->email,
            'product_id' => $product->id,
        ]);

        return redirect()->back()->with('success', __('product.we_will_update'));
    }

    public function changeLocale($locale) {
        $availableLocales = ['ru', 'en'];
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.locale');
        }
        session(['locale' => $locale]);
        App::setLocale($locale);
        return redirect()->back();
    }
}
