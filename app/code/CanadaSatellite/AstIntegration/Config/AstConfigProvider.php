<?php

namespace CanadaSatellite\AstIntegration\Config;

class AstConfigProvider {
    const AST_CREDENTIALS_USERNAME_PATH = 'ast/credentials/username';
    const AST_CREDENTIALS_PASSWORD_PATH = 'ast/credentials/password';
    const AST_BASE_URL_PATH = 'ast/urls/base_point';
    const AST_TOKEN_PATH = 'ast/urls/login';
    const AST_IRIDIUM_BASE_PATH = 'ast/urls/iridium/base_part';
    const AST_IRI_ACTIVATION_PATH = 'ast/urls/iridium/activation';
    const AST_IRI_DEACTIVATION_PATH = 'ast/urls/iridium/deactivation';
    const AST_IRI_SUSPENSION_PATH = 'ast/urls/iridium/suspension';
    const AST_IRI_UNSUSPENSION_PATH = 'ast/urls/iridium/unsuspension';
    const AST_IRI_TOPUP_PATH = 'ast/urls/iridium/topup';
    const AST_IRI_BALANCE_PATH = 'ast/urls/iridium/balance';
    const AST_IRI_SIM_SWAP_PATH = 'ast/urls/iridium/swap';
    const AST_IRI_MODIFICATION_PATH = 'ast/urls/iridium/modification';
    const AST_INMARSAT_BASE_PATH = 'ast/urls/inmarsat/base_part';
    const AST_INM_ACTIVATION_PATH = 'ast/urls/inmarsat/activation';
    const AST_INM_DEACTIVATION_PATH = 'ast/urls/inmarsat/deactivation';
    const AST_INM_SUSPENSION_PATH = 'ast/urls/inmarsat/suspension';
    const AST_INM_UNSUSPENSION_PATH = 'ast/urls/inmarsat/unsuspension';
    const AST_INM_TOPUP_PATH = 'ast/urls/inmarsat/topup';
    const AST_INM_BALANCE_PATH = 'ast/urls/inmarsat/balance';
    const AST_INM_SIM_SWAP_PATH = 'ast/urls/inmarsat/swap';
    const AST_INM_MODIFICATION_PATH = 'ast/urls/inmarsat/modification';
    const AST_INM_CREATE_VESSEL_PATH = 'ast/urls/inmarsat/create_vessel';
    const AST_ACTION_STATUS_PATH = 'ast/urls/action_status';

    private $scopeConfig;

    private $username;
    private $password;
    private $baseUrl;
    private $tokenPart;
    private $iridiumBasePart;
    private $iridiumActivationPart;
    private $iridiumDeactivationPart;
    private $iridiumSuspensionPart;
    private $iridiumUnsuspensionPart;
    private $iridiumTopupPart;
    private $iridiumBalancePart;
    private $iridiumSimSwapPart;
    private $iridiumModificationPart;
    private $inmarsatBasePart;
    private $inmarsatActivationPart;
    private $inmarsatDeactivationPart;
    private $inmarsatSuspensionPart;
    private $inmarsatUnsuspensionPart;
    private $inmarsatTopupPart;
    private $inmarsatBalancePart;
    private $inmarsatSimSwapPart;
    private $inmarsatModificationPart;
    private $inmarsatCreateVesselPart;
    private $actionStatusPart;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getAstUserName() {
        if (!isset($this->username)) {
            $this->username = $this->scopeConfig->getValue(self::AST_CREDENTIALS_USERNAME_PATH);
        }

        return $this->username;
    }

    public function getAstPassword() {
        if (!isset($this->password)) {
            $this->password = $this->scopeConfig->getValue(self::AST_CREDENTIALS_PASSWORD_PATH);
        }

        return $this->password;
    }

    public function getTokenUrl() {
        return $this->getBaseUrl() . $this->getTokenPart();
    }

    public function getIridiumActivationUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumActivationPart();
    }

    public function getIridiumDeactivationUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumDeactivationPart();
    }

    public function getIridiumSuspensionUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumSuspensionPart();
    }

    public function getIridiumUnsuspensionUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumUnsuspensionPart();
    }

    public function getIridiumTopupUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumTopupPart();
    }

    public function getIridiumBalanceUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumBalancePart();
    }

    public function getIridiumSimSwapUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumSimSwapPart();
    }

    public function getIridiumModificationUrl() {
        return $this->getBaseUrl() . $this->getIridiumBasePart() . $this->getIridiumModificationPart();
    }

    public function getInmarsatActivationUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatActivationPart();
    }

    public function getInmarsatDeactivationUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatDeactivationPart();
    }

    public function getInmarsatSuspensionUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatSuspensionPart();
    }

    public function getInmarsatUnsuspensionUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatUnsuspensionPart();
    }

    public function getInmarsatTopupUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatTopupPart();
    }

    public function getInmarsatBalanceUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatBalancePart();
    }

    public function getInmarsatSimSwapUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatSimSwapPart();
    }

    public function getInmarsatModificationUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatModificationPart();
    }

    public function getInmarsatCreateVesselUrl() {
        return $this->getBaseUrl() . $this->getInmarsatBasePart() . $this->getInmarsatCreateVesselPart();
    }

    public function getActionStatusUrl() {
        return $this->getBaseUrl() . $this->getActionStatusPart();
    }

    private function getBaseUrl() {
        if (!isset($this->baseUrl)) {
            $this->baseUrl = $this->scopeConfig->getValue(self::AST_BASE_URL_PATH);
        }

        return $this->baseUrl;
    }

    private function getTokenPart() {
        if (!isset($this->tokenPart)) {
            $this->tokenPart = $this->scopeConfig->getValue(self::AST_TOKEN_PATH);
        }

        return $this->tokenPart;
    }

    private function getIridiumBasePart() {
        if (!isset($this->iridiumBasePart)) {
            $this->iridiumBasePart = $this->scopeConfig->getValue(self::AST_IRIDIUM_BASE_PATH);
        }

        return $this->iridiumBasePart;
    }

    private function getIridiumActivationPart() {
        if (!isset($this->iridiumActivationPart)) {
            $this->iridiumActivationPart = $this->scopeConfig->getValue(self::AST_IRI_ACTIVATION_PATH);
        }

        return $this->iridiumActivationPart;
    }

    private function getIridiumDeactivationPart() {
        if (!isset($this->iridiumDeactivationPart)) {
            $this->iridiumDeactivationPart = $this->scopeConfig->getValue(self::AST_IRI_DEACTIVATION_PATH);
        }

        return $this->iridiumDeactivationPart;
    }

    private function getIridiumSuspensionPart() {
        if (!isset($this->iridiumSuspensionPart)) {
            $this->iridiumSuspensionPart = $this->scopeConfig->getValue(self::AST_IRI_SUSPENSION_PATH);
        }

        return $this->iridiumSuspensionPart;
    }

    private function getIridiumUnsuspensionPart() {
        if (!isset($this->iridiumUnsuspensionPart)) {
            $this->iridiumUnsuspensionPart = $this->scopeConfig->getValue(self::AST_IRI_UNSUSPENSION_PATH);
        }

        return $this->iridiumUnsuspensionPart;
    }

    private function getIridiumTopupPart() {
        if (!isset($this->iridiumTopupPart)) {
            $this->iridiumTopupPart = $this->scopeConfig->getValue(self::AST_IRI_TOPUP_PATH);
        }

        return $this->iridiumTopupPart;
    }

    private function getIridiumBalancePart() {
        if (!isset($this->iridiumBalancePart)) {
            $this->iridiumBalancePart = $this->scopeConfig->getValue(self::AST_IRI_BALANCE_PATH);
        }

        return $this->iridiumBalancePart;
    }

    private function getIridiumSimSwapPart() {
        if (!isset($this->iridiumSimSwapPart)) {
            $this->iridiumSimSwapPart = $this->scopeConfig->getValue(self::AST_IRI_SIM_SWAP_PATH);
        }

        return $this->iridiumSimSwapPart;
    }

    private function getIridiumModificationPart() {
        if (!isset($this->iridiumModificationPart)) {
            $this->iridiumModificationPart = $this->scopeConfig->getValue(self::AST_IRI_MODIFICATION_PATH);
        }

        return $this->iridiumModificationPart;
    }

    private function getInmarsatBasePart() {
        if (!isset($this->inmarsatBasePart)) {
            $this->inmarsatBasePart = $this->scopeConfig->getValue(self::AST_INMARSAT_BASE_PATH);
        }

        return $this->inmarsatBasePart;
    }

    private function getInmarsatActivationPart() {
        if (!isset($this->inmarsatActivationPart)) {
            $this->inmarsatActivationPart = $this->scopeConfig->getValue(self::AST_INM_ACTIVATION_PATH);
        }

        return $this->inmarsatActivationPart;
    }

    private function getInmarsatDeactivationPart() {
        if (!isset($this->inmarsatDeactivationPart)) {
            $this->inmarsatDeactivationPart = $this->scopeConfig->getValue(self::AST_INM_DEACTIVATION_PATH);
        }

        return $this->inmarsatDeactivationPart;
    }

    private function getInmarsatSuspensionPart() {
        if (!isset($this->inmarsatSuspensionPart)) {
            $this->inmarsatSuspensionPart = $this->scopeConfig->getValue(self::AST_INM_SUSPENSION_PATH);
        }

        return $this->inmarsatSuspensionPart;
    }

    private function getInmarsatUnsuspensionPart() {
        if (!isset($this->inmarsatUnsuspensionPart)) {
            $this->inmarsatUnsuspensionPart = $this->scopeConfig->getValue(self::AST_INM_UNSUSPENSION_PATH);
        }

        return $this->inmarsatUnsuspensionPart;
    }

    private function getInmarsatTopupPart() {
        if (!isset($this->inmarsatTopupPart)) {
            $this->inmarsatTopupPart = $this->scopeConfig->getValue(self::AST_INM_TOPUP_PATH);
        }

        return $this->inmarsatTopupPart;
    }

    private function getInmarsatBalancePart() {
        if (!isset($this->inmarsatBalancePart)) {
            $this->inmarsatBalancePart = $this->scopeConfig->getValue(self::AST_INM_BALANCE_PATH);
        }

        return $this->inmarsatBalancePart;
    }

    private function getInmarsatSimSwapPart() {
        if (!isset($this->inmarsatSimSwapPart)) {
            $this->inmarsatSimSwapPart = $this->scopeConfig->getValue(self::AST_INM_SIM_SWAP_PATH);
        }

        return $this->inmarsatSimSwapPart;
    }

    private function getInmarsatModificationPart() {
        if (!isset($this->inmarsatModificationPart)) {
            $this->inmarsatModificationPart = $this->scopeConfig->getValue(self::AST_INM_MODIFICATION_PATH);
        }

        return $this->inmarsatModificationPart;
    }

    private function getInmarsatCreateVesselPart() {
        if (!isset($this->inmarsatCreateVesselPart)) {
            $this->inmarsatCreateVesselPart = $this->scopeConfig->getValue(self::AST_INM_CREATE_VESSEL_PATH);
        }

        return $this->inmarsatCreateVesselPart;
    }

    private function getActionStatusPart() {
        if (!isset($this->actionStatusPart)) {
            $this->actionStatusPart = $this->scopeConfig->getValue(self::AST_ACTION_STATUS_PATH);
        }

        return $this->actionStatusPart;
    }
}