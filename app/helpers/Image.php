<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

use Imagine\Image\Box;

/**
 * Помощник Image.
 * Содержит все необходимые функции для работы с изображениями, доболняя Imagine.
 */
class Image
{
    /**
     * Уменьшает или увеличивает изображение.
     *
     * @param       $source
     * @param       $width
     * @param       $height
     * @param array $params
     *
     * @return string
     */
    public static function resize($source, $width, $height, $params = [])
    {
        $sourceName = public_path() . '/files/images/' . str_replace(':', '/originals/', $source);
        if (!is_file($sourceName)) {
            return '';
        }

        $_parts = explode(':', basename($source));
        $_partsExt = explode('.', $_parts[1]);
        $ext = '.' . end($_partsExt);
        $fileName = basename($_parts[1], $ext) . '_' . $width . 'x' . $height . $ext;
        $destination = dirname(str_replace(':', '/resized/', $source)) . '/' . $fileName;
        $destinationPath = public_path() . '/files/images/' . $destination;

        //  Если такой файл есть или конвертация не требуется, то ничего ресайзить не надо.
        if (is_file($destinationPath) || isset($params['convert']) && $params['convert'] == false) {
            return '/files/images/' . $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);

        try {
            $image->getImagick()->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
            $image->save($destinationPath);

            //  FIXME: Все это можно сделать через paste imagine.
            if (isset($params['strict']) && $params['strict']) {
                $newImage = new \Imagick();
                $newImage->newImage($width, $height, new \ImagickPixel('white'));
                $newImage->setImageFormat('jpg');
                $newImage->compositeImage(
                    $image->getImagick(),
                    \Imagick::COMPOSITE_DEFAULT,
                    (((($newImage->getImageWidth()) - ($image->getImagick()->getImageWidth()))) / 2),
                    (((($newImage->getImageHeight()) - ($image->getImagick()->getImageHeight()))) / 2)
                );
                $newImage->writeImage($destinationPath);

                unset($newImage);
            }

            isset($params['watermark']) && $params['watermark'] && self::watermark($image, $destinationPath);
        } catch (\RuntimeException $e) {
            \App::abort($e->getCode(), $e->getMessage());
        }

        $image->__destruct();

        unset($image);
        unset($imagine);

        return '/files/images/' . $destination;
    }

    /**
     * Наложение водяного знака
     * @param $image
     * @param $destinationPath
     */
    public static function watermark($image, $destinationPath)
    {
        // Open the watermark
        $watermark = new \Imagick();
        $watermark->readImage(public_path() . '/img/watermark.png');
        $image->getImagick()->compositeImage($watermark, \Imagick::COMPOSITE_OVER, 0, 0);
        $image->save($destinationPath);
    }

    /**
     * Обрезает изображение.
     *
     * @param string $source
     * @param integer $width
     * @param integer $height
     * @param array $params
     *
     * @return string
     */
    public static function crop($source, $width, $height, $params = [])
    {
        $sourceName = public_path() . '/files/images/' . str_replace(':', '/originals/', $source);
        if (!is_file($sourceName)) {
            return '';
        }

        $_parts = explode(':', basename($source));
        $_partsExt = explode('.', $_parts[1]);
        $ext = '.' . end($_partsExt);
        $fileName = basename($_parts[1], $ext) . '_' . $width . 'x' . $height . 'c' . $ext;
        $destination = dirname(str_replace(':', '/resized/', $source)) . '/' . $fileName;
        $destinationPath = public_path() . '/files/images/' . $destination;

        //  Если такой файл есть или конвертация не требуется, то ничего ресайзить не надо.
        if (is_file($destinationPath) || isset($params['convert']) && $params['convert'] == false) {
            return '/files/images/' . $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);

        try {
            $image->getImagick()->cropThumbnailImage($width, $height);
            $image->save($destinationPath);
        } catch (\RuntimeException $e) {
            \App::abort($e->getCode(), $e->getMessage());
        }

        $image->__destruct();

        unset($image);
        unset($imagine);

        if (isset($params['useDestPath']) AND $params['useDestPath']) {
            return $destinationPath;
        }

        return '/files/images/' . $destination;
    }

    /**
     * Вернет html img тег, с сылкой на изображение с новыми размерами.
     *
     * @param string $source
     * @param integer $width
     * @param integer $height
     * @param array $params
     *
     * @return string
     */
    public static function img($source, $width, $height, $params = [])
    {
        $alt = isset($params['alt']) ? $params['alt'] : '';
        $attrs = implode(' ', array_map(function ($v, $k) {
            return $k . '="' . str_replace('"', '\'', $v) . '"';
        }, $params, array_keys($params)));
        return isset($params['crop']) && $params['crop']
            ? '<img src="' . static::crop($source, $width, $height, $params) . '" ' . $attrs . ' />'
            : '<img src="' . static::resize($source, $width, $height, $params) . '" ' . $attrs . ' />';
    }

    /**
     * Вернет сылку на изображение с новыми размерами.
     *
     * @param string $source
     * @param integer $width
     * @param integer $height
     * @param array $params
     *
     * @return string
     */
    public static function url($source, $width, $height, $params = [])
    {
        return isset($params['crop']) && $params['crop']
            ? static::crop($source, $width, $height, $params)
            : static::resize($source, $width, $height, $params);
    }

    public static function certificate($brand)
    {
        $translitedBrand = \helpers\StringHelper::translitLow($brand);
        $certFile = $translitedBrand . '.jpg';
        if (!file_exists($pathCertFile = public_path() . '/files/images/certificates/originals/' . $certFile))
            return '';
        $desc = \models\Certificates::where('brand', '=', $translitedBrand)->remember(120)->first();
        return '<a href="' . asset('files/images/certificates/originals/' . $certFile) . '"'
        . ' data-lightbox="image-1" data-toggle="tooltip"  data-placement="right" data-html="true"'
        . ' data-template="<div class=\'tooltip\' role=\'tooltip\'><div class=\'tooltip-arrow\'></div><div class=\'tooltip-inner\' id=\'certTooltip\'></div></div>" '
        . ' title="' . (isset($desc->desc) ? $desc->desc : '') . '"'
        . ' class="thumbnail certLink"'
        . '>'
        . ($translitedBrand === 'nortec' ? 'Эксклюзивный партнер ' : 'Сертификат дилера ') . $brand
        . '</a>';
    }

    public static function certificateIcon($brand)
    {
        $translitedBrand = \helpers\StringHelper::translitLow($brand);
        $title = ($translitedBrand === 'nortec' ? 'Эксклюзивный партнер ' : 'Сертификат дилера ') . $brand;
        if (file_exists(public_path() . '/files/images/certificates/originals/' . $translitedBrand . '.jpg'))
            return '<span class="icon-certificate" title="' . $title . '" data-toggle="tooltip"  data-placement="left"></span>';
    }

    /**
     * Возвращает ссылки на файлы сертификатов
     *
     * @return string
     */
    public static function certificateFiles()
    {
        $data = '';
        foreach (\File::files('files/certificates') as $file)
            $data .= '<p><a href="/' . $file . '" type="application/file">' . \File::name($file) . '</a></p>';
        return $data;
    }
}
