@lang('mail.subscription.dear_client') {{ $sku->product->__("name") }} @lang('mail.subscription.appeared_in_stock').

<a href="{{ route('sku', [$sku->product->category->code, $sku->product->code, $sku]) }}">@lang('mail.subscription.more_info')</a>