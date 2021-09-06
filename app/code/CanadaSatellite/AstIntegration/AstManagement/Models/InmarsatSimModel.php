<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Models;

class InmarsatSimModel implements \JsonSerializable {

    private $simNumber;
    private $serviceTypeId;
    private $category;
    private $packageId;
    private $ratePlanId;
    private $qos;
    private $industry;
    private $country;
    private $airtimeRegion;
    private $alertEmail;
    private $voicemailPin;
    private $spendAlerts;
    private $reference;
    private $billProfileId;

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $category
     * @param integer $packageId
     * @param integer $ratePlanId
     * @param string $qos
     * @param string $industry
     * @param string $country
     * @param string $airtimeRegion
     * @param string $alertEmail
     * @param string $voicemailPin
     * @param integer[] $spendAlerts
     * @param string $reference
     * @param integer $billProfileId
     */
    public function __construct(
        $simNumber,
        $serviceTypeId,
        $category,
        $packageId,
        $ratePlanId,
        $qos,
        $industry,
        $country,
        $airtimeRegion,
        $alertEmail,
        $voicemailPin,
        $spendAlerts,
        $reference,
        $billProfileId
    ) {
        $this->simNumber = $simNumber;
        $this->serviceTypeId = $serviceTypeId;
        $this->category = $category;
        $this->packageId = $packageId;
        $this->ratePlanId = $ratePlanId;
        $this->qos = $qos;
        $this->industry = $industry;
        $this->country = $country;
        $this->airtimeRegion = $airtimeRegion;
        $this->alertEmail = $alertEmail;
        $this->voicemailPin = $voicemailPin;
        $this->spendAlerts = $spendAlerts;
        $this->reference = $reference;
        $this->billProfileId = $billProfileId;
    }

    public function jsonSerialize() {
        return [
            'Service' => $this->simNumber,
            'Category' => $this->category,
            'ServiceTypeId' => $this->serviceTypeId,
            'PackageId' => $this->packageId,
            'RatePlanId' => $this->ratePlanId,
            'Qos' => $this->qos,
            'Industry' => $this->industry,
            'CountryOfActivation' => $this->country,
            'AirtimeRegion' => $this->airtimeRegion,
            'AlertingEmailAddress' => $this->alertEmail,
            'SpendAlerts' => $this->spendAlerts,
            'Reference' => $this->reference,
            'BillProfileId' => $this->billProfileId,
            'VoicemailPin' => $this->voicemailPin
        ];
    }

    public function getSimNumber() {
        return $this->simNumber;
    }

    public function getServiceTypeId() {
        return $this->serviceTypeId;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getPackageId() {
        return $this->packageId;
    }

    public function getRatePlanId() {
        return $this->ratePlanId;
    }

    public function getQos() {
        return $this->qos;
    }

    public function getIndustry() {
        return $this->industry;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getAirtimeRegion() {
        return $this->airtimeRegion;
    }

    public function getAlertEmail() {
        return $this->alertEmail;
    }

    public function getVoicemailPin() {
        return $this->voicemailPin;
    }

    public function getSpendAlerts() {
        return $this->spendAlerts;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getBillProfileId() {
        return $this->billProfileId;
    }
}