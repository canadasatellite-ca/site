<?php

namespace MageSuper\Faq\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Security\Model\ConfigInterface;
use Magedelight\Faqs\Model\ResourceModel\Faq\Collection;
use Magedelight\Faqs\Model\ResourceModel\Faq\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class CheckFaqTime implements ObserverInterface
{
    const LIMIT_TIME_BETWEEN_REQUESTS = 120;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ConfigInterface
     */
    private $securityConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        RemoteAddress $remoteAddress,
        ConfigInterface $securityConfig,
        RequestInterface $request
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dateTime = $dateTime;
        $this->remoteAddress = $remoteAddress;
        $this->securityConfig = $securityConfig;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        $remoteIp = $this->remoteAddress->getRemoteAddress();

        $recaptchaScore = $this->request->getParam('score_v3');
        $observer->getData('object')->setRemoteIp($remoteIp)
                                        ->setData('score_v3', $recaptchaScore);

        $lastRecordCreationTimestamp = $this->loadLastRecordCreationTimestamp($remoteIp);

        if ($lastRecordCreationTimestamp && (
                self::LIMIT_TIME_BETWEEN_REQUESTS >
                ($this->dateTime->gmtTimestamp() - $lastRecordCreationTimestamp)
            )) {
            throw new SecurityViolationException(
                __(
                    'Too many FAQs requests. Please wait and try again in 2 minutes.'
                )
            );
        }

        return $this;

    }

    private function loadLastRecordCreationTimestamp($remoteIp)
    {
        /** @var \Magedelight\Faqs\Model\ResourceModel\Faq\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('remote_ip', $remoteIp);
        $record = $collection->getLastItem();
        return (int) strtotime($record->getCreationTime());
    }

}