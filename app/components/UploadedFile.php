<?php

namespace components;

use \Symfony\Component\HttpFoundation\File\Exception\FileException;

class UploadedFile extends \Symfony\Component\HttpFoundation\File\UploadedFile
{
    /**
     * Скопирует загруженный файл.
     * @param $directory
     * @param null $name
     * @return \Symfony\Component\HttpFoundation\File\File
     * @throws FileException
     */
    public function copy($directory, $name = null)
    {
        if ($this->isValid()) {
            $target = $this->getTargetFile($directory, $name);

            if (!copy($this->getPathname(), $target)) {
                $error = error_get_last();
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
            }

            @chmod($target, 0666 & ~umask());

            return $target;
        }

        throw new FileException($this->getErrorMessage());
    }
}