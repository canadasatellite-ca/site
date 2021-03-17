<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Container;

/**
 * Class QuoteProposalAcceptedIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
class QuoteProposalAcceptedIdentity extends AbstractQuoteIdentity
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Container\QuoteProposalAcceptedIdentity {
        getRecieverEmail as private traitGetRecieverEmail;
        getRecieverName as private traitGetRecieverName;
    }

    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'cart2quote_email/quote_proposal_accepted/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'cart2quote_email/quote_proposal_accepted/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'cart2quote_email/quote_proposal_accepted/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'cart2quote_email/quote_proposal_accepted/template';
    const XML_PATH_EMAIL_ENABLED = 'cart2quote_email/quote_proposal_accepted/enabled';
    /**
     * @var \Magento\Framework\Mail\Template\SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * QuoteProposalAcceptedIdentity constructor.
     *
     * @param \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($scopeConfig, $storeManager);
        $this->senderResolver = $senderResolver;
    }

    /**
     * Get reciever email
     *
     * @return string
     */
    public function getRecieverEmail()
    {
        return $this->traitGetRecieverEmail();
    }

    /**
     * Get reciever name
     *
     * @return string
     */
    public function getRecieverName()
    {
        return $this->traitGetRecieverName();
    }
}
