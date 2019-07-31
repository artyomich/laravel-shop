<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace widgets\modals;

use models\Cities;
use widgets\BaseModal;

/**
 * @package widgets\modals
 */
class VideoOpinionsModal extends BaseModal
{
    /**
     * @inheritdoc
     */
    public function getButtonName()
    {
        return '<span class="glyphicon glyphicon-play-circle"></span> <span>Видео отзыв</span>';
    }
}