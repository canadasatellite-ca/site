<?php

namespace CanadaSatellite\Theme\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;

class SimActivate extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * Customer session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * The ActivationForm Factory
     *
     * @var \Interactivated\ActivationForm\Model\ActivationformFactory
     */
    protected $_requestFactory;

    /**
     * Activation email sender
     *
     * @var \Interactivated\ActivationForm\Email\EmailSender
     */
    protected $_emailSender;

    /**
     * The SIM Factory
     *
     * @var \CanadaSatellite\Theme\Model\SimFactory
     */
    protected $_simFactory;

    /**
     * Address utils
     *
     * @var \CanadaSatellite\DynamicsIntegration\Utils\AddressUtils
     */
    protected $_addressUtils;

    /**
     * Logger
     *
     * @var \CanadaSatellite\DynamicsIntegration\Logger\Logger
     */
    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Interactivated\ActivationForm\Model\ActivationformFactory $requestFactory,
        \Interactivated\ActivationForm\Email\EmailSender $emailSender,
        \CanadaSatellite\Theme\Model\SimFactory $simFactory,
        \CanadaSatellite\DynamicsIntegration\Utils\AddressUtils $addressUtils,
        \CanadaSatellite\SimpleAmqp\Publisher $publisher,
        \CanadaSatellite\DynamicsIntegration\Config\Config $config,
        \CanadaSatellite\DynamicsIntegration\Envelope\ActivationFormEnvelopeFactory $envelopeFactory,
        \CanadaSatellite\DynamicsIntegration\Event\EventFactory $eventFactory,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->_requestFactory = $requestFactory;
        $this->_emailSender = $emailSender;
        $this->_simFactory = $simFactory;
        $this->_addressUtils = $addressUtils;
        $this->_publisher = $publisher;
        $this->_config = $config;
        $this->_envelopeFactory = $envelopeFactory;
        $this->_eventFactory = $eventFactory;
        $this->_logger = $logger;

        parent::__construct($context);
    }

    protected function _getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    protected function _getSession()
    {
        return $this->_customerSession;
    }

    public function dispatch(RequestInterface $request)
    {
        if (!$this->_getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }
    public function execute()
    {
        try {      
            $simIds = $this->_request->getParam('sims');

            $customer = $this->_getCustomer()->getDataModel();
            $customerId = $customer->getId();
            $customerEmail = $customer->getEmail();

            $this->_logger->info("[SIM Activate] Activate SIMs of customer $customerId ...");
            
            $this->_logger->info("[SIM Activate] Activate SIMs: " . var_export($simIds, true));

            $activatedSimsIds = array();

            foreach ($simIds as $simId) {
                try {
                    $sim = $this->_simFactory->create()->load($simId);

                    if (!$sim->getId()) {
                        $this->_logger->info("[SIM Acitivate] SIM $simId not found");
                        $this->addErrorMessage('SIM does not longer exist');
                        continue;
                    }

                    if ($sim->getMagentoCustomerId() != $customerId) {
                        $this->_logger->info("[SIM Activate] SIM $simId does not belong to $customerId");
                        $this->addErrorMessage("SIM {$sim->getSimNumber()} does not longer exist");
                        continue;
                    }

                    if (!$sim->isIssued()) {
                        $this->_logger->info("[SIM Activate] SIM $simId can not be activated. SIM status is not ISSUED but {$sim->getNetworkStatus()}");
                        $this->addErrorMessage("SIM {$sim->getSimNumber()} could be activated only in Issued network status.");
                        continue;
                    }
                    if ($sim->wasRecentlyActivated()) {
                        $this->_logger->info("[SIM Activate] SIM $simId activation was already requested during last 3 days. Skipping...");
                        continue;
                    }

                    $request = $this->createRequest($customer, $sim);
                    $this->_logger->info("[SIM Activate] SIM $simId activation request created");

                    $this->setRequestAccount($request, $customer);
                    $this->_logger->info("[SIM Activate] Set account of customer $customerId on activation request");

                    $activatedSimsIds []= $simId;

                    $this->_emailSender->sendActivationEmails($request);
                    $this->_logger->info("[SIM Activate] SIM $simId activation emails sent");
                } catch (\Exception $e) {
                    $this->_logger->info("[SIM Activate] Failed to activate SIM $simId for customer $customerId: " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
                    $this->addErrorMessage("Sorry, an error has occured on SIM {$sim->getSimNumber()} activation");
                }
            }

            if (!empty($activatedSimsIds)) {
                $this->addSuccessMessage("Your activation request(-s) has been received and is in progress. The activation may take up to 2 business days to complete. Once your service(s) are activated, you will receive an Activation Confirmation email, which will include your new satellite phone number and expiry date (if applicable).");
                
            }

            return $this->redirect($activatedSimsIds);
        } catch (\Exception $e) {
            $this->_logger->info("[SIM Activate] Failed to activate SIMs for customer $customerId: " . $e->getMessage() . "\r\nStack trace: " . $e->getTraceAsString());
            $this->addErrorMessage("An error has occured");
            return $this->redirect();
        }
    }

    private function createRequest($customer, $sim)
    {
        $email = $customer->getEmail();
        $firstname = $customer->getFirstname();
        $lastname = $customer->getLastname();
        $company = $this->getCompany($customer);
        $orderNumber = null;
        $simNumber = $sim->getSimNumber();

        $request = $this->_requestFactory->create();
        $request->setData(array(
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'company' => $company,
            'order_number' => $orderNumber,
            'sim_number' => $simNumber,
            'notes' => null,
        ));
        $request->save();

        return $request;
    }

    private function setRequestAccount($request, $customer)
    {
        $this->_publisher->publish(
            $this->_config->getIntegrationQueue(),
            $this->_eventFactory->createActivationFormSavedEvent(
                $request->getId(),
                $this->_envelopeFactory->create($request, $customer)
            )
        );
    }

    private function getCompany($customer)
    {
        $address = $this->_addressUtils->getCustomerDefaultBillingAddress($customer);
        if ($address === null) {
            return null;
        }

        return $address->getCompany();
    }

    private function addSuccessMessage($message)
    {
        $this->messageManager->addSuccess(__($message));
    }

    private function addErrorMessage($error)
    {
        $this->messageManager->addError(__($error));
    }

    private function redirect($activatedSimsIds = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if (!is_array($activatedSimsIds)) {
            $activatedSimsIds = array();
        }

        if (count($activatedSimsIds) > 0) {
            $params = array(
                '_query' => array(
                    'activated' => $activatedSimsIds,
                )
            );
            return $resultRedirect->setPath('*/*/viewsim', $params);
        }

        return $resultRedirect->setPath('*/*/viewsim');
    }
}