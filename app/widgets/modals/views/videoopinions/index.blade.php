<div class="modal fade" id="{{ $modal->getId() }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                <h4 class="modal-title">Видео отзыв</h4>
            </div>
            <div class="modal-body">
                <?php
                $opinions = ['houvq27ES3w', '5lh3MFhNV-8', 'I6dU3hDBgGw', 'AX3yEmiqxnk', 'gae4BFvGSe0', '9OWbMmWyvis', 'jG4W0Pt1HjU', '3lHHbjXD60o'];
                $randOpinion = rand(0, count($opinions) - 1);
                ?>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $opinions[$randOpinion] }}"
                        frameborder="0" allowfullscreen></iframe>
            </div>
        </form>
    </div>
</div>