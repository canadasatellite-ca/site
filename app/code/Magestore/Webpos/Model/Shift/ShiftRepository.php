<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:42
 */

namespace Magestore\Webpos\Model\Shift;
use Magento\Framework\Exception\CouldNotSaveException;
use Magestore\Webpos\Api\Data\Shift\ShiftInterface;


class ShiftRepository implements \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
{
    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */

    protected $_shiftFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory */

    protected $_shiftCollectionFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /** @var  \Magestore\Webpos\Helper\Permission */
    protected $_permissionHelper;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    public function __construct(
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,     
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Helper\Shift $shiftHelper
    ) {
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->shiftResource = $shiftResource;
        $this->_storeManager = $storeManager;
        $this->_shiftFactory = $shiftFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_permissionHelper = $permissionHelper;
        $this->_shiftHelper = $shiftHelper;
    }

    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface
     */
    public function getList()
    {
        $staff = $this->_permissionHelper->getCurrentStaffModel();
        $staffId = $staff->getStaffId();
        $shift_data = [];
        $shiftModel = $this->_shiftFactory->create();
        $shift_data = $shiftModel->getList($staffId);

        $searchResult = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magestore\Webpos\Api\Data\Shift\ShiftSearchResults'
        );

        $searchResult->setItems($shift_data);
        $searchResult->setTotalCount(count($shift_data));

        return $searchResult;
       
    }

    /**
     * get detail information of a shift with specify shift_id
     * this function call to detail function of Shift Model
     * @param int $shift_id
     * @return mixed
     */
    public function detail($shift_id)
    {
        $shiftModel = $this->_shiftFactory->create();
        $data = $shiftModel->detail($shift_id);

        return $data;
    }

    /**
     * save a shift with its data in $shift (ShiftInterface)
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return mixed
     * @throws CouldNotSaveException
     */
    public function save(ShiftInterface $shift)
    {
        //$indexeddbId = $shift->getIndexeddbId();
        $shiftId = $shift->getShiftId();

        $shiftModel = $this->_shiftFactory->create();

        if(!$shiftId){
            return;
        }
        else {
            $shiftModel->load($shiftId,"shift_id");
        }

        if($shiftModel->getShiftId()){
            $shift->setEntityId($shiftModel->getEntityId());
        }
        else{
            $shift->setEntityId(null);
        }

        if($shift->getStatus() == 1){

            $balance = $shift->getData("base_closed_amount") - $shift->getData("base_cash_left");
            if($balance > 0){
                //create removed cash transaction
                $cashTransactionData = [
                    "shift_id" => $shift->getData("shift_id"),
                    "location_id" => $shift->getData("location_id"),
                    "value" => $shift->getData("closed_amount") - $shift->getData("cash_left"),
                    "base_value" => $shift->getData("base_closed_amount") - $shift->getData("base_cash_left"),
                    "note" => "Remove cash when closed Shift",
                    "balance" => $shift->getData("balance"),
                    "base_balance" => $shift->getData("base_balance"),
                    "type" => "remove",
                    "base_currency_code" => $shift->getData("base_currency_code"),
                    "transaction_currency_code" =>  $shift->getData("shift_currency_code"),
                ];
                $transactionModel = $this->_cashTransactionFactory->create();
                $transactionModel->setData($cashTransactionData);

                try {
                    $transactionModel->save();
                } catch (\Exception $exception) {

                    throw new CouldNotSaveException(__($exception->getMessage()));
                }
            }
        }

        try {
            $this->shiftResource->save($shift);
        } catch (\Exception $exception) {


            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        $shiftData = $this->_shiftHelper->prepareOfflineShiftData($shift->getShiftId());
        $response[] = $shiftData;

        return $response;
    }


}