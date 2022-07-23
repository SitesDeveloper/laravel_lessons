@extends('layouts.master', ['file'=>'category']);
@section('title', __('main.category') . $category->__("name"))

@section('content')
        <h1>
            {{ $category->__("name") }}
        </h1>
        <p>
            {{ $category->__("description") }}
        </p>

        <a href="/categories">Все категории</a>


        <div class="row">
            @foreach ($category->products->map->skus->flatten() as $sku)
                @include('layouts.card', compact("sku"))    
            @endforeach
        </div>
@endsection
