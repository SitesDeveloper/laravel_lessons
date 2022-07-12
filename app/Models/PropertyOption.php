<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyOption extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    protected $fillable = ['property_id', 'name', 'name_en'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    //TODO: check table name and fields
    public function skus()
    {
        return $this->belongsToMany(Sku::class);
    }
}
