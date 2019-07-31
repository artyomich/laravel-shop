<div class="row opinions">
    @foreach ($model::where('is_visible', 't')->get() as $opinion)
        <div class="col-xs-2">
            <a href="{{ \helpers\Image::url($opinion->image->filename, 1024, 768) }}" data-lightbox="image-1"
               class="thumbnail">
                {{ \helpers\Image::img($opinion->image->filename, 145, 145, ['crop' => true]) }}
                <div class="caption">{{ $opinion->name }}</div>
            </a>
        </div>
    @endforeach
</div>