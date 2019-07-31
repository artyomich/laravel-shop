@if (count($model->photos))
<h2>Фото</h2>
<div class="row">
    @foreach ($model->photos as $photo)
    <div class="col-xs-2">
        <a href="{{ \helpers\Image::url($photo->filename, 1024, 768) }}" data-lightbox="image-1" class="thumbnail">
            {{ \helpers\Image::img($photo->filename, 145, 145, ['crop' => true]) }}
        </a>
    </div>
    @endforeach
</div>
@endif