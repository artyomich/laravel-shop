<?php
/**
 * @var \models\Categories $category текущая категория.
 * @var string $categoryAlias
 */

$formDisplay = $categoryAlias != '' || in_array(Request::path(), ['/', 'catalog']);

?>

<div class="box collapsed-box">
    <div class="nav-tabs-custom filter-сatalog">
        <ul class="menu-сatalog">
            <li{{ $category && $category->type == \models\Categories::TYPE_TIRES ? ' class="active"' : '' }}>
                <a href="#tabSimpleFilter1" data-toggle="tab">
                    <img src="/img/car_tires.png">
                    <span class="hidden-xs">Подбор шин</span>&nbsp;
                </a>
            </li>
            @if (!empty($filterDataDisk) && \Config::get('settings.disks.enabled'))
                <li{{ $category && $category->type == \models\Categories::TYPE_DISKS ? ' class="active"' : '' }}>
                    <a href="#tabSimpleFilter2" data-toggle="tab">
                        <img src="/img/wheels.png">
                        <span class="hidden-xs">Подбор дисков</span>&nbsp;
                    </a>
                </li>
            @endif
        </ul>
        <ul class="nav nav-tabs partner-banner-head">
            @if (\Cookie::get('city_alias') == 'rostov-na-donu')
                <li class="rostov">
                    {{ \widgets\modals\RostovModal::a(['class' => 'videoLink']) }}
                </li>
            @endif
            <li class="hidden-sm hidden-xs">
                {{ \widgets\modals\NortecAdModal::a(['class' => 'videoLink']) }}
            </li>
            <li class="hidden-xs">
                {{ \widgets\modals\VideoOpinionsModal::a(['class' => 'videoLink']) }}
            </li>
        </ul>
        <div class="tab-content">
            @include('_tab_tires')
            @if (!empty($filterDataDisk) && \Config::get('settings.disks.enabled'))
                @include('_tab_disks')
            @endif
        </div>
    </div>
</div>