<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Models;


class IridiumTopupModel implements \JsonSerializable {
    private $simNumber;
    private $serviceTypeId;
    private $reference;
    private $voucher;
    private $voucherQuantity;
    private $extraValidity;

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $reference
     * @param string $voucher
     * @param integer $voucherQuantity
     * @param integer $extraValidity
     */
    public function __construct(
        $simNumber,
        $serviceTypeId,
        $reference,
        $voucher,
        $voucherQuantity,
        $extraValidity
    ) {
        $this->simNumber = $simNumber;
        $this->serviceTypeId = $serviceTypeId;
        $this->reference = $reference;
        $this->voucher = $voucher;
        $this->voucherQuantity = $voucherQuantity;
        $this->extraValidity = $extraValidity;
    }

    public function jsonSerialize() {
        return [
            'Service' => $this->simNumber,
            'ServiceTypeId' => $this->serviceTypeId,
            'Reference' => $this->reference,
            'Voucher' => $this->voucher,
            'VoucherQuantity' => $this->voucherQuantity,
            'ExtraValidity' => $this->extraValidity
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

    public function getVoucher() {
        return $this->voucher;
    }

    public function getVoucherQuantity() {
        return $this->voucherQuantity;
    }

    public function getExtraValidity() {
        return $this->extraValidity;
    }
}