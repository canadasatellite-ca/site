<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class DynamicProperty implements \JsonSerializable {
    private $name;
    private $maxLength;
    private $productGuid;

    function __construct($name, $productGuid, $maxLength = 100) {
        $this->name = $name;
        $this->maxLength = $maxLength;
        $this->productGuid = $productGuid;
    }

    function jsonSerialize() {
        return [
            'datatype' => 3, // Single Line Of Text
            'ishidden' => false,
            'isreadonly' => false,
            'isrequired' => false,
            'maxlengthstring' => $this->maxLength,
            'name' => $this->name,
            'regardingobjectid_product@odata.bind' => "/products({$this->productGuid})"
        ];
    }

    function getName() {
        return $this->name;
    }

    function setName($value) {
        $this->name = $value;
    }

    function getMaxLength() {
        return $this->maxLength;
    }

    function setMaxLength($value) {
        $this->maxLength = $value;
    }
}