<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request;

/**
 * Class RequestStrategyContainer
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request
 */
class RequestStrategyContainer extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var \Cart2Quote\Quotation\Block\Quote\Request\Provider
     */
    private $blockProvider;

    /**
     * @var string
     */
    private $blockChildAlias = 'addtoquote.button';

    /**
     * RequestStrategyContainer constructor.
     *
     * @param \Cart2Quote\Quotation\Block\Quote\Request\Provider $blockProvider
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Block\Quote\Request\Provider $blockProvider,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockProvider = $blockProvider;

        if (isset($data['blockChildAlias'])) {
            $this->blockChildAlias = $data['blockChildAlias'];
        }
    }

    /**
     * Prepare layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $blockChildAlias = $this->blockChildAlias;
        if (!$this->getChildBlock($blockChildAlias)) {
            $this->addChild($blockChildAlias, $this->blockProvider->getBlockClass());
        }

        return $this;
    }
}
