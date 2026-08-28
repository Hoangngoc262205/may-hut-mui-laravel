@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Wishlist</span>
            <h1>Sản phẩm yêu thích</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            @if($wishlist->items->isEmpty())
                <div class="empty-state">
                    <h2>Chưa có sản phẩm yêu thích</h2>
                    <p>Nhấn biểu tượng trái tim trên sản phẩm để lưu lại.</p>
                    <a class="btn btn-dark" href="{{ route('products.index') }}">Khám phá sản phẩm</a>
                </div>
            @else
                <div class="product-grid">
                    @foreach($wishlist->items as $item)
                        <div>
                            <x-product-card :product="$item->product" />
                            <form method="POST" action="{{ route('wishlist.destroy', $item) }}" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline full" type="submit">Bỏ yêu thích</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
