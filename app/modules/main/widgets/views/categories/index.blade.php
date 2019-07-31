<ul class="nav">
    @foreach($categories as $key => $category)
    <li class="col-md-{{ in_array($key, [2,4]) ? 3 : 2 }} col-xs-6 text-center ">
        <a href="/catalog/{{ $category->alias }}/"{{ $categoryAlias == $category->alias ? ' class="active"' : '' }}>
            <img src="/img/menu/{{ $category->alias }}.png" alt="{{ $category->name }}"/>
            <div>{{ $category->name }}</div>
        </a>
    </li>
    @endforeach
</ul>