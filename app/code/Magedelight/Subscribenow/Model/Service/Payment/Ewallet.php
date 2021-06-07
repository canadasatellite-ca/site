<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service\Payment;

use Magento\Framework\ObjectManagerInterface;

class Ewallet
{

    /**
     * @var Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var array
     */
    private $data;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CyberSource constructor
     * @param ObjectManagerInterface $objectManager
     * @param object $cardCollectionFactory
     * @param object $order
     * @param array $data
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $cardCollectionFactory,
        $order,
        array $data = []
    ) {

        $this->order = $order;
        $this->data = $data;
        $this->objectManager = $objectManager;
    }

    /**
     * Get Payment Token
     * @return string
     */
    public function getPaymentToken()
    {
        return '';
    }
    
    public function getTitle()
    {
        return __("Magedelight EWallet");
    }

    public function checkBalance($grandTotal)
    {
        $walletModel = $this->objectManager->create(\Magedelight\EWallet\Model\ResourceModel\WalletHistory::class);
        $subscription = $this->getSubscription();
        $customerWallet = $walletModel->loadByCustomerId1($subscription->getCustomerId());
        if ($customerWallet['remaining_wallet_amount'] > $grandTotal) {
            return true;
        }
        return false;
    }

    /**
     * return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    private function getSubscription()
    {
        return  $this->data['subscription_instance'];
    }

    /**
     * @return mixed
     */
    public function getMethodCode()
    {
        return 'magedelight_ewallet';
    }

    public function getImportData()
    {
        return [ 'method' => 'free'];
    }
}
