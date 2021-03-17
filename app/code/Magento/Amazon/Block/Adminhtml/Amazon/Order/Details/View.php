<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Order\Details;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View
 */
class View extends Container
{
    /** @var string */
    protected $_template = 'amazon/order/details/view.phtml';

    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Api\AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->frontendUrl = $frontendUrl;
        $this->accountRepository = $accountRepository;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_order_details';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';
        $this->setData('id', 'channel_amazon_order_details_index');

        $backUrl = $this->getBackUrl();
        /** @var array */
        $statuses = [
            Definitions::UNSHIPPED_ORDER_STATUS
        ];

        $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
        $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $backUrl . '\')');
        $this->buttonList->update('back', 'class', 'spectrumButton spectrumButton--secondary back');
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');

        try {
            try {
                /** @var OrderInterface */
                $order = $this->orderRepository->getByOrderId($this->getRequest()->getParam('id'));
            } catch (NoSuchEntityException $e) {
                return;
            }

            if (in_array($order->getStatus(), $statuses)) {

                /** @var string */
                $url = $this->getUrl('channel/amazon/order_cancel_index', ['id' => $order->getId()]);

                // add listing actions button
                $this->buttonList->add(
                    'cancel',
                    [
                        'label' => __('Cancel Order'),
                        'onclick' => "setLocation('" . $url . "')",
                        'class' => 'add spectrumButton spectrumButton--secondary'
                    ],
                    0
                );
            }
        } catch (NoSuchEntityException $e) {
            // do not set action buttons
        }
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('adminhtml/*/*', ['_current' => true, 'period' => null]);
    }

    /**
     * @return string
     */
    public function getBackUrl(): string
    {
        $merchantId = $this->getRequest()->getParam('merchant_id');
        if (!$merchantId) {
            return $this->frontendUrl->getHomeUrl();
        }
        try {
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return $this->frontendUrl->getHomeUrl();
        }
        return $this->frontendUrl->getStoreDetailsUrl($account);
    }
}
