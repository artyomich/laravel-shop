<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Обратная связь</h4>
            </div>
            <div class="modal-body">
                @include('layouts.support_modal', ['supportModel' => new \models\Support()])
            </div>
        </div>
    </div>
</div>