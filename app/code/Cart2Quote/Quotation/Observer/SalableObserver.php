<?php
/**
 * Cart2Quote
 */

namespace Cart2Quote\Quotation\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RenderObserver
 *
 * @package Cart2Quote\Quotation\Observer
 */
class SalableObserver implements ObserverInterface
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Cart2Quote Theme Enabled
     */
    protected $_enabled;

    /**
     * Cart2Quote Module Enabled
     */
    protected $_enabledModule;

    /**
     * Cart2Quote Alternative Rendering Enabled
     */
    protected $_enabledAlternateRendering;

    /**
     * Module manager
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * HideConditions constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_coreRegistry = $coreRegistry;
        $this->_moduleManager = $moduleManager;

        $this->_enabled = $scopeConfig->getValue(
            'cart2quote_quotation/global/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_enabledModule = $moduleManager->isEnabled('Cart2Quote_Quotation');
    }

    /**
     * The function that gets executed when the event is observed
     * It registers the products that are used in situations where the salable state is checked
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_enabledModule || !$this->_enabled || !$this->_enabledAlternateRendering) {
            return;
        }

        $product = $observer->getProduct();
        $products = [];
        if ($this->_coreRegistry->registry('c2q_current_salable_product')) {
            $products = $this->_coreRegistry->registry('c2q_current_salable_product');
            $this->_coreRegistry->unregister('c2q_current_salable_product');
        }

        if ($product->getId()) {
            $products[$product->getId()] = $product;
        }

        $this->_coreRegistry->register('c2q_current_salable_product', $products);
    }
}
