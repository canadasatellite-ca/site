<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard;

/**
 * Class DiagramsPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Backend\Block\Dashboard
 */
class DiagramsPlugin
{
    /**
     * Add our quote tab to the diagrams on the dashboard
     *
     * @param \Magento\Backend\Block\Dashboard\Diagrams $subject
     * @param \Magento\Backend\Block\Dashboard\Diagrams $entity
     * @return \Magento\Backend\Block\Dashboard\Diagrams
     * @throws \Exception
     */
    public function afterSetLayout(
        \Magento\Backend\Block\Dashboard\Diagrams $subject,
        \Magento\Backend\Block\Dashboard\Diagrams $entity
    ) {
        $entity->addTab(
            'quotes',
            [
                'label' => __('Quotes'),
                'content' => $entity->getLayout()->createBlock(
                    \Cart2Quote\Quotation\Block\Adminhtml\Dashboard\Tab\Quotes::class
                )->toHtml()
            ]
        );

        return $entity;
    }
}
