<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class SimFactory {
    function __construct() {

    }

    function create($simNumber, $accountId, $orderId, $network, $service = null, $type = null, $plan = null) {
        return new Sim($simNumber, $accountId, $orderId, $network, $type, $service, $plan);
    }
}