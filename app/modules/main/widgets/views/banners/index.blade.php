@foreach($banners as $banner)
@if($banner->id == 9 && \Cookie::get('city_id', 1) == 1 || $banner->id != 9)
<!-- ASIDE ITEM -->
<a href="{{ $banner->link }}" class="aside-item thumbnail">
    {{ \helpers\Image::img($banner->image->filename, 245, 600, ['crop' => false]) }}
</a>
<!-- /ASIDE ITEM -->
@endif
@endforeach

