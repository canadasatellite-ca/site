<?php

namespace MageSuper\Categoryattribute\Model;

class Subcatoptionstablet extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => 'Select...', 'value' => null],
                ['label' => '1', 'value' => '1'],
                ['label' => '2', 'value' => '2'],
                ['label' => '3', 'value' => '3'],
                ['label' => '4', 'value' => '4']
            ];
        }
        return $this->_options;
    }
}
