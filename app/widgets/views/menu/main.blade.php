@use('helpers\Html')

<nav class="row nav-main">
    <ul class="nav nav-pills">
        @foreach ($data as $item)
        <li class="dropdown">
            <a href="{{ $current == $item['parent']->alias ? '#' : $item['parent']->alias }}"
            @if ($current == $item['parent']->alias) onclick="return false;" @endif
               class="@if (count($item['childs'])) dropdown-toggle @endif @if ($current == $item['parent']->alias) active @endif"
            @if (count($item['childs'])) data-toggle="dropdown" @endif ><span>{{ $item['parent']->name }}</span>
            @if (count($item['childs']))
            <i class="caret"></i>
            @endif
            </a>
            @if (count($item['childs']))
            <ul class="dropdown-menu" role="menu">
                @foreach ($item['childs'] as $child)
                    <li{{ $current == $child->alias ? ' class="active"' : '' }}>
                        <a href="{{ $current == $child->alias ? '#' : $child->alias }}"
                        @if ($current == $child->alias) onclick="return false;" @endif
                                >
                            {{ $child->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
            @endif
        </li>
        @endforeach
    </ul>
</nav>

<script>
    $('.active', '.nav-main').closest('.dropdown').addClass('active');
</script>