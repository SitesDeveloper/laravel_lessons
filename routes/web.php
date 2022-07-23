<?php
namespace App;

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\Person\OrderController as PersonOrderController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//Route::resource('home', HomeController::class);
//Route::get('/home', 'HomeController@index');

Auth::routes([
    "reset" => false,
    "confirm" => false,
    "verify" => false,
]);

Route::get('locale/{locale}', [MainController::class, 'changeLocale'])->name('locale');
Route::get('currency/{currencyCode}', 'MainController@changeCurrency')->name('currency');

Route::get('/logout', [LoginController::class, 'logout'])->name('get-logout');
Route::get('/reset', [ResetController::class, 'reset'])->name('reset');

Route::middleware((["set_locale"]))->group(function () {

    Route::middleware(["auth"])->group(function () {
        Route::group([
            "namespace" => "Person",
            "prefix" => "person",
            "as" => "person.",
        ], function () {
            Route::get('/orders', [PersonOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [PersonOrderController::class, 'show'])->name('orders.show');
        });

        Route::group([
            "namespace" => "Admin",
            "prefix" => "admin",
        ], function () {
            Route::group(["middleware" => "is_admin"], function () {
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('home');
                Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            });

            Route::resource("categories", "CategoryController");
            Route::resource("products", "ProductController");
            Route::resource('products/{product}/skus', 'SkuController');
            Route::resource("properties", "PropertyController");
            Route::resource("properties/{property}/property-options", "PropertyOptionController");
        });

    });

    Route::get('/', [MainController::class, 'index'])->name('index');
    Route::get('/categories', [MainController::class, 'categories'])->name('categories');
    Route::post('/subscription/{sku}', 'MainController@subscribe')->name('subscription');

    Route::group([
        "prefix" => "basket",
    ], function () {
        Route::post('/add/{sku}', [BasketController::class, 'basketAdd'])->name('basket-add');
        Route::group([
            "middleware" => "basket_not_empty",
        ], function () {
            Route::get('/', [BasketController::class, 'basket'])->name('basket');
            Route::post('/remove/{sku}', [BasketController::class, 'basketRemove'])->name('basket-remove');
            Route::get('/place', [BasketController::class, 'basketPlace'])->name('basket-place');
            Route::post('/place', [BasketController::class, 'basketConfirm'])->name('basket-confirm');
        });
    });

    Route::get('/{category}', [MainController::class, 'category'])->name('category');
    Route::get('/{category}/{product}/{sku}', [MainController::class, 'sku'])->name('sku');

});
