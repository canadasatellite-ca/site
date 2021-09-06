<?php

namespace CanadaSatellite\DynamicsIntegration\Config;

class ConfigValuesProvider {
    const SIM_LIST_ORDER_NOTE_REGEX_PATH = 'magento/regulars/get_sim_list_from_order_note';
    const SPLIT_SIMS_FROM_LIST_REGEX_PATH = 'magento/regulars/split_sims_from_list';
    const SIM_ORDER_PLAN = 'map_options/sim_order/plan';
    const TOPUP_PHONE_NUMBER = 'map_options/topup/phone_number';
    const TOPUP_TARGET_SKU = 'map_options/topup/target_sku';


    private $scopeConfig;

    function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    function getSimListOrderNoteRegex() {
        return $this->scopeConfig->getValue(self::SIM_LIST_ORDER_NOTE_REGEX_PATH);
    }

    function getSplitSimsFromListRegex() {
        return $this->scopeConfig->getValue(self::SPLIT_SIMS_FROM_LIST_REGEX_PATH);
    }

    function getSimOrderPlan() {
        return explode(',', $this->scopeConfig->getValue(self::SIM_ORDER_PLAN));
    }

    function getTopupPhoneNumber() {
        return explode(',', $this->scopeConfig->getValue(self::TOPUP_PHONE_NUMBER));
    }

    function getTopupTargetSku() {
        return explode(',', $this->scopeConfig->getValue(self::TOPUP_TARGET_SKU));
    }
}