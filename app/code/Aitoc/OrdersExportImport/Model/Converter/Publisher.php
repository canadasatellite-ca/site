<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Model\Converter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\View\Asset;

/**
 * Class Publisher
 * @package Aitoc\OrdersExportImport\Model\Converter
 */
class Publisher
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\File\WriteFactory
     */
    public $file;

    /**
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    public $readFile;

    /**
     * @var string
     */
    private $path;

    /**
     * Source file handler.
     *
     * @var \Magento\Framework\Filesystem\File\Write
     */
    private $fileHandler;

    /**
     * @var file
     */
    public $filename;

    /**
     * Publisher constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Directory\WriteFactory $directory
     * @param \Magento\Framework\Filesystem\File\WriteFactory $file
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Directory\WriteFactory $directory,
        \Magento\Framework\Filesystem\File\WriteFactory $file,
        \Magento\Framework\Filesystem\File\ReadFactory $readFile
    ) {
        $this->filesystem = $filesystem;
        $this->file       = $file;
        $this->readFile   = $readFile;
        $this->directory  = $directory;
    }

    /**
     * Data to file
     *
     * @param $filename
     * @param $data
     */
    public function publish($filename, $data)
    {
        $path = $this->getPathFile();
        $this->setFilename($filename);

        return $this->publishFile($path, $filename, $data);
    }


    /**
     * Get full path for file
     *
     * @return string]
     */
    public function getPathFile()
    {
        $dir  = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $path = $dir->getAbsolutePath() . $this->getPath();
        if (!$dir->isExist($this->getPath())) {
            $directory = $this->directory->create($path);
            $directory->create();
        }

        return $path;
    }

    /**
     * Write to file
     *
     * @param $target
     * @param $name
     * @param $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function publishFile($target, $name, $data)
    {
        $file = $this->file->create(
            $target . "/" . $name,
            \Magento\Framework\Filesystem\DriverPool::FILE,
            'w'
        );
        $file->write($data);
        $file->close();
    }

    /**
     * Set path
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set Filename
     *
     * @param $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     *
     * Get Filename
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * initiliaze for CSV
     *
     * @param $filename
     */
    public function initCSV($filename, $type = 'w')
    {
        $path = $this->getPathFile();
        $this->setFilename($filename);
        $file    = $this->file;
        $allPath = $path . "/" . $filename;
        if ($type == "r") {
            $file    = $this->readFile;
            $allPath = $filename;
        }
        $this->fileHandler = $file->create(
            $allPath,
            \Magento\Framework\Filesystem\DriverPool::FILE,
            $type
        );
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @throws \Exception
     * @return $this
     */
    public function writeRow(array $rowData, $delimiter = ",", $enclosure = "\"")
    {
        foreach ($rowData as $key => &$value) {
            if (is_array($value) || is_object($value)) {
                $value = $this->toSerialize($value, $delimiter);
            } else {
                $value = str_replace(
                    ["\r\n", "\n", "\r"],
                    ' ',
                    $value
                );
            }
        }
        $this->fileHandler->writeCsv(
            $rowData,
            $delimiter,
            $enclosure
        );

        return $this;
    }

    /**
     * @return array|bool|null
     */
    public function readRow()
    {
        return $this->fileHandler->readCsv();
    }

    /**
     * get Keys
     *
     * @param $data
     * @return array
     */
    private function getHeaders($data)
    {
        $keys = [];
        foreach ($data as $key => $element) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'text/csv';
    }

    /**
     * Object destructor.
     *
     * @return void
     */
    public function destruct()
    {
        if (is_object($this->fileHandler)) {
            $this->fileHandler->close();
        }
    }

    /**
     * Date to Xml
     *
     * @param $data
     * @return string
     */
    public function toSerialize($data, $delimiter)
    {
        return "ser;" . str_replace($delimiter, "%%", json_encode($data));
    }

    /**
     * @param $file
     * @return \DOMDocument
     */
    public function readXml($file)
    {
        $doc = new \DOMDocument();
        $doc->load($file);

        return $doc;
    }
}
