<?php

namespace BroSolutions\GuestRegistration\Observer;

class ConvertGuestToCustomer implements \Magento\Framework\Event\ObserverInterface
{
    private $objectCopyService;

    private $encryptorInterface;

    private $random;

    private $simpleDataObjectConverter;

    public $customerSession;

    public $customerRepositoryInterface;

    public $customerFactory;

    public $addressFactory;

    public $regionFactory;

    public $scopeConfig;

    public $accountManagementInterface;

    public $storeManager;

    public $logger;

    public $customer;

    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Framework\Math\Random $random,
        \Magento\Framework\Api\SimpleDataObjectConverter $simpleDataObjectConverter,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory,
        \Magento\Customer\Api\AccountManagementInterface $accountManagementInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->objectCopyService = $objectCopyService;
        $this->encryptorInterface = $encryptorInterface;
        $this->random = $random;
        $this->simpleDataObjectConverter = $simpleDataObjectConverter;
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->accountManagementInterface = $accountManagementInterface;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customer = $customer;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $customer = false;
        $autoPlace = true;
        $optionalRegister = false;
        $email = $quote->getCustomerEmail();
        $isLoggedIn = $this->customerSession->getId();
        $isEmailAvailable = (int)$this->accountManagementInterface->isEmailAvailable($quote->getCustomerEmail());
        $tryAccountCreation = false;

        if (!$isLoggedIn && $isEmailAvailable) {
            $customer = $this->customerFactory->create(['data' => $this->prepcustomerData($quote)]);
            $customer->setWebsiteId($this->storeManager->getWebsite()->getId())
                ->setStoreId($this->storeManager->getStore()->getId());
            $quote->getShippingAddress()->setEmail($email);
            $pword = $this->random->getRandomString(8);
            $pwordH = $this->encryptorInterface->getHash($pword, true);

            try {
                $customerA = $this->accountManagementInterface
                    ->createAccountWithPasswordHash($customer, $pwordH);
                $tryAccountCreation = true;
                $this->accountManagementInterface->initiatePasswordReset(
                    $customer->getEmail(),
                    \Magento\Customer\Model\AccountManagement::EMAIL_RESET,
                    $customer->getWebsiteId()
                );
                $setCustomerAsLoggedIn = $this->customer->load($customerA->getId());
                $this->customerSession->setCustomerAsLoggedIn($setCustomerAsLoggedIn);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }

            if ($tryAccountCreation) {
                $customer = $customerA;
            }

            $isEmailAvailable = 0;
        }

        if (!$isLoggedIn && $autoPlace && !$isEmailAvailable) {
            if (!is_object($customer)) {
                $customer = $this->customerRepositoryInterface
                    ->get($email, $this->storeManager->getWebsite()->getId());

            }

            if (!empty($order) && is_object($order) && is_object($customer)) {
                $this->addUserToOrder($quote, $order, $customer);
                $order->save();
                $quote->save();
            }
        }
    }

    private function addUserToOrder(
        $quote,
        $order,
        $customer
    ) {
        $orderCustomerAttr = array_keys($order->getData());
        foreach ($orderCustomerAttr as $k) {
            if (strpos($k, 'customer_') !== false) {
                $k = str_replace('customer_', '', $k);

                $convertedKey = $this->simpleDataObjectConverter
                    ->snakeCaseToUpperCamelCase($k);
                $setMethodName = 'setCustomer' . $convertedKey;
                $getMethodName = 'get' . $convertedKey;

                if (method_exists($customer, $getMethodName)) {
                    $v = call_user_func([
                        $customer,
                        $getMethodName
                    ]);

                    if ($v) {
                        call_user_func([
                            $order,
                            $setMethodName
                        ], $v);

                        call_user_func([
                            $quote,
                            $setMethodName
                        ], $v);
                    }
                }
            }
        }
    }

    private function prepcustomerData($salesObj)
    {

        $customerData = $this->objectCopyService->copyFieldsetToTarget(
            'order_address',
            'to_customer',
            $salesObj->getBillingAddress(),
            []
        );

        if ($salesObj->getCustomerGender() && !isset($customerData['gender'])) {
            $customerData['gender'] = $salesObj->getCustomerGender();
        }

        if ($salesObj->getCustomerDob() && !isset($customerData['dob'])) {
            $customerData['dob'] = $salesObj->getCustomerDob();
        }

        if (!$salesObj->getIsVirtual()) {
            $addresses = [$salesObj->getBillingAddress(), $salesObj->getShippingAddress()];
        } else {
            $addresses = [$salesObj->getBillingAddress()];
        }

        foreach ($addresses as $address) {
            $addressData = $this->objectCopyService->copyFieldsetToTarget(
                'order_address',
                'to_customer_address',
                $address,
                []
            );
            /** @var \Magento\Customer\Api\Data\AddressInterface $customerAddress */
            $customerAddress = $this->addressFactory->create(['data' => $addressData]);
            switch ($address->getAddressType()) {
                case \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_BILLING:
                    $customerAddress->setIsDefaultBilling(true);
                    break;
                case \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING:
                    $customerAddress->setIsDefaultShipping(true);
                    break;
            }

            if (is_string($address->getRegion())) {
                /** @var \Magento\Customer\Api\Data\RegionInterface $region */
                $region = $this->regionFactory->create();
                $region->setRegion($address->getRegion());
                $region->setRegionCode($address->getRegionCode());
                $region->setRegionId($address->getRegionId());
                $customerAddress->setRegion($region);
            }
            $customerData['addresses'][] = $customerAddress;
        }

        return $customerData;
    }
}
