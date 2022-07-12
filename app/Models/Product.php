<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use App\Services\CurrencyConversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    //protected $table = "product";

    protected $fillable = [
        'name', 'code', 'price', 'category_id', 'description', 'image', 
        'new', 'hit','recommend', 'count',
        "name_en","description_en"
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    //TODO: check table name and fields
    public function properties()
    {
        return $this->belongsToMany(Property::class);
    }


    public function getPriceForCount()
    {
        if (!is_null($this->pivot)) 
        {
            return $this->price * $this->pivot->count;
        }

        return $this->price;
    }

    public function scopeHit($query) {
        return $query->where("hit",1);
    }

    public function scopeNew($query) {
        return $query->where("new",1);
    }

    public function scopeRecommend($query) {
        return $query->where("recommend",1);
    }

    public function scopeByCode($query, $code) {
        return $query->where("code", $code);
    }

    public function setNewAttribute($value) {
        $this->attributes["new"] = ($value==="on") ? 1 : 0;
        //dd($this->attributes["new"]);
    }

    public function setHitAttribute($value) {
        $this->attributes["hit"] = ($value==="on") ? 1 : 0;
    }

    public function setRecommendAttribute($value) {
        $this->attributes["recommend"] = ($value==="on") ? 1 : 0;
    }

  
    public function isAvailable(){
        return !$this->trashed() && $this->count > 0;
    }

    public function isNew() {
        return $this->new == 1;
    }

    public function isHit() {
        return $this->hit == 1;
    }

    public function isRecommend() {
        return $this->recommend == 1;
    }

    public function getPriceAttribute($value) {
        return round(CurrencyConversion::convert($value), 2);
    }




}
