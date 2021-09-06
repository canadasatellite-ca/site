<?php

namespace CanadaSatellite\AstIntegration\AstManagement\Models;

class InmarsatVoucherModel implements \JsonSerializable {
    private $voucher;
    private $quantity;

    public function __construct(
        $voucher,
        $quantity
    ) {
        $this->voucher = $voucher;
        $this->quantity = $quantity;
    }

    public function jsonSerialize() {
        return [
            'Voucher' => $this->voucher,
            'Quantity' => $this->quantity
        ];
    }

    public function getVoucher() {
        return $this->voucher;
    }

    public function getQuantity() {
        return $this->quantity;
    }


}