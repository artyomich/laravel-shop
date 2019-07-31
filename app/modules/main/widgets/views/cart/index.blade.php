<div class="nav-cart">
    <i class="icon icon-cart"></i> Корзина: @if($count) <a
            href="/cart/">{{ $count }} {{ \Lang::choice('товар|товара|товаров', $count, [], 'ru') }}</a>
    ({{ $cart['total']  }} руб)
    @else
        Пуста
    @endif
</div>