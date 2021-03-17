<?php

namespace CanadaSatellite\SimpleAmqp\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    public function convert($source)
    {
        return array(
            'queues' => $this->toConfigArray($source, 'csQueue'),
        );
    }
    
    /**
     * Return config node converted to array
     */
    protected function toConfigArray($source, $nodeName)
    {
        $items = array();
        $config = $source->getElementsByTagName($nodeName);
        
        foreach($config as $configNode) {
            $item = array();
            foreach($configNode->attributes as $attribute) {
                $item[$attribute->name] = $attribute->value;
            }
            $items []= $item;
        }

        return $items;
    }
}
