<?php

namespace App\Models;

use App\Services\CurrencyConversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sku extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["product_id", "count", "price"];
    //protected $visible = ['id', 'count', 'price', 'product_name'];
    protected $hidden  = ['created_at','updated_at', 'deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('count', '>', 0);
    }

    public function getProductNameAttribute()
    {
        return $this->product->name;
    }    

    public function propertyOptions()
    {
        return $this->belongsToMany(PropertyOption::class, 'sku_property_option')->withTimestamps();
    }

    public function isAvailable(){
        return !$this->product->trashed() && $this->count > 0;
    }

    public function getPriceForCount()
    {
        if (!is_null($this->pivot)) 
        {
            return $this->price * $this->pivot->count;
        }

        return $this->price;
    }

    /* получить цену в валюте сессии */
    public function getPriceAttribute($value) {
        return round(CurrencyConversion::convert($value), 2);
    }

    /* получить цену в данной валюте */
    public function getPriceInCurrency(Currency $currency) {
        return round(CurrencyConversion::convert($this->price,  CurrencyConversion::getCurrentCurrencyFromSession()->code, $currency->code), 2);
    }
}
