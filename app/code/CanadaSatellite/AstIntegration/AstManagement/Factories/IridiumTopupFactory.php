<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Factories;

use CanadaSatellite\AstIntegration\AstManagement\Models\IridiumTopupModel;

class IridiumTopupFactory {
    public function __construct() {
    }

    public function create(
        $simNumber,
        $serviceTypeId,
        $reference,
        $voucher,
        $voucherQuantity,
        $extraValidity
    ) {
        return new IridiumTopupModel(
            $simNumber,
            $serviceTypeId,
            $reference,
            $voucher,
            $voucherQuantity,
            $extraValidity
        );
    }
}