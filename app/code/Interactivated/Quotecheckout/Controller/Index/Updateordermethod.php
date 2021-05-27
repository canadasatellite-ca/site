<?php
namespace Interactivated\Quotecheckout\Controller\Index;
use Cart2Quote\Quotation\Model\Session as QuotationSession;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Newsletter\Model\Subscriber;
# 2021-05-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the Interactivated_Quotecheckout module": https://github.com/canadasatellite-ca/site/issues/116
class Updateordermethod extends \Interactivated\Quotecheckout\Controller\Checkout\Onepage {
	protected $quoteFactory;
	protected $customerManagement;
	protected $sender;
	protected $helper;
	protected $quoteProposalSender;
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		CustomerRepositoryInterface $customerRepository,
		AccountManagementInterface $accountManagement,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Translate\InlineInterface $translateInline,
		\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\View\LayoutFactory $layoutFactory,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
		\Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
		\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
		\Cart2Quote\Quotation\Helper\Data $helper,
		\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
	) {
		$this->quoteProposalSender = $quoteProposalSender;
		$this->helper = $helper;
		$this->sender = $sender;
		$this->quoteFactory = $quoteFactory;
		parent::__construct($context,
			$customerSession,
			$customerRepository,
			$accountManagement,
			$coreRegistry,
			$translateInline,
			$formKeyValidator,
			$scopeConfig,
			$layoutFactory,
			$quoteRepository,
			$resultPageFactory,
			$resultLayoutFactory,
			$resultRawFactory,
			$resultJsonFactory
		);
	}

	/**
	 * 2021-05-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the Interactivated_Quotecheckout module": https://github.com/canadasatellite-ca/site/issues/116
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
	 */
	function execute() {
		$guest = false;
		$this->defineProperties();
		if (!df_checkout_h()->canOnepageCheckout()) {
			echo json_encode(['error' => 0, 'msg' => '', 'redirect' => $this->_url->getUrl('quotation/quote')]);
			exit;
		}
		// Validate checkout
		$quote = $this->getOnepage()->getQuote();
		if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
			echo json_encode(['error' => 0, 'msg' => '', 'redirect' => $this->_url->getUrl('quotation/quote')]);
			exit;
		}
		$isLoggedIn = $this->_customerSession->isLoggedIn();
		if (!$isLoggedIn) {
			if (isset($_POST['register_new_account'])) {
				$isGuest = $this->getRequest()->getPost('register_new_account');
				if ($isGuest == '1' || $this->_dataHelper->haveProductDownloadable()) {
					// If checkbox register_new_account checked or exist downloadable product, create new account
					$this->getOnepage()->saveCheckoutMethod('register');
					$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
					// Preparing data for new customer
					$customer = $this->_objectManager->get('Magento\Customer\Model\CustomerFactory')->create();
					$customer->setWebsiteId($storeManager->getWebsite()->getId())
						->setEmail(isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '')
						->setPrefix(isset($_POST['billing']['prefix']) ? $_POST['billing']['prefix'] : '')
						->setFirstname(isset($_POST['billing']['firstname']) ? $_POST['billing']['firstname'] : '')
						->setLastname(isset($_POST['billing']['lastname']) ? $_POST['billing']['lastname'] : '')
						->setMiddlename(isset($_POST['billing']['middlename']) ? $_POST['billing']['middlename'] : '')
						->setSuffix(isset($_POST['billing']['suffix']) ? $_POST['billing']['suffix'] : '')
						->setDob(isset($_POST['dob']) ? $_POST['dob'] : '')
						->setTaxvat(isset($_POST['billing']['taxvat']) ? $_POST['billing']['taxvat'] : '')
						->setGender(isset($_POST['billing']['gender']) ? $_POST['billing']['gender'] : '')
						->setPassword(isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '');
					// Set customer information to quote
					$quote->setCustomer($customer->getDataModel())->setPasswordHash($customer->getPasswordHash());
				}
				else {
					$this->getOnepage()->saveCheckoutMethod('guest');
				}
			}
			else {
				// Fix for persistent
				if (
					$this->getCheckout()->getPersistentRegister() && $this->getCheckout()->getPersistentRegister() == "register"
				) {
					$this->getOnepage()->saveCheckoutMethod('register');
				}
				else {
					if (!$this->_dataHelper->getStoreConfig('onestepcheckout/general/allowguestcheckout')
						|| !$this->_dataHelper->getStoreConfig('checkout/options/guest_checkout')
						|| $this->_dataHelper->haveProductDownloadable()
					) {
						$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
						// Preparing data for new customer
						$customer = $this->_objectManager->get(
							'Magento\Customer\Model\CustomerFactory'
						)->create();
						$guest = false;
						$email = isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '';
						if ($email) {
							$customer1 = $this->_objectManager->get(
								'Magento\Customer\Model\CustomerFactory'
							)->create();
							$customer1->loadByEmail($email);
							if ($customer1->getId()) {
								$customer->setEntityId($customer1->getEntityId());
								if (!$customer1->getPasswordHash()) {
									$customer->setEntityId($customer1->getEntityId());
									$this->_customerSession->setCustomerId($customer1->getEntityId());
								}
								else {
									$password = isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '';
									$accountManagement = $this->_objectManager->get('Magento\Customer\Api\AccountManagementInterface');
									$accountManagement->authenticate($email, $password);
									$guest = true;
								}
							}
						}
						if (!$guest) {
							$this->getOnepage()->saveCheckoutMethod('register');
						}
						else {
							$this->getOnepage()->saveCheckoutMethod('guest');
						}
						$customer->setWebsiteId($storeManager->getWebsite()->getId())
							->setEmail(isset($_POST['billing']['email']) ? $_POST['billing']['email'] : '')
							->setPrefix(isset($_POST['billing']['prefix']) ? $_POST['billing']['prefix'] : '')
							->setFirstname(isset($_POST['billing']['firstname']) ? $_POST['billing']['firstname'] : '')
							->setLastname(isset($_POST['billing']['lastname']) ? $_POST['billing']['lastname'] : '')
							->setMiddlename(isset($_POST['billing']['middlename']) ? $_POST['billing']['middlename'] : '')
							->setSuffix(isset($_POST['billing']['suffix']) ? $_POST['billing']['suffix'] : '')
							->setDob(isset($_POST['dob']) ? $_POST['dob'] : '')
							->setTaxvat(isset($_POST['billing']['taxvat']) ? $_POST['billing']['taxvat'] : '')
							->setGender(isset($_POST['billing']['gender']) ? $_POST['billing']['gender'] : '')
# 2021-05-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# @todo "«empty password in vendor/magento/framework/Encryption/Encryptor.php on line 591»
# on a quotecheckout/index/updateordermethod request": https://github.com/canadasatellite-ca/site/issues/127
							->setPassword(isset($_POST['billing']['customer_password']) ? $_POST['billing']['customer_password'] : '');
						// Set customer information to quote
						$quote->setCustomer($customer->getDataModel())->setPasswordHash($customer->getPasswordHash());
					}
					else {
						$this->getOnepage()->saveCheckoutMethod('guest');
					}
				}
			}
		}
		// Save billing address
		if ($this->getRequest()->isPost()) {
			$billingData = $this->_dataHelper->filterdata(
				$this->getRequest()->getPost('billing', []),
				false
			);
			if ($isLoggedIn) {
				$this->saveAddress('billing', $billingData);
			}
			$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
			if ($this->getRequest()->getPost('billing_address_id') != ""
				&& (!isset($billingData['save_in_address_book'])
					|| (isset($billingData['save_in_address_book']) && $billingData['save_in_address_book']) == 0)
			) {
				$customerAddressId = "";
			}
			if ($isLoggedIn
				&& (isset($billingData['save_in_address_book']) && $billingData['save_in_address_book'] == 1)
				&& !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')
			) {
				$customerAddressId = $this->getDefaultAddress('billing');
			}
			if (isset($billingData['email'])) {
				$billingData['email'] = trim($billingData['email']);
				if ($this->_dataHelper->isSubscriberByEmail($billingData['email'])) {
					if ($this->getRequest()->getParam('subscribe_newsletter') == '1') {
						if ($isLoggedIn) {
							$customer = $this->_customerSession->getCustomer();
							$this->_objectManager->get(
								'Magento\Newsletter\Model\SubscriberFactory'
							)->create()->subscribeCustomerById($customer->getId());
						} else {
							$this->saveSubscriber($billingData['email']);
						}
					}
				}
			}
			$address = $this->_objectManager->get('Magento\Quote\Model\Quote\Address');
			if ($customerAddressId) {
				$addressData = $this->_objectManager->get('Magento\Customer\Api\AddressRepositoryInterface')
					->getById($customerAddressId)
					->__toArray();
				$billingData = array_merge($billingData, $addressData);
			}
			$address->setData($billingData);
			$this->getOnepage()->getQuote()->setBillingAddress($address);
			if (isset($billingData['save_into_account'])
				&& intval($billingData['save_into_account']) == 1
				&& $isLoggedIn
			) {
				$this->setAccountInfoSession($billingData);
			}
		}
		// Save shipping address
		$isclick = $this->getRequest()->getPost('ship_to_same_address');
		$ship = "billing";
		if ($isclick != '1') {
			$ship = "shipping";
		}
		if ($this->getRequest()->getPost()) {
			$shippingData = $this->_dataHelper->filterdata($this->getRequest()->getPost($ship, []), false);
			if ($isLoggedIn && !$isclick) {
				$this->saveAddress('shipping', $shippingData);
			}
			if ($isclick == '1') {
				$shippingData['same_as_billing'] = 1;
			}
			// Change address if user change infomation
			// Reassign customeraddressid and save to shipping
			$customeraddressid = $this->getRequest()->getPost($ship . '_address_id', false);
			// If user chage shipping, billing infomation but not save to database
			if ($isclick || ($this->getRequest()->getPost('shipping_address_id') != ""
					&& (!isset($shippingData['save_in_address_book']) || (isset($shippingData['save_in_address_book']) && $shippingData['save_in_address_book'] == 0)))
			) {
				$customeraddressid = "";
			}
			if (!$isclick && $isLoggedIn
				&& (isset($shippingData['save_in_address_book']) && $shippingData['save_in_address_book'] == 1)
				&& !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')
			) {
				$customeraddressid = $this->getDefaultAddress('shipping');
			}
			$this->getOnepage()->saveShipping($shippingData, $customeraddressid);
		}
		if ($customer_note = $this->getRequest()->getPost('onestepcheckout_comments')) {
			$quote->setCustomerNote($customer_note);
		}
		if ($this->getRequest()->isPost()) {
			$shippingMethodData = $this->getRequest()->getPost('shipping_method', '');
			$resultSaveShippingMethod = $this->getOnepage()->saveShippingMethod($shippingMethodData);
			if (!$resultSaveShippingMethod) {
				$eventManager = $this->_objectManager->get('Magento\Framework\Event\ManagerInterface');
				$eventManager->dispatch('checkout_controller_onepage_save_shipping_method', [
					'request' => $this->getRequest(),
					'quote' => $this->getOnepage()->getQuote()
				]);
			}
			$this->getOnepage()->getQuote()->collectTotals();
		}
		$result = new \Magento\Framework\DataObject();
		try {
			if (!$quote->getCustomerId() && !$guest) {
				df_quote_customer_m()->populateCustomerInfo($quote);
			}
			$quote->setIsActive(false);
			$quoteModel = $this->quoteFactory->create();
			$quotation = $quoteModel->create($quote)->load($quote->getId());
			$isAutoProposalEnabled = $this->helper->isAutoConfirmProposalEnabled();
			$qtyBreak = false;
			$price = true;
			$totalItems = 0;
			foreach ($quote->getAllItems() as $item) {
				if (!$item->getParentItemId()) {
					$totalItems++;
					if ($item->getQty() > 1) {
						$qtyBreak = true;
					}
					if ($item->getProduct()->getFinalPrice() == 0 || $item->getProduct()->getPrice() == 0) {
						$price = false;
					}
				}
			}
			if ($quote->getCustomerNote() || $qtyBreak || !$price || $totalItems > 1) {
				$isAutoProposalEnabled = false;
			}
			if ($isAutoProposalEnabled) {
				$quotation->setProposalSent((new \DateTime())->getTimestamp());
				$quotation->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING)
					->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT);
				$this->quoteProposalSender->send($quotation);
				$quotation->save();
			}
			else {
				$this->sendEmailToSalesRep($quotation);
			}
			if (true || $this->getRequest()->getParam('clear_quote', false)) {
				$qs = df_o(QuotationSession::class); /** @var QuotationSession $qs */
				$qs->fullSessionClear();
				$qs->updateLastQuote($quotation);
			}
			$redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
			$result->setData('success', true);
			$result->setData('error', false);
		}
		catch (\Exception $e) {
			$data = ['error' => 1, 'msg' => $e->getMessage(),];
			$reloadcheckoutpage = $quote->getData('reloadcheckoutpage');
			if ($reloadcheckoutpage) {
				$data['redirect'] = $this->_url->getUrl('checkout');
			}
			echo json_encode($data);
			exit;
		}
		if (isset($redirectUrl)) {
			$result->setData('redirect', $redirectUrl);
		}
		$this->_eventManager->dispatch('checkout_controller_onepage_saveOrder', ['result' => $result, 'action' => $this]);
		if (isset($redirectUrl)) {
			echo json_encode([
				'error' => 0,
				'msg' => '',
				'redirect' => $redirectUrl
			]);
			exit;
		}
		echo json_encode([
			'error' => 0,
			'msg' => '',
			'redirect' => $this->_url->getUrl('quotation/quote/success', array('id' => $quote->getId()))
		]);
		exit;
		return;
	}

	/**
	 * @used-by execute()
	 * @used-by saveAddress()
	 * @param string $type
	 * @return int|string
	 */
	private function getDefaultAddress($type) {
		$customer = $this->_customerSession->getCustomer();
		if ($type == "billing") {
			$address = $customer->getDefaultBillingAddress();
			$addressId = $address->getEntityId();
		}
		else {
			$address = $customer->getDefaultShippingAddress();
			$addressId = $address->getEntityId();
		}
		return $addressId;
	}

	/**
	 * @used-by execute()
	 * @param string $type
	 * @param array $data
	 */
	private function saveAddress($type, $data) {
		$addressId = $this->getRequest()->getPost($type . '_address_id');
		if (isset($data['save_in_address_book']) && $data['save_in_address_book'] == 1) {
			if ($addressId == "" && !$this->_dataHelper->getStoreConfig('onestepcheckout/addfield/addressbook')) {
				$addressId = $this->getDefaultAddress($type);
			}
			if ($addressId != "") {
				$customer = $this->_customerSession->getCustomer();
				$addressModel = $this->_objectManager->get('Magento\Customer\Model\Address');
				$existsAddress = $customer->getAddressById($addressId);
				if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
					$addressModel->setId($existsAddress->getId());
				}
				$addressForm = $this->_objectManager->get('Magento\Customer\Model\Form');
				$addressData = $this->getRequest()->getPost($type, []);
				try {
					$addressForm->setFormCode('customer_address_edit')->setEntity($addressModel);
					$addressForm->validateData($addressData);
					$addressForm->compactData($addressData);
					$addressModel->setCustomerId($customer->getId())
						->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
						->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
					$addressModel->save();
				}
				catch (\Magento\Framework\Exception\LocalizedException $e) {
					$this->_objectManager->get('Magento\Framework\Logger\Monolog')->critical($e);
				}
			}
		}
	}

	/**
	 * @used-by execute()
	 * @param $mail
	 */
	private function saveSubscriber($mail) {
		if ($mail) {
			$email = (string)$mail;
			try {
				if (!\Zend_Validate::is($email, 'EmailAddress')) {
					throw new \Exception(__('Please enter a valid email address.'));
				}
				if ($this->_dataHelper->getStoreConfig(Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1
					&& !$this->_customerSession->isLoggedIn()
				) {
					throw new \Exception(__(
						'Sorry, but the administrator denied subscription for guests. Please <a href="%1">register</a>.',
						$this->_url->getUrl('customer/account/create/')
					));
				}
				$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
				$ownerId = $this->_objectManager->get('Magento\Customer\Model\CustomerFactory')->create()
					->setWebsiteId($storeManager->getWebsite()->getId())
					->loadByEmail($email)
					->getId();
				if ($ownerId !== null && $ownerId != $this->_customerSession->getId()) {
					throw new \Exception(__('This email address is already assigned to another user.'));
				}
				$status = $this->_objectManager->get(
					'Magento\Newsletter\Model\SubscriberFactory'
				)->create()->subscribe($email);
				if ($status == Subscriber::STATUS_NOT_ACTIVE) {
					$this->messageManager->addSuccess(__('The confirmation request has been sent.'));
				}
				else {
					$this->messageManager->addSuccess(__('Thank you for your subscription.'));
				}
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				$this->messageManager->addError(
					__('There was a problem with the subscription: %1', $e->getMessage())
				);
			}
			catch (\Exception $e) {
				$this->messageManager->addError(__('Something went wrong with the subscription.'));
			}
		}
	}

	/**
	 * @used-by execute()
	 * @param \Cart2Quote\Quotation\Model\Quote $quotation
	 * @return void
	 */
	private function sendEmailToSalesRep(\Cart2Quote\Quotation\Model\Quote $quotation) {
		$this->sender->send($quotation, false, true);
	}

	/**
	 * @used-by execute()
	 * @param array $billingData
	 */
	private function setAccountInfoSession($billingData) {
		if (!$this->getRequest()->getParam('dob')) {
			$dob = '';
		}
		else {
			$dob = $this->getRequest()->getParam('dob');
		}
		$gender = "";
		if (isset($billingData['gender'])) {
			$gender = $billingData['gender'];
		}
		$taxvat = "";
		if (isset($billingData['taxvat'])) {
			$taxvat = $billingData['taxvat'];
		}
		$suffix = "";
		if (isset($billingData['suffix'])) {
			$suffix = $billingData['suffix'];
		}
		$prefix = "";
		if (isset($billingData['prefix'])) {
			$prefix = $billingData['prefix'];
		}
		$middlename = "";
		if (isset($billingData['middlename'])) {
			$middlename = $billingData['middlename'];
		}
		$firstname = "";
		if (isset($billingData['firstname'])) {
			$firstname = $billingData['firstname'];
		}
		$lastname = "";
		if (isset($billingData['lastname'])) {
			$lastname = $billingData['lastname'];
		}
		$accountInfo = [$dob, $gender, $taxvat, $suffix, $prefix, $middlename, $firstname, $lastname];
		$this->_sessionManager->setAccountInfor($accountInfo);
	}
}