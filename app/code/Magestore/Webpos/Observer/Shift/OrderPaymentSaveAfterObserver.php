<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Observer\Shift;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magestore\Webpos\Model\Shift\Shift;
use Magento\Framework\Exception\CouldNotSaveException;



class OrderPaymentSaveAfterObserver implements ObserverInterface
{
    /** @var \Magestore\Webpos\Model\Shift\ShiftFactory  */
    protected $_shiftFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory */
    protected $_orderPaymentCollectionFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory */
    protected $_cashTransactionCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;

    protected $_orderIncrementId;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * SalesOrderAfterPlaceObserver constructor.
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory
     * @param \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory $cashTransactionCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
     */
    public function __construct(
       \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
       \Magestore\Webpos\Model\ResourceModel\Payment\OrderPayment\CollectionFactory $orderPaymentCollectionFactory,
       \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
       \Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction\CollectionFactory $cashTransactionCollectionFactory,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
       \Magestore\Webpos\Helper\Shift $shiftHelper,
       \Magestore\Webpos\Api\Sales\OrderRepositoryInterface $orderRepository


    ) {
        $this->_shiftFactory = $shiftFactory;
        $this->_orderPaymentCollectionFactory = $orderPaymentCollectionFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_cashTransactionCollectionFactory = $cashTransactionCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        $this->_shiftHelper = $shiftHelper;
        $this->_orderRepository = $orderRepository;
    }

    /**
     * create cash transaction for the current opening shift of the staff after an order is created.
     * @param EventObserver $observer
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(EventObserver $observer)
    {
        $orderPayment = $observer->getEvent()->getDataObject();

        $shiftId = $orderPayment->getShiftId();
        $order_id = $orderPayment->getOrderId();
        $order = $this->_orderRepository->get($order_id);
        $this->_orderIncrementId = $order->getIncrementId();

        if (!$shiftId) {
            return $this;
        }

        $shiftModel = $this->_shiftFactory->create();
        $shiftModel->load($shiftId,"shift_id");
        if(!$shiftModel->getShiftId()){
            return $this;
        }

        //update shift information: total_sales, cash_added, cash_removed
        $shiftData = $this->updateShiftWhenCreateOrder($orderPayment, $shiftModel);
        if($orderPayment->getMethod() == "cashforpos"){
            $this->createCashTransactionWhenCreateOrder($orderPayment->getData(), $shiftData);
        }

        return $this;
    }

    /**
     * update shift information: total_sales, cash_added, cash_removed
     * @param $orderPaymentCollection
     * @param $shiftId
     * @return array
     */
    public function updateShiftWhenCreateOrder($orderPayment, $shiftModel){
        $cashSale = $shiftModel->getCashSale();
        $cashAdded = $shiftModel->getCashAdded();
        $balance = $shiftModel->getBalance();
        $totalSales = $shiftModel->getTotalSales();

        if($orderPayment->getMethod() == 'cashforpos'){
            $cashSale = $cashSale + $orderPayment->getRealAmount();
            $cashAdded = $cashAdded + $orderPayment->getRealAmount();
            $balance = $balance + $orderPayment->getRealAmount();
        }
        $totalSales = $totalSales + $orderPayment->getRealAmount();

        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCashSale = $this->_webposCurrencyHelper->convertToBase($cashSale, $currentCurrencyCode);
        $baseCashAdded = $this->_webposCurrencyHelper->convertToBase($cashAdded, $currentCurrencyCode);
        $baseBalance = $this->_webposCurrencyHelper->convertToBase($balance, $currentCurrencyCode);
        $baseTotalSales = $this->_webposCurrencyHelper->convertToBase($totalSales, $currentCurrencyCode);

        $shiftModel->setCashSale($cashSale);
        $shiftModel->setBaseCashSale($baseCashSale);
        $shiftModel->setCashAdded($cashAdded);
        $shiftModel->setBaseCashAdded($baseCashAdded);
        $shiftModel->setBalance($balance);
        $shiftModel->setBaseBalance($baseBalance);
        $shiftModel->setTotalSales($totalSales);
        $shiftModel->setBaseTotalSales($baseTotalSales);


        try {
            $shiftModel->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        
        return $shiftModel->getData();
    }

    /**
     * @param $orderPaymentData
     * @param $shiftData
     * @throws CouldNotSaveException
     */
    public function createCashTransactionWhenCreateOrder($orderPaymentData, $shiftData){

        if($orderPaymentData['real_amount'] == 0){
            return;
        }
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $data['shift_id'] = $shiftData['shift_id'];
        $data['location_id'] = $shiftData['location_id'];
        $data['value'] = $orderPaymentData['real_amount'];
        $data['base_value'] = $this->_webposCurrencyHelper->convertToBase($data['value'], $currentCurrencyCode);
        $data['balance'] = $shiftData['balance'];
        $data['base_balance'] = $this->_webposCurrencyHelper->convertToBase($data['balance'], $currentCurrencyCode);
        $data['note'] = "Add cash from order with id=" . $this->_orderIncrementId ;
        $data['order_id'] = $orderPaymentData['order_id'];
        $data['type'] = "order";
        $data['base_currency_code'] = $baseCurrencyCode;
        $data['transaction_currency_code'] = $currentCurrencyCode;
        $cashTransactionModel->setData($data);

        try {
            $cashTransactionModel->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }


}
