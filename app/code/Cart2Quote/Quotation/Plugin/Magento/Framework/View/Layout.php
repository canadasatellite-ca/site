<?php
/**
 * Cart2Quote
 */
namespace Cart2Quote\Quotation\Plugin\Magento\Framework\View;

/**
 * Class Layout
 * @package Cart2Quote\Quotation\Plugin\Magento\Framework\View
 */
class Layout
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Cart2Quote Enabled
     */
    protected $_enabled;

    /**
     * Cart2Quote Alternative Rendering Enabled
     */
    protected $_enabledAlternateRendering;

    /**
     * Temp check var
     * @var
     */
    protected $_isMiniCartRendered = false;

    /**
     * Temp useCache var
     * @var
     */
    protected $_useCache = true;
    /**
     * Temp counter var
     * @var
     */
    protected $_counter = 0;

    /**
     * Layout constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_enabled = $scopeConfig->getValue(
            'cart2quote_quotation/global/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $subject
     * @param string $alias
     * @param bool $useCache
     * @return array
     */
    public function beforeRenderElement(
        $subject,
        $alias = '',
        $useCache = true
    ) {
        if (!$this->_enabled || !$this->_enabledAlternateRendering) {
            return [$alias, $useCache];
        }

        if ($alias == 'minicart') {
            $this->_isMiniCartRendered = true;
            $this->_useCache = $useCache;
        } else {
            if ($this->_isMiniCartRendered) {
                //count the nesting
                $this->_counter++;
            }
        }
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterRenderElement(
        $subject,
        $result
    ) {
        if ($this->_enabled && $this->_enabledAlternateRendering && $this->_isMiniCartRendered) {
            //check for nesting
            if ($this->_counter != 0) {
                $this->_counter--;
                return $result;
            }

            $this->_isMiniCartRendered = false;
            $miniquote = $subject->renderElement('miniquote', $this->_useCache);
            return $result . $miniquote;
        }

        return $result;
    }
}
