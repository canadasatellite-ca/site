<?php

namespace CanadaSatellite\AstIntegration\LogicProcessors;

class AstQueueItem {
    public $dataId;
    public $voucher;
    public $nextTime;
    public $simNumber;

    /**
     * @param integer $dataId AST API response queue DataId
     * @param string $simNumber ICCID of SIM
     * @param array|object|null $voucher Array of voucher data or null
     */
    public function __construct($dataId, $simNumber, $voucher = null) {
        $this->dataId = $dataId;
        $this->simNumber = $simNumber;

        if (gettype($voucher) === 'object') {
            $this->voucher = [
                'ServiceTypeId' => $voucher->ServiceTypeId,
                'Voucher' => $voucher->Voucher,
                'Quantity' => $voucher->Quantity,
                'Reference' => $voucher->Reference
            ];
        } else {
            $this->voucher = $voucher;
        }

    }

    /**
     * @param string $phoneNumber
     * @param \CanadaSatellite\AstIntegration\AstManagement\AstManager $astManager
     * @param \CanadaSatellite\DynamicsIntegration\Rest\RestApi $dynamicsApi
     * @throws \Exception
     */
    public function finalize($phoneNumber, $astManager, $dynamicsApi) {
        // Update SIM entity in Dynamics
        $sim = $dynamicsApi->getSimByNumber($this->simNumber);
        if ($sim === false) {
            throw new \Exception("AstQueueItem finalize: SIM entity not found");
        }

        $dynamicsApi->updateSim($sim->cs_simid, [
            'cs_activationdate' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
            'cs_satellitenumber' => $phoneNumber,
            'cs_simstatus' => 100000001
        ]);

        // Apply voucher
        if (!is_null($this->voucher)) {
            $data = $this->voucher;
            $astManager->processTopup($this->simNumber, $data['ServiceTypeId'], $data['Voucher'],
                $data['Quantity'], $data['Reference']);
        }
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