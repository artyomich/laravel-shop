<?php
$gridId = uniqid('sortview-');

use helpers\Html;

/**
 * Рекурсивный рендер меню.
 */
function recursiveMenu($childs, $model)
{
    $data = [];

    foreach ($childs as $child) {
        $result = '<input type="hidden" name="sort[]" value="' . $child['parent']->id . '"/>
            <table class="table"><tr>' .
            '<td class="move-zone"><a href="#" class="text-muted"><i class="fa fa-bars"></i></a></td>';
        foreach ($model->columns() as $k => $item) {
            $_col = $item->column;
            $result .= '<td' . ($_col == '_actions' ? ' class="sortview-actions"' : '') .
                (isset($item->style) ? ' style="' . $item->style . '"' : '') . '>' .
                $child['parent']->$_col . '</td>';
        }
        $data[] = $result . '</tr></table>';
    }

    return '<ul class="sortview-item"><li>' . implode('</li><li>', $data) . '</li></ul>';
}

?>

<form>
    <ul class="sortview" id="<?= $gridId ?>">
        @foreach ($data as $id => $column)
        <li data-id="{{ $id }}">
            <input type="hidden" name="sort[]" value="{{ $id }}"/>
            <table class="table">
                <tr>
                    <td class="move-zone"><a href="#" class="text-muted"><i class="fa fa-bars"></i></a></td>
                    @foreach ($model->columns() as $k => $item)
                    <?php $_col = $item->column; ?>
                    <td class="{{ $_col == '_actions' ? 'sortview-actions' : '' }} {{ !empty($item->params['class']) ? $item->params['class'] : '' }}"
                        style="{{ isset($item->style) ? $item->style : '' }}">{{ $column['parent']->$_col }}
                    </td>
                    @endforeach
                </tr>
            </table>
            <?= recursiveMenu($column['childs'], $model) ?>
        </li>
        @endforeach
    </ul>
</form>

<script>
    /**
     * Сортировка.
     */
    var sortableSetup = function () {
        $('#<?= $gridId ?>, .sortview-item').sortable({
            items: 'li:not(.sortview-disabled)',
            placeholder: 'sortview-highlight',
            handle: '.move-zone a',
            axis: 'y',
            update: function () {
                _.post(
                        '/admin/<?= !empty($params['controller']) ? $params['controller'] : \helpers\RouteInfo::controller() ?>/sort',
                    $('#<?= $gridId ?>').closest('form').serialize(),
                    function (error) {
                        if (error) {
                            _.alert(error, 'danger');
                        }
                    }
                );
            }
        }).disableSelection();
    };

    $(document).ajaxComplete(function () {
        sortableSetup();
    });

    sortableSetup();
</script>