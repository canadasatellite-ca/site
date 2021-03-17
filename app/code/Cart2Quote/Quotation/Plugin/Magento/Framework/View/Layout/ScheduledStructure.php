<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Framework\View\Layout;

/**
 * Class ScheduledStructure
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Framework\View\Layout
 */
class ScheduledStructure
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Temp check var
     *
     * @var
     */
    protected $_isMiniCartRendered = false;

    /**
     * Temp useCache var
     *
     * @var
     */
    protected $_useCache = true;

    /**
     * Temp counter var
     *
     * @var
     */
    protected $_counter = 0;

    /**
     * ScheduledStructure constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Cancel the move of miniquote to header-wrapper when header-wrapper is removed in xml
     * - Or when alternative rendering mode is enabled
     *
     * @param \Magento\Framework\View\Layout\ScheduledStructure $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetListToMove($subject, $result)
    {
        if (!$this->quotationHelper->isFrontendEnabled()) {
            return $result;
        }

        $sheduledRemoves = $subject->getListToRemove();
        $sheduledRemoveKey = array_search('header-wrapper', $sheduledRemoves);

        if ($sheduledRemoveKey) {
            $moveKey = array_search('miniquote', $result);
            if ($moveKey) {
                unset($result[$moveKey]);
            }
        }

        return $result;
    }
}
