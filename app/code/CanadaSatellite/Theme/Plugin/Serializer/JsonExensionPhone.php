<?php

namespace CanadaSatellite\Theme\Plugin\Serializer;

use Magento\Framework\Serialize\Serializer\Json;

class JsonExensionPhone {

    function aroundUnserialize(Json $subject, callable $proceed, $string) {

        $result = json_decode($string, true);
        json_decode($string, true) ? $error = false : $error = true;
        if ($error && stripos($string, 'is_region_visible')>0) {
            return unserialize($string);
        }
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
