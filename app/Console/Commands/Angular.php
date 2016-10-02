<?php

namespace Hottab\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Angular extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'angular:list {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate angular list';

    public $tplUrl = null;
    public $outPut = null;

    public function __construct()
    {
        $this->tplUrl = base_path('resources/template/');
        $this->outPut = base_path('resources/generator/');
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }

    protected function _snakeToCamel($val)
    {
        $val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
        //$val = strtolower(substr($val,0,1)).substr($val,1);
        return $val;
    }

    /**
     * Covert CamelCase to snake case
     * @param $str
     */
    protected function _camelToSnake($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $str)), '_');
    }

    /**
     * Convert CamelCase to file name
     * @param $str
     */
    protected function _convertFileName($str)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '-$0', $str)), '-');
    }

    /**
     * create file with content, and create folder structure if doesn't exist
     * @param String $filepath
     * @param String $message
     */
    protected function _forceFilePutContents($filepath, $message)
    {
        try {
            $isInFolder = preg_match("/^(.*)\/([^\/]+)$/", $filepath, $filepathMatches);
            if ($isInFolder) {
                $folderName = $filepathMatches[1];
                $fileName = $filepathMatches[2];
                if (!is_dir($folderName)) {
                    mkdir($folderName, 0777, true);
                }
            }
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            file_put_contents($filepath, $message);
        } catch (Exception $e) {
            echo "ERR: error writing '$message' to '$filepath', " . $e->getMessage();
        }
    }

    protected function _getInput($fileName)
    {
        return file_get_contents($this->tplUrl . $fileName);
    }

    protected function _writeOutPut($fileName, $massage)
    {
        $this->_forceFilePutContents($this->outPut . $fileName, $massage);
    }

    /**
     * Open input, replace string and write output
     * @param $inputFile
     * @param $outPutFile
     * @param array $message
     */
    protected function _transformFile($inputFile, $outPutFile, $message = [])
    {
        $content = $this->_getInput($inputFile);
        $search = array_keys($message);
        $replace = array_values($message);
        $content = str_replace($search, $replace, $content);

        $this->_writeOutPut($outPutFile, $content);

    }


    /**
     * Clear output directory
     */
    protected function _clearOutput($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") $this->_clearOutput($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Transform file with regular expression
     * @param $inputFile
     * @param $outPutFile
     * @param $message
     */
    protected function transformFileWithRegex($inputFile, $outPutFile, $message)
    {
        $content = $this->_getInput($inputFile);

        foreach ($message as $key => $value) {
            echo $key . PHP_EOL;
            $content = preg_replace($key, $value, $content);
        }

        $this->_writeOutPut($outPutFile, $content);
    }

    /**
     * Scan dir to find file and directory
     * @param $dir
     * @return mixed
     */
    public function scanDir($dir)
    {
        $objects = scandir($dir);

        // unset 2 first elements
        unset($objects[0], $objects[1]);

        return $objects;
    }
}
