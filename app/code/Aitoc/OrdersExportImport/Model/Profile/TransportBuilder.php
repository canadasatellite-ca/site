<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Profile;

/**
 * Class Converter
 * @package Aitoc\OrdersExportImport\Model
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param $filePath
     * @param $filename
     */
    public function createAttachment($filePath, $filename)
    {
        $fileContents = file_get_contents($filePath);
        $this->message->createAttachment($fileContents. 'text/csv')->filename = $filename;

        return $this;
    }
}
