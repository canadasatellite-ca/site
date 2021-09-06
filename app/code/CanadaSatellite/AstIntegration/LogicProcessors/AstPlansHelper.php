<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class AstPlansHelper {
    private $data;

    public function __construct() {
        $file = file_get_contents('app/code/CanadaSatellite/AstIntegration/etc/plans.json');
        $this->data = json_decode($file, true);
    }

    /**
     * @param string $provider
     * @param string $plan
     * @return array|null
     */
    public function get($provider, $plan) {
        $providerPlans = $this->data[$provider];
        if (!isset($providerPlans)) return null;

        return isset($providerPlans[$plan])
            ? $providerPlans[$plan]
            : null;
    }

    /**
     * @param string $key1
     * @param string $key2
     * @return array
     */
    public function getMeta($key1, $key2) {
        return $this->data['@meta'][$key1][$key2];
    }
}