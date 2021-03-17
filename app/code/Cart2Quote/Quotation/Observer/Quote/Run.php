<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSequence
 */
class Run implements ObserverInterface
{
    /**
     * Global configuration storage.
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $globalConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig
    ) {
        $this->globalConfig = $globalConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $controller = $observer->getControllerAction();
        $enabled = $this->globalConfig->getValue(
            'cart2quote_quotation/global/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$enabled) {
            $frontname = $observer->getRequest()->getFrontName();

            //make sure that we are not in the backend
            if ($frontname == "quotation") {
                $controller->getResponse()->setRedirect(
                    $controller->getUrl('')
                );
            }
        }

        return $this;
    }
}
