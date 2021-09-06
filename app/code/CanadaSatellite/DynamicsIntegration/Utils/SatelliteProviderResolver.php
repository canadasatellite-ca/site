<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class SatelliteProviderResolver {
    const IRIDIUM_ICCID_CODE = "881";
    const INMARSAT_ICCID_CODE = "870";
    const ICCID_PREFIX = "89";

    function __construct() {

    }

    function isSimIridium($iccid) {
        return strcmp(substr($iccid, 2, strlen(self::IRIDIUM_ICCID_CODE)), self::IRIDIUM_ICCID_CODE) === 0;
    }

    function isSimInmarsat($iccid) {
        return strcmp(substr($iccid, 2, strlen(self::INMARSAT_ICCID_CODE)), self::INMARSAT_ICCID_CODE) === 0;
    }

    function isIccid($iccid) {
        return strpos(self::ICCID_PREFIX, $iccid) === 0
            && ($this->isSimInmarsat($iccid) || $this->isSimIridium($iccid));
    }
}