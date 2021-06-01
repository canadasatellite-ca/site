<?php

namespace CanadaSatellite\Theme\Plugin\Controller;

use Magedelight\Faqs\Controller\Faq\Addquetion;

class FaqAddquetion
{
    function beforeExecute(Addquetion $subject)
    {
        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $subject->getRequest();

        if (!$request->getPostValue('phone')) {
            $request->setPostValue('phone', '');
        }
    }
}