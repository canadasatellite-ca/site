<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Admin\Quote;

class QuoteCreator
{
    use \Cart2Quote\Features\Traits\Model\Admin\Quote\QuoteCreator {
        getQuoteCreator as private traitGetQuoteCreator;
    }

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    public $authSession;

    /**
     * QuoteCreator constructor.
     *
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession
    ){
        $this->authSession = $authSession;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getQuoteCreator(){
        return $this->traitGetQuoteCreator();
    }
}

