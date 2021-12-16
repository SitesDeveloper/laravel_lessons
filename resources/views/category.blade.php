@extends('layouts.master', ['file'=>'category']);
@section('title', "Категория ". $category->name)

@section('content')
        <h1>
            {{ $category->name }} ({{$category->products->count()}})
        </h1>
        <p>
            {{ $category->description }}
        </p>

        <a href="/categories">Все категории</a>


        <div class="row">
            @foreach ($category->products as $product)
                @include('layouts.card', ['product'=>$product])    
            @endforeach
        </div>
@endsection
