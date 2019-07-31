<?php

Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push('Главная', '/');
});

Breadcrumbs::register('team', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Наша команда', '');
});

Breadcrumbs::register('page', function($breadcrumbs, $page) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($page->name, '');
});

Breadcrumbs::register('catalog', function($breadcrumbs, $object) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Каталог', '/catalog/');

    if (is_object($object)){ //$object может быть пустой, в случае если это форма подбора товара, ни каталог, ни товар не выбран
        if (get_class($object) == 'models\Products') {
            $breadcrumbs->push($object->categories->breadcrumb, '/catalog/' . $object->categories->alias);
            $breadcrumbs->push($object->name, '');
        } elseif (get_class($object) == 'models\Categories') {
            $breadcrumbs->push($object->breadcrumb, '/catalog/' . $object->alias);
        }
    }
});

Breadcrumbs::register('news', function($breadcrumbs, $object) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Новости', '/news/news_poshk/'); //иерархии нет, из объекта не вытащить родительский элемент
    if (is_object($object)) {
        if (get_class($object) == 'models\Pages') {
            $breadcrumbs->push($object['name'], '');
        }
    }
});
