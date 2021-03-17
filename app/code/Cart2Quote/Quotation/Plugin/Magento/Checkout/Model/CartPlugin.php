<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Checkout\Model;

/**
 * Class CartPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Checkout\Model
 */
class CartPlugin
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * CartPlugin constructor
     *
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Check if allowed function AddProduct
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int|\Magento\Catalog\Model\Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return array
     * @throws \Exception
     */
    public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $subject,
        $productInfo,
        $requestInfo = null
    ) {
        $this->allowedMethod();

        return [$productInfo, $requestInfo];
    }

    /**
     * Check if allowed function AddProductsByIds
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param array $productIds
     * @return array
     * @throws \Exception
     */
    public function beforeAddProductsByIds(\Magento\Checkout\Model\Cart $subject, $productIds)
    {
        $this->allowedMethod();

        return [$productIds];
    }

    /**
     * Check if allowed function UpdateItems
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function beforeUpdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        $this->allowedMethod();

        return [$data];
    }

    /**
     * Check if allowed function UpdateItem
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int|array|\Magento\Framework\DataObject $requestInfo
     * @param null|array|\Magento\Framework\DataObject $updatingParams
     * @return array
     * @throws \Exception
     */
    public function beforeUpdateItem(
        \Magento\Checkout\Model\Cart $subject,
        $requestInfo = null,
        $updatingParams = null
    ) {
        $this->allowedMethod();

        return [$requestInfo, $updatingParams];
    }

    /**
     * Check if allowed function
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param $itemId
     * @return array|int
     * @throws \Exception
     */
    public function beforeRemoveItem(\Magento\Checkout\Model\Cart $subject, $itemId)
    {
        $this->allowedMethod();

        return [$itemId];
    }

    /**
     * Blocks method if active confirm mode is set true to session
     *
     * @throws \Exception
     */
    public function allowedMethod()
    {
        $confirmationMode = $this->helper->getActiveConfirmMode();

        if ($confirmationMode) {
            $this->messageManager->addComplexNoticeMessage(
                'logoutFromConfirmationModeMessage',
                [
                    'url' => $this->urlBuilder->getUrl('quotation/cart/clearCart')
                ]
            );

            throw new \Magento\Framework\Exception\LocalizedException(
                __('Action is blocked in quote confirmation mode.')
            );
        }
    }
}
