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

namespace Magedelight\Subscribenow\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magedelight\Subscribenow\Helper\Data as SubscribeHelper;
use Magedelight\Subscribenow\Model\Service\EmailService;
use Magedelight\Subscribenow\Model\Service\EmailServiceFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magedelight\Subscribenow\Logger\Logger;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magedelight\Subscribenow\Model\Service\Order\Generate;

class SubscriptionFailedObserver implements ObserverInterface
{
    /**
     * @var SubscribeHelper
     */
    private $subscribeHelper;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var Generate
     */
    private $generateOrder;
    
    /**
     * @param SubscribeHelper $subscribeHelper
     */
    public function __construct(
        SubscribeHelper $subscribeHelper,
        EmailServiceFactory $emailService,
        TimezoneInterface $timezone,
        Logger $logger,
        CheckoutSession $checkoutSession,
        Generate $generateOrder
    ) {
        $this->subscribeHelper = $subscribeHelper;
        $this->emailService = $emailService;
        $this->timezone = $timezone;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->generateOrder = $generateOrder;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->subscribeHelper->isModuleEnable()) {
            return $this;
        }
        
        $subscription = $observer->getData('subscription');
        $message = $observer->getData('exceptionMessage');
        
        if (empty($message)) {
            $message = __('Your subscription order failed. Due to card expiration, '
                    . 'insufficient funds, card number change, non availability of product etc. '
                    . 'In order to prevent discontinuation of subscription service, '
                    . 'please verify your subscription information or contact store owner for more details.');
        }
        
        $generatedTime = $this->timezone->date()->format('r');
        $storeId = $subscription->getStoreId();
        
        $vars = [
            'placed_on' => $generatedTime,
            'subscription' => $subscription,
            'store_id' => $storeId,
            'failmessage' => $message,
        ];
        
        $this->removeCurrentQuote();
        
        try {
            $this->sendEmail($vars, EmailService::EMAIL_PAYMENT_FAILED, $subscription->getSubscriberEmail());
            $this->logger->info("email sent successfully for # {$subscription->getProfileId()}");
        } catch (\Exception $ex) {
            $this->logger->info("email not send. Reason : " . $ex->getMessage());
        }
        
        return $this;
    }

    public function sendEmail($emailVariable, $type, $email)
    {
        $emailService = $this->emailService->create();
        $emailService->setStoreId($emailVariable['store_id']);
        $emailService->setTemplateVars($emailVariable);
        $emailService->setType($type);
        $emailService->setSendTo($email);
        $emailService->send();
    }
    
    private function removeCurrentQuote()
    {
        $quote = $this->generateOrder->currentQuote;
        if ($quote && $quote->getId()) {
            try {
                $this->generateOrder->currentQuote = null;
                $quote->delete();
            } catch (\Exception $ex) {
                $this->logger->info("quote is not delete " . $ex->getMessage());
            }
        }
        return true;
    }
}
