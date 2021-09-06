<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Models;

class IridiumSimModel implements \JsonSerializable {

    private $simNumber;
    private $serviceTypeId;
    private $planId;
    private $planOptions;
    private $language;
    private $voiceAccess;
    private $dataAccess;
    private $reference;
    private $billProfileId;
    private $voicePin;
    private $dataPin;
    private $rudicsGroup;

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
     */
    public function __construct(
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
    ) {
        $this->simNumber = $simNumber;
        $this->serviceTypeId = $serviceTypeId;
        $this->planId = $planId;
        $this->planOptions = $planOptions;
        $this->reference = $reference;
        $this->billProfileId = $billProfileId;
        $this->language = $language;
        $this->rudicsGroup = $rudicsGroup;
        $this->voiceAccess = $voiceAccess;
        $this->dataAccess = $dataAccess;
        $this->voicePin = $voicePin;
        $this->dataPin = $dataPin;
    }

    public function jsonSerialize() {
        $data = [
            'Service' => $this->simNumber,
            'ServiceTypeId' => $this->serviceTypeId,
            'PlanId' => $this->planId,
            'PlanOptions' => $this->planOptions,
            'Language' => $this->language,
            'RudicsGroup' => $this->rudicsGroup,
            'Reference' => $this->reference,
            'BillProfileId' => $this->billProfileId
        ];

        if ($this->voiceAccess) {
            $data['VoiceAccess'] = true;
            $data['VoicePin'] = $this->voicePin;
        }

        if ($this->dataAccess) {
            $data['DataAccess'] = true;
            $data['DataPin'] = $this->dataPin;
        }

        return $data;
    }

    public function getSimNumber() {
        return $this->simNumber;
    }

    public function getServiceTypeId() {
        return $this->serviceTypeId;
    }

    public function getPlanId() {
        return $this->planId;
    }

    public function getPlanOptions() {
        return $this->planOptions;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function getVoiceAccess() {
        return $this->voiceAccess;
    }

    public function getDataAccess() {
        return $this->dataAccess;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getBillProfileId() {
        return $this->billProfileId;
    }
}