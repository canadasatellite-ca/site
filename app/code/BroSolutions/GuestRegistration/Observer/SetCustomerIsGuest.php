<?php

namespace BroSolutions\GuestRegistration\Observer;

class SetCustomerIsGuest implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            if ($observer->getCheckoutSession() != null) {
                $activeForCustomer = $this->quoteRepository->getActiveForCustomer(
                    $this->customerSession->getCustomerId()
                );
                if ($activeForCustomer->getItemsCollection()->getSize()) {
                    $activeForCustomer->setCustomerIsGuest(false)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

    }
}
