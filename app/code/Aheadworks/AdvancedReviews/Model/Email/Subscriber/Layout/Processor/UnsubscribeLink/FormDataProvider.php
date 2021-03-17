<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink;

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as EmailSubscriberResolver;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\RequestInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\AbstractFormDataProvider;
use Aheadworks\AdvancedReviews\Model\Data\Extractor as DataExtractor;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink
 * @codeCoverageIgnore
 */
class FormDataProvider extends AbstractFormDataProvider
{
    /**
     * Request param key for email security code
     */
    const SECURITY_CODE_REQUEST_PARAM_KEY = 'code';

    /**
     * @var EmailSubscriberResolver
     */
    private $emailSubscriberResolver;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ArrayManager $arrayManager
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Config $config
     * @param DataExtractor $dataExtractor
     * @param EmailSubscriberResolver $emailSubscriberResolver
     * @param RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        DataObjectProcessor $dataObjectProcessor,
        Config $config,
        DataExtractor $dataExtractor,
        EmailSubscriberResolver $emailSubscriberResolver,
        RequestInterface $request
    ) {
        parent::__construct(
            $arrayManager,
            $dataObjectProcessor,
            $config,
            $dataExtractor
        );
        $this->emailSubscriberResolver = $emailSubscriberResolver;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscriberFormProviderPath()
    {
        return 'components/awArEmailSubscriberFormProvider';
    }

    /**
     * {@inheritdoc}
     */
    protected function getPreparedSubscriberData($subscriber)
    {
        $preparedSubscriberData = parent::getPreparedSubscriberData($subscriber);
        $preparedSubscriberData[self::SECURITY_CODE_REQUEST_PARAM_KEY] = $this->request->getParam(
            self::SECURITY_CODE_REQUEST_PARAM_KEY,
            ''
        );
        return $preparedSubscriberData;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCurrentSubscriber()
    {
        $securityCode = $this->request->getParam(self::SECURITY_CODE_REQUEST_PARAM_KEY, '');
        $subscriber = $this->emailSubscriberResolver->getBySecurityCode($securityCode);
        return $subscriber;
    }
}
