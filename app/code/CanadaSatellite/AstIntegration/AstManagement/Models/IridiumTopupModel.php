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
     * @param integer|null $extraValidity
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
        $data = [
            'Service' => $this->simNumber,
            'ServiceTypeId' => $this->serviceTypeId,
            'Reference' => $this->reference
        ];

        if (!is_null($this->voucher)) {
            $data['Voucher'] = $this->voucher;
        }

        if (!is_null($this->voucherQuantity)) {
            $data['VoucherQuantity'] = $this->voucherQuantity;
        } else {
            $data['VoucherQuantity'] = 1;
        }

        if (!is_null($this->extraValidity)) {
            $data['ExtraValidity'] = $this->extraValidity;
        }

        return $data;
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
