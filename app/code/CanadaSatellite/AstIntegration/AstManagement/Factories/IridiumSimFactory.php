<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Factories;

use CanadaSatellite\AstIntegration\AstManagement\Models\IridiumSimModel;

class IridiumSimFactory {

    public function __construct() {

    }

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $planId
     * @param array $planOptions
     * @param string $reference
     * @param integer $billProfileId
     * @param string $language
     * @param string $rudicsGroup
     * @param boolean $voiceAccess
     * @param boolean $dataAccess
     * @param integer|null $voicePin
     * @param integer|null $dataPin
     * @return IridiumSimModel
     */
    public function create(
        $simNumber,
        $serviceTypeId,
        $planId,
        $planOptions,
        $reference,
        $billProfileId,
        $language = 'English',
        $rudicsGroup = 'ONSATMAIL',
        $voiceAccess = false,
        $dataAccess = false,
        $voicePin = null,
        $dataPin = null
    ) {
        return new IridiumSimModel(
            $simNumber,
            $serviceTypeId,
            $planId,
            $planOptions,
            $reference,
            $billProfileId,
            $language,
            $rudicsGroup,
            $voiceAccess,
            $dataAccess,
            $voicePin,
            $dataPin
        );
    }
}