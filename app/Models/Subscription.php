<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendSubscriptionMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    public $fillable = [
        "email", "sku_id"
    ];

    public function scopeActiveByProductId($query, $skuId)
    {
        return $query->where('status', 0)->where('sku_id', $skuId);
    }

    public function sku() {
        $this->belongsTo(Sku::class);
    }

    public static function sendEmailsBySubscription(Sku $sku)
    {
        $subscriptions = self::activeBySkuId($sku->id)->get();

        foreach($subscriptions as $subscription) {
            Mail::to($subscription->email)->send(new SendSubscriptionMessage($sku));
            $subscription->status = 1;
            $subscription->save();
        }
    }

}
