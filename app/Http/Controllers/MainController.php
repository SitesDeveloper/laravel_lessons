<?php

namespace App\Http\Controllers;

use App\Models\Sku;
use App\Models\Product;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Services\CurrencyRates;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Http\Requests\SubscriptionRequest;
use App\Http\Requests\ProductFilterRequest;

class MainController extends Controller
{
    
    public function index(ProductFilterRequest $request) 
    {
        $skusQuery = Sku::with(['product','product.category']);
        if ($request->filled("price_from")) {
            //Debugbar::info($request->price_from);
            $skusQuery->where("price",">=",$request->price_from);
        }
        if ($request->filled("price_to")) {
            $skusQuery->where("price","<=",$request->price_to);
        }

        foreach(["hit","new","recommend"] as $field)
            if ($request->has($field)) {
                $skusQuery->whereHas('product', function ($query) use ($field) {
                    $query->$field();
                });

            }

        $skus = $skusQuery->paginate(6)->withPath("?".$request->getQueryString());

        return view('index')->with(compact("skus"));
    }

    public function categories() 
    {
        return view('categories');
    }

    public function category($code=null) 
    {
        $category = Category::where("code",$code)->first();
        return view('category')->with([
            'category' => $category
        ]);
    }

    public function sku($categoryCode, $productCode, Sku $sku) 
    {
        if ($sku->product->code != $productCode) {
            abort(404, 'Product not found');
        }
        if ($sku->product->category->code != $categoryCode) {
            abort(404, 'Category not found');
        }
        
        return view('product', compact('sku'));
    }

    public function subscribe(SubscriptionRequest $request, Sku $sku)
    {
        Subscription::create([
            'email' => $request->email,
            'sku_id' => $sku->id,
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

    public function changeCurrency($currencyCode) {
        $currency = Currency::byCode($currencyCode)->firstOrFail();
        session(['currency' => $currency->code]);
        return redirect()->back();
    }

}
