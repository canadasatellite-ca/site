<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class DeleteTierItem
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class DeleteTierItem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private $tierItemModel;

    /**
     * DeleteTierItem constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
    ) {
        $this->tierItemModel = $tierItemModel;
        parent::__construct($context);
    }


    /**
     * Remove tier quantity action
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        if (array_key_exists('tier_id', $postData)) {
            try {
                $this->tierItemModel->deleteTierItem($postData['tier_id']);
                $this->messageManager->addSuccessMessage(__('Tier quantity deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }
}
