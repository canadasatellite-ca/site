<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Factories;

use CanadaSatellite\AstIntegration\AstManagement\Models\InmarsatSimModel;

class InmarsatSimFactory {

    public function __construct() {

    }

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $category
     * @param integer $packageId
     * @param integer $ratePlanId
     * @param string $reference
     * @param integer $billProfileId
     * @param string $alertEmail
     * @param integer[] $spendAlerts
     * @param string $airtimeRegion
     * @param string $country
     * @param string $industry
     * @param string $qos
     * @param string $voicemailPin
     * @return InmarsatSimModel
     */
    public function create(
        $simNumber,
        $serviceTypeId,
        $category,
        $packageId,
        $ratePlanId,
        $reference,
        $billProfileId,
        $alertEmail = 'sales@canadasatellite.ca',
        $spendAlerts = [100],
        $airtimeRegion = 'CAN',
        $country = 'CAN',
        $industry = 'OIL AND GAS',
        $qos = 'Background',
        $voicemailPin = '1111'
    ) {
        return new InmarsatSimModel(
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
        );
    }
}