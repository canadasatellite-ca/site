<?php
namespace MageSuper\Casat\Plugin;

class WysiwygConfig
{
    function afterGetConfig($subject, \Magento\Framework\DataObject $config)
    {
        $config->addData([
            'add_directives' => true,
        ]);

        return $config;
    }
}