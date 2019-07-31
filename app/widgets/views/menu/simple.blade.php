@use('helpers\Html')

<ul>
    @foreach ($data as $item)
    <li class="dropdown">
        <a href="{{ $current == $item['parent']->alias ? '/#' : $item['parent']->alias }}"
        @if ($current == $item['parent']->alias) onclick="return false;" class="active" @endif
                ><p>{{ $item['parent']->name }}</p></a>
    </li>
    @endforeach
</ul>

<script>
    $('.active', '.nav-main').closest('.dropdown').addClass('active');
</script>