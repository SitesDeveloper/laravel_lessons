@extends('layouts.master')

@section('title', __('main.product'))

@section('content')
    <h1>{{ $sku->product->__("name") }}</h1>
    <h2>{{ $sku->product->category->__("name") }}</h2>
    <p>@lang('product.price'): <b>{{ $sku->price }} {{ $currencySymbol }}</b></p>

    @isset($sku->product->properties)
        @foreach ($sku->propertyOptions as $propertyOption)
            <h4>{{ $propertyOption->property->__('name') }}: {{ $propertyOption->__('name') }}</h4>
        @endforeach
    @endisset

    <img src="{{ Storage::url($sku->product->image) }}">
    <p>{{ $sku->product->__("description") }}</p>

    @if($sku->isAvailable())
        <form action="{{ route('basket-add', $sku) }}" method="POST">
            <button type="submit" class="btn btn-success" role="button">@lang('product.add_to_cart')</button>

            @csrf
        </form>
    @else

        <span>@lang('product.not_available')</span>
        <br>
        <span>@lang('product.tell_me'):</span>
        <div class="warning">
            @if($errors->get('email'))
                {!! $errors->get('email')[0] !!}
            @endif
        </div>
        <form method="POST" action="{{ route('subscription', $sku) }}">
            @csrf
            <input type="text" name="email">
            <button type="submit">@lang('product.subscribe')</button>
        </form>
    @endif
@endsection