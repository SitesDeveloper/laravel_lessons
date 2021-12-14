@extends('master', ['file'=>'product']);
@section('title', "Товар")

@section('content')
    <div class="starter-template">
        <h1>{{$product->name}}</h1>
        <p>Цена: <b>{{$product->price}} руб.</b></p>
        <img src="http://laravel-diplom-1.rdavydov.ru/storage/products/iphone_x.jpg">
        <p>{{$product->description}}</p>
        <form method="POST" action="{{route('basket-add', $product->id)}}">
            @csrf
            <button type="submit" class="btn btn-success" role="button">Добавить в корзину</button>
        </form>
    </div>
    </div>
@endsection
