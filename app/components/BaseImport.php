<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace components;

use SoapBox\Formatter\Formatter;

/**
 * Родительский класс для всех импортов.
 * @package components
 */
class BaseImport
{
    /**
     * Флаг записи прихода 1С.
     * @var bool
     */
    protected $enable1CVisitLog = true;

    /**
     * @var bool
     */
    protected $enableImport = true;

    /**
     * @var bool
     */
    protected $enableExport = true;

    /**
     * @var bool автоматическое удаление архивов.
     */
    protected $isClearZip = true;

    /**
     * @var string
     */
    protected $importName = '';

    /**
     * @var string
     */
    protected $importDir = '';

    /**
     * @param object $data
     * @return mixed
     */
    protected function import($data)
    {
        return '';
    }

    /**
     * @param mixed $importResult
     * @return array
     */
    protected function export($importResult = [])
    {
        return $importResult;
    }

    /**
     * Запускает механизм импорта затем экспорта.
     * @param $className
     * @return array|string
     * @throws \Exception
     */
    public static function run($className = self::class)
    {
        set_time_limit(0);
        ini_set('max_execution_time', 999999);

        /** @var BaseImport $instance */
        $instance = new $className;
        $instance->importName = str_replace('\\', '', (string)$className);

        if (empty($instance->importName)) {
            throw new \Exception('Не задано имя импорта');
        }

        if ($instance->enable1CVisitLog && $instance->is1C()) {
            //  Запишем дату последнего обращения и уравняем версии падений (см. алгоритм отправки смс).
            \components\ConfigWriter::set([
                'lastVisit' => time(),
                'lastFailVersion' => \Config::get('1c.lastFailSms')
            ], '', '1c');
        }

        $result = [];
        if ($instance->enableImport) {
            $data = $instance->read();
            $result = $instance->import((object)$data);
        }

        //  Экспорт доступен только для 1С
        if ($instance->enableExport) {
            $data = $instance->export($result);
            return $instance->answer($data);
        }

        return '';
    }

    /**
     * Прочтет все переданные файлы и вернет результат в качестве массива.
     * @return array
     */
    protected function read()
    {
        $result = [];
        $this->importDir = \Config::get('app.tmpDir') . 'import_' . str_replace('\\', '', $this->importName) . '/';
        if (is_dir($this->importDir)) {
            $this->rmDir($this->importDir);
        }
        mkdir($this->importDir);

        try {
            $file = \Input::file('file');
            if (!isset($file) || strtolower($file->getClientOriginalExtension()) != 'zip') {
                throw new \Exception();
            }

            $file->move($this->importDir, $file->getClientOriginalName());
        } catch (\Exception $e) {
            throw new \Exception('Не удалось загрузить файл.');
        }

        //  Распакуем и прочтем файлы
        foreach (glob($this->importDir . '*') as $archiveName) {
            foreach ($this->unpackFile($archiveName, $this->isClearZip) as $fileName) {
                if (!is_file($fileName) OR $fileName === $archiveName) {
                    continue;
                }
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $baseName = basename($fileName, '.' . $ext);
                $result[$baseName] = $this->readFile($fileName);
            }
        }
        return $result;
    }

    /**
     * Распакует архив.
     * @param $fileName
     * @param bool|true $removeAfter
     * @return array список распакованных файлов.
     */
    protected function unpackFile($fileName, $removeAfter = true)
    {
        $dirName = dirname($fileName);
        try {
            $zip = new \Chumper\Zipper\Zipper;
            $zip->make($fileName)->extractTo($dirName);
            $zip->close();
        } catch (\Exception $e) {
            throw new \Exception('Загруженный файл не является архивом или это не ZIP');
        }

        if ($removeAfter && is_file($fileName)) {
            unlink($fileName);
        }

        return glob($dirName . '/*');
    }

    /**
     * Прочтет один файл определяя тип по расширению.
     * @param string $fileName
     * @return array
     */
    protected function readFile($fileName)
    {
        //  Определим расширение.
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'csv':
                return \helpers\CsvToArray::toArray($fileName, ';');
            case 'json':
            case 'xml':
            case 'yaml':
                return Formatter::make(file_get_contents($fileName), $ext)->toArray();
            case 'xls':
                return \helpers\XlsToArray::toArray($fileName);
        }

        return [];
    }

    /**
     * Вернет ответ.
     * @param string|array $data
     * @return array
     */
    protected function answer($data)
    {
        $this->is1C() ? Formatter::make(!is_array($data) ? [$data] : $data, Formatter::ARR)->toXml() : $data;
        $response = \Response::make(Formatter::make($data, Formatter::ARR)->toXml(), 200);
        $response->header('Content-Type', 'application/x-www-form-urlencoded');
        return $response;
    }

    /**
     * @return bool
     */
    public function is1C()
    {
        $key = \Input::get('key');
        return !empty($key) && $key == $this->getImportKey();
    }

    /**
     * @return string
     */
    public function getImportKey()
    {
        return \Config::get('app.importKey');
    }

    /**
     * Рекурсивное удаление дирректории.
     * @param string $dirName
     */
    private function rmDir($dirName)
    {
        if (is_dir($dirName)) {
            $objects = scandir($dirName);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dirName . "/" . $object) == "dir") {
                        if (count(glob($dirName . '/' . $object . '/*'))) {
                            $this->rmDir($dirName . '/' . $object);
                        } else {
                            rmdir($dirName . "/" . $object);
                        }
                    } else {
                        unlink($dirName . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dirName);
        }
    }
}