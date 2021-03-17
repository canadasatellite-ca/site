<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Product;

/**
 * Class Create
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Product
 */
class Create extends \Magento\Backend\App\Action
{
    /**
     * @const create new product setting value
     */
    const CREATE_NEW_PRODUCT = '1';

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\CustomProduct
     */
    protected $customProduct;

    /**
     * Create constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Model\Quote\CustomProduct $customProduct
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Cart2Quote\Quotation\Model\Quote\CustomProduct $customProduct
    ) {
        $this->customProduct = $customProduct;
        parent::__construct($context);
    }

    /**
     * Ececute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $productParams = $this->getRequest()->getParams();
            if (isset($productParams['setting']) && $productParams['setting'] === self::CREATE_NEW_PRODUCT) {
                $response = $this->customProduct->createNewProduct($productParams);
            } else {
                $response = $this->customProduct->useExistingProduct($productParams);
            }
            $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
            $resultJson->setData($response);

            return $resultJson;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::actions');
    }
}
