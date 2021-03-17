<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service;

use Magento\Quote\Model\Quote;
use Magedelight\Subscribenow\Helper\Data as DataHelper;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\PriceCurrency;

/**
 * Class CalculationService acts as wrapper around actual
 * CalculatorInterface so logic valid for all calculations like
 * min order amount is only done once.
 */
class CalculationService
{
    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PriceCurrency
     */
    private $priceCurrency;
    
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * Base Amount
     */
    private $amount = 0;

    /**
     * CalculationService constructor.
     *
     * @param DataHelper $helper
     * @param LoggerInterface $logger
     * @param PriceCurrency $priceCurrency
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        DataHelper $helper,
        LoggerInterface $logger,
        PriceCurrency $priceCurrency,
        SubscriptionService $subscriptionService
    ) {
    
        $this->helper = $helper;
        $this->logger = $logger;
        $this->priceCurrency = $priceCurrency;
        $this->subscriptionService = $subscriptionService;
    }
    
    /**
     *
     * @param type $id
     * @return type
     */
    private function getProduct($id)
    {
        if ($this->products[$id]) {
            return $this->products[$id];
        } else {
            return $this->setProduct($id);
        }
    }
    
    /**
     * @return $this
     */
    public function calculate(Quote $quote, $type)
    {
        $this->amount = 0;
        if (!$this->helper->isModuleEnable()) {
            return $this->amount;
        }

        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            if ($this->subscriptionService->isSubscribed($item) &&
                !$this->isFutureSubscription($type, $item)
            ) {
                $this->amount += $this->getPrice($item, $type);
            }
        }

        return $this;
    }
    
    /**
     * Return converted amount
     * @return float
     */
    public function getAmount()
    {
        return $this->getConvertedPrice($this->amount);
    }

    /**
     * Return base amount
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->amount;
    }

    /**
     * @param object $item
     * @return float
     */
    private function getPrice($item, $type)
    {
        $amount = 0;
        if ($type == 'init_amount') {
            $amount = $item->getProduct()->getData('initial_amount');
        } elseif ($item->getProduct()->getData('allow_trial') && $type == 'trial_amount') {
            $amount = $item->getQty() * $item->getProduct()->getData('trial_amount');
        }

        return $amount;
    }

    /**
     * @param float $amount
     * @return mixed
     */
    private function getConvertedPrice($amount)
    {
        return $this->priceCurrency->convert($amount);
    }
    
    private function isFutureSubscription($type, $item)
    {
        if ($type == 'trial_amount') {
            if ($this->subscriptionService->isFutureItem(
                $item->getBuyRequest()->getData()
            )) {
                return true;
            }
        }
        return false;
    }
}
