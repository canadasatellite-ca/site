<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Customer\Review;

use Magento\Ui\Block\Wrapper;
use Magento\Framework\View\Element\Template\Context;
use Magento\Ui\Model\UiComponentGenerator;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Listing
 *
 * @package Aheadworks\AdvancedReviews\Block\Customer\Review
 */
class Listing extends Wrapper
{
    /**
     * @param Context $context
     * @param UiComponentGenerator $uiComponentGenerator
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        UiComponentGenerator $uiComponentGenerator,
        CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $uiComponentGenerator, $data);
        $this->addData($this->getComponentConfigData($customerSession));
    }

    /**
     * Retrieve config data for component data provider
     *
     * @param CustomerSession $customerSession
     * @return array
     *
     * @see Wrapper::injectDataInDataSource()
     */
    private function getComponentConfigData($customerSession)
    {
        return [
            'params' => [
                'customer_id' => $customerSession->getCustomerId()
            ]
        ];
    }
}
