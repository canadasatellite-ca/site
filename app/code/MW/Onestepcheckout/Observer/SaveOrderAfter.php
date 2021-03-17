<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveOrderAfter implements ObserverInterface
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

    /**
     * @var \Magento\Sales\Model\Order\Address
     */
    protected $_addressModel;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
	 * @var \Magento\Framework\Logger\Monolog
	 */
	protected $_logger;

	/**
	 * @param \MW\Onestepcheckout\Helper\Data $dataHelper
	 * @param \Magento\Sales\Model\Order\Address $addressModel
	 * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\Logger\Monolog $logger
	 */
	public function __construct(
		\MW\Onestepcheckout\Helper\Data $dataHelper,
		\Magento\Sales\Model\Order\Address $addressModel,
		\Magento\Framework\Session\SessionManagerInterface $sessionManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\Logger\Monolog $logger
	) {
		$this->_dataHelper = $dataHelper;
		$this->_addressModel = $addressModel;
		$this->_sessionManager = $sessionManager;
		$this->_customerSession = $customerSession;
		$this->_customerFactory = $customerFactory;
		$this->_checkoutSession = $checkoutSession;
		$this->_logger = $logger;
	}

	/**
	 * Save customer information after save order
	 *
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if (!$this->_dataHelper->enabledInFrontend()) {
			return $this;
		}

		$order = $observer->getEvent()->getOrder();
		try {
			// Remove postcode with value = '.'
			$billingModel = $this->_addressModel;
			$billing = $order->getBillingAddress();
			if (!$this->_dataHelper->onlyProductDownloadable()) {
		  		$shipping = $order->getShippingAddress();
				$billingModel->load($shipping->getId());
		  		if ($billingModel->getPostcode() == ".") {
	  				$billingModel->setPostcode('')->setId($shipping->getId());
	  				$billingModel->save();
	  			}
			}

	  		$billingModel->load($billing->getId());
	  		if ($billingModel->getPostcode() == ".") {
  				$billingModel->setPostcode('')->setId($billing->getId());
  				$billingModel->save();
  			}

  			$isLoggedIn = $this->_customerSession->isLoggedIn();
  			if ($isLoggedIn && $this->_sessionManager->getAccountInfor()) {
  				$accountinformation = $this->_sessionManager->getAccountInfor();
  				// Save account information
				$customerId = $this->_customerSession->getCustomerId();
				$customer = $this->_customerFactory->create()->load($customerId);

				// Dob
				if ($accountinformation[0] != "") {
					$dateofbirth = date("Y-m-d",strtotime($accountinformation[0]));
					$customer->setDob($dateofbirth);
				}
				// Gender
				if ($accountinformation[1] != "") {
					$customer->setGender($accountinformation[1]);
				}
				// Taxvat
				if ($accountinformation[2] != "") {
					$customer->setTaxvat($accountinformation[2]);
				}
				// Suffix
				if ($accountinformation[3] != "") {
					$customer->setSuffix($accountinformation[3]);
				}
				// Prefix
				if ($accountinformation[4] != "") {
					$customer->setPrefix($accountinformation[4]);
				}
				// Middlename
				if ($accountinformation[5] != "") {
					$customer->setMiddlename($accountinformation[5]);
				}
				// Firstname
				if ($accountinformation[6] != "") {
					$customer->setFirstname($accountinformation[6]);
				}
				// Lastname
				if ($accountinformation[7] != "") {
					$customer->setLastname($accountinformation[7]);
				}

				$customer->setEntityId($customerId);
				$customer->save();
				$this->_customerSession->setCustomer($customer);
  				// Unset session account
  				$this->_customerSession->unsAccountInfor();
  			} else {
  				$this->_checkoutSession->setQuoteId($order->getQuoteId());
  			}
		} catch(\Exception $e) {
			$this->_logger->critical($e);
  		}

  		return $this;
	}
}
