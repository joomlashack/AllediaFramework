<?php

class AutoLoaderMock extends \Alledia\Framework\AutoLoader
{
    protected static $files = array();

    /**
     * @var AutoLoaderMock
     */
    protected static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function setFiles(array $files)
    {
        self::$files = $files;
    }

    protected static function registerLoader($method)
    {
        self::getInstance();
    }

    protected function requireFile($file)
    {
        return in_array($file, self::$files);
    }

    public function mockLoadClass($class)
    {
        return $this->loadClass($class);
    }

    public function mockLoadCamelClass($class)
    {
        return $this->loadCamelClass($class);
    }
}
