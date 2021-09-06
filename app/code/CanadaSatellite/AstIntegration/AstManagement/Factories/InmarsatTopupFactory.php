<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Factories;

use CanadaSatellite\AstIntegration\AstManagement\Models\InmarsatTopupModel;

class InmarsatTopupFactory {
    public function __construct() {
    }

    public function create(
        $simNumber,
        $serviceTypeId,
        $reference,
        $vouchers = []
    ) {
        return new InmarsatTopupModel(
            $simNumber,
            $serviceTypeId,
            $reference,
            $vouchers
        );
    }
}