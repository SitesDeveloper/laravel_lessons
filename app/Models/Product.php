<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    //protected $table = "product";

    protected $fillable = [
        'name', 'code', 'price', 'category_id', 'description', 'image', 
        'new', 'hit','recomend'
    ];

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

    public function getPriceForCount()
    {
        if (!is_null($this->pivot)) 
        {
            return $this->price * $this->pivot->count;
        }

        return $this->price;
    }

    public function setNewAttribute($value) {
        $this->attributes["new"] = ($value==="on") ? 1 : 0;
        //dd($this->attributes["new"]);
    }

    public function setHitAttribute($value) {
        $this->attributes["hit"] = ($value==="on") ? 1 : 0;
    }

    public function setRecomendAttribute($value) {
        $this->attributes["recomend"] = ($value==="on") ? 1 : 0;
    }


    public function isNew() {
        return $this->new == 1;
    }

    public function isHit() {
        return $this->hit == 1;
    }

    public function isRecomend() {
        return $this->recomend == 1;
    }

}
