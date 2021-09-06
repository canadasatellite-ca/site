<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Models;

class InmarsatTopupModel implements \JsonSerializable {
    private $simNumber;
    private $serviceTypeId;
    private $reference;
    private $vouchers;

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $reference
     * @param array $vouchers
     */
    public function __construct(
        $simNumber,
        $serviceTypeId,
        $reference,
        $vouchers = []
    ) {
        $this->simNumber = $simNumber;
        $this->serviceTypeId = $serviceTypeId;
        $this->reference = $reference;
        $this->vouchers = $vouchers;
    }

    public function jsonSerialize() {
        return [
            'Service' => $this->simNumber,
            'ServiceTypeId' => $this->serviceTypeId,
            'Reference' => $this->reference,
            'Vouchers' => $this->vouchers
        ];
    }

    public function getSimNumber() {
        return $this->simNumber;
    }

    public function getServiceTypeId() {
        return $this->serviceTypeId;
    }

    public function getReference() {
        return $this->reference;
    }

    public function getVouchers() {
        return $this->vouchers;
    }

    public function addVoucher($voucher, $quantity) {
        array_push($this->vouchers, new InmarsatVoucherModel($voucher, $quantity));
    }
}