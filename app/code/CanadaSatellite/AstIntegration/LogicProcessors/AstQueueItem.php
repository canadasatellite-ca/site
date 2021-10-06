<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class AstQueueItem {
    public $dataId;
    public $voucher;
    public $nextTime;
    public $simNumber;

    public function __construct(int $dataId, string $simNumber, string $voucher = null) {
        $this->dataId = $dataId;
        $this->simNumber = $simNumber;
        $this->voucher = $voucher;
    }

    /**
     * @throws \Exception
     */
    public function finalize(string                                                   $phoneNumber,
                             \CanadaSatellite\AstIntegration\AstManagement\AstManager $astManager,
                             \CanadaSatellite\DynamicsIntegration\Rest\RestApi        $dynamicsApi) {

        // Update SIM in Dynamics 365
        $sim = $dynamicsApi->getSimByNumber($this->simNumber);
        if ($sim === false) {
            throw new \Exception("AstQueueItem finalize: SIM entity not found");
        }

        $payload = [
            'cs_activationdate' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
            'cs_satellitenumber' => $phoneNumber,
            'cs_simstatus' => 100000001, // Network Status = Active
            'new_airtimevendor' => 100000000, // Airtime Vendor == AST
        ];

        if (isset($this->voucher) && !is_null($this->voucher)) {
            $voucher = $dynamicsApi->getAstVoucherBySku($this->voucher);
            if ($voucher !== false) {
                $payload['new_substatus'] = $voucher->new_parts_count === 2
                    ? 100000017 // Sub Status = 0/2
                    : 100000004; // Sub Status = PAID
                $payload['new_ast_voucher@odata.bind'] = "/new_ast_vouchers($voucher->new_ast_voucherid)";
                $payload['cs_expirydate'] = (new \DateTime())->format('Y-m-d\TH:i:s\Z');
            }
        }

        $dynamicsApi->updateSim($sim->cs_simid, $payload);
    }

    /**
     * Converts object fields to associative array
     * @return array
     */
    public function toArray() {
        return [
            'dataId' => $this->dataId,
            'voucher' => $this->voucher,
            'nextTime' => $this->nextTime,
            'simNumber' => $this->simNumber
        ];
    }
}