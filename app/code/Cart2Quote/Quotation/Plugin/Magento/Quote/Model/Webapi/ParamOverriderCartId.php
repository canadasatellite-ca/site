<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Webapi;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartManagementInterface;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel
 */
class ParamOverriderCartId extends \Magento\Quote\Model\Webapi\ParamOverriderCartId
{
    /**
     * User context
     *
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * Cart Management
     *
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * ParamOverriderCartId constructor
     *
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        UserContextInterface $userContext,
        CartManagementInterface $cartManagement,
        \Magento\Framework\App\RequestInterface $request,
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
        $this->request = $request;
        $this->userContext = $userContext;
        $this->cartManagement = $cartManagement;
        parent::__construct($userContext, $cartManagement);
    }

    /**
     * If the origin URL has quotation then the mine from the REST API
     * - will be the quotation cart id instead of the Magento cart id
     *
     * @param \Magento\Quote\Model\Webapi\ParamOverriderCartId $subject
     * @return int|null
     */
    public function aroundGetOverriddenValue($subject)
    {
        try {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $referer = $this->request->getHeader('Referer');
                if (strpos($referer, 'quotation') !== false) {
                    return $this->quoteSession->getQuoteId();
                } else {
                    $customerId = $this->userContext->getUserId();

                    /** @var \Magento\Quote\Api\Data\CartInterface */
                    $cart = $this->cartManagement->getCartForCustomer($customerId);
                    if ($cart) {
                        return $cart->getId();
                    }
                }
            }
        } catch (NoSuchEntityException $e) {
            //do nothing and just return null
            return null;
        }

        return null;
    }
}
