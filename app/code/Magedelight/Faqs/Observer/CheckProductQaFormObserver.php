<?php namespace Magedelight\Faqs\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Captcha\Observer\CaptchaStringResolver;

class CheckProductQaFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    public $helper;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    public $actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    public $redirect;

    /**
     * @var CaptchaStringResolver
     */
    public $captchaStringResolver;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;
    public $faqHelper;
    public $httpContext;

    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param CaptchaStringResolver $captchaStringResolver
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        CaptchaStringResolver $captchaStringResolver,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magedelight\Faqs\Helper\Data $faqHelper
    ) {
        $this->helper = $helper;
        $this->actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->faqHelper = $faqHelper;
        $this->httpContext = $httpContext;
    }

    /**
     * Check CAPTCHA on Custom Form
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'faq_form';
        $captcha = $this->helper->getCaptcha($formId);
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        
        $guestStaus =  $this->faqHelper->getConfig('md_faq/general_product/enabled_product_guest');
        if ($captcha->isRequired() && $guestStaus && $isLoggedIn == false) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $productQaData = $controller->getRequest()->getPostValue();
                $this->messageManager->addError(__('Incorrect CAPTCHA.'));
                $this->getDataPersistor()->set($formId, $controller->getRequest()->getPostValue());
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), $productQaData['producturl']);
            }
        }
    }
    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}
