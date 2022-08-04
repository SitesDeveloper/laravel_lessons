<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    protected $fillable = [
        'code', 'value', 'type', 'currency_id', 'only_once',  'description', 'expired_at'
    ];

    protected $dates = ['expired_at'];


    public function orders() {
        return $this->hasMany( Order::class);
    }

    public function currency() {
        return $this->belongsTo( Currency::class );
    }

    public function isAbsolute() {
        return $this->type == 1;
    }

    public function isOnlyOnce()
    {
        return $this->only_once === 1;
    }


}
