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

namespace Magedelight\Subscribenow\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Magedelight\Subscribenow\Helper\Data as DataHelper;
use Magedelight\Subscribenow\Model\Service\CalculationService;

/**
 * Class TrialAmountConfigProvider
 * @package Magedelight\Subscribenow\Model
 */
class TrialAmountConfigProvider implements ConfigProviderInterface
{
    /**
     * @var DataHelper
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param DataHelper $helper
     * @param Session $checkoutSession
     * @param CalculationService $calculator
     */
    public function __construct(DataHelper $helper, Session $checkoutSession, CalculationService $calculator)
    {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->calculator = $calculator;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $trialAmountConfig = [];
        
        if (!$this->helper->isModuleEnable()) {
            return $trialAmountConfig;
        }
        
        $quote = $this->checkoutSession->getQuote();
        $fees = $this->calculator->calculate($quote, 'trial_amount');
        
        $trialAmountConfig[Subscription::TRIAL_AMOUNT_FIELD_NAME.'_title'] = $this->helper->getTrialAmountTitle();
        $trialAmountConfig[Subscription::TRIAL_AMOUNT_FIELD_NAME] = $fees->getAmount();
        $trialAmountConfig['show_hide_'.Subscription::TRIAL_AMOUNT_FIELD_NAME] = $fees->getAmount() > 0.0;
        return $trialAmountConfig;
    }
}
