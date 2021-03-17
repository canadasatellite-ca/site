<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class Escaper
 *
 * @package Cart2Quote\Quotation\Helper
 */
class Escaper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Escaper constructor.
     *
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->escaper = $escaper;
    }

    /**
     * Get escaper
     *
     * @return \Magento\Framework\Escaper
     */
    public function getEscaper()
    {
        return $this->escaper;
    }
}
