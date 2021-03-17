<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Email\Processor\AbstractProcessor;
use Aheadworks\AdvancedReviews\Model\Email\ProcessorFactory;
use Aheadworks\AdvancedReviews\Model\Email\DateTime\Resolver as EmailDateTimeResolver;
use Aheadworks\AdvancedReviews\Model\Source\Email\Status;
use Aheadworks\AdvancedReviews\Model\Email\Sender;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator as QueueItemValidator;
use Psr\Log\LoggerInterface;

/**
 * Class QueueManagement
 * @package Aheadworks\AdvancedReviews\Model
 */
class QueueManagement implements QueueManagementInterface
{
    /**
     * @var QueueItemInterfaceFactory
     */
    private $queueItemFactory;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var EmailDateTimeResolver
     */
    private $emailDateTimeResolver;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var ProcessorFactory
     */
    private $processorFactory;

    /**
     * @var QueueItemValidator
     */
    private $queueItemValidator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param QueueItemInterfaceFactory $queueItemFactory
     * @param QueueRepositoryInterface $queueRepository
     * @param EmailDateTimeResolver $emailDateTimeResolver
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProcessorFactory $processorFactory
     * @param Sender $sender
     * @param QueueItemValidator $queueItemValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        QueueItemInterfaceFactory $queueItemFactory,
        QueueRepositoryInterface $queueRepository,
        EmailDateTimeResolver $emailDateTimeResolver,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProcessorFactory $processorFactory,
        Sender $sender,
        QueueItemValidator $queueItemValidator,
        LoggerInterface $logger
    ) {
        $this->queueItemFactory = $queueItemFactory;
        $this->queueRepository = $queueRepository;
        $this->emailDateTimeResolver = $emailDateTimeResolver;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->processorFactory = $processorFactory;
        $this->sender = $sender;
        $this->queueItemValidator = $queueItemValidator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function add($type, $objectId, $storeId, $recipientName, $recipientEmail)
    {
        $result = null;
        /** @var $queueItem QueueItemInterface $object */
        $queueItem = $this->queueItemFactory->create();
        $queueItem
            ->setStoreId($storeId)
            ->setType($type)
            ->setObjectId($objectId)
            ->setStatus(Status::PENDING)
            ->setCreatedAt($this->emailDateTimeResolver->getCurrentDate())
            ->setScheduledAt($this->emailDateTimeResolver->getScheduledDateTimeInDbFormat($type, $storeId))
            ->setRecipientName($recipientName)
            ->setRecipientEmail($recipientEmail);

        if ($this->queueItemValidator->isValid($queueItem)) {
            try {
                $queueItem = $this->queueRepository->save($queueItem);
                $result = $queueItem;
            } catch (CouldNotSaveException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(QueueItemInterface $queueItem)
    {
        try {
            $queueItem->setStatus(Status::CANCELED);
            $queueItem = $this->queueRepository->save($queueItem);
        } catch (CouldNotSaveException $e) {
            throw new LocalizedException($e->getMessage());
        }

        return $queueItem;
    }

    /**
     * {@inheritdoc}
     */
    public function cancelById($queueItemId)
    {
        try {
            $queueItem = $this->queueRepository->getById($queueItemId);
            $result = $this->cancel($queueItem);
            return $result;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProcessed()
    {
        $deadlineDate = $this->emailDateTimeResolver->getDeadlineForProcessedEmails();

        $this->searchCriteriaBuilder
            ->addFilter(QueueItemInterface::STATUS, Status::getProcessedStatuses(), 'in')
            ->addFilter(QueueItemInterface::SCHEDULED_AT, $deadlineDate, 'lteq');

        /** @var QueueItemSearchResultsInterface $result */
        $result = $this->queueRepository->getList($this->searchCriteriaBuilder->create());

        /** @var QueueItemInterface $queueItem */
        foreach ($result->getItems() as $queueItem) {
            try {
                $this->queueRepository->delete($queueItem);
            } catch (CouldNotDeleteException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendScheduled()
    {
        $now = $this->emailDateTimeResolver->getCurrentDate();

        $this->searchCriteriaBuilder
            ->addFilter(QueueItemInterface::STATUS, Status::PENDING)
            ->addFilter(QueueItemInterface::SCHEDULED_AT, $now, 'lteq')
            ->setPageSize(self::SEND_LIMIT);

        /** @var QueueItemSearchResultsInterface $result */
        $result = $this->queueRepository->getList($this->searchCriteriaBuilder->create());

        /** @var QueueItemInterface $queueItem */
        foreach ($result->getItems() as $queueItem) {
            try {
                $this->send($queueItem);
            } catch (\Exception $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(QueueItemInterface $queueItem)
    {
        try {
            if ($this->queueItemValidator->isValid($queueItem)) {
                /** @var AbstractProcessor $processor */
                $processor = $this->processorFactory->create($queueItem->getType());
                $emailMetadata = $processor->process($queueItem);
                $this->sender->send($emailMetadata);
                $queueItem->setStatus(Status::SENT);
                $queueItem->setSentAt($this->emailDateTimeResolver->getCurrentDate());
                $this->queueRepository->save($queueItem);
                return true;
            } else {
                $this->cancel($queueItem);
                return false;
            }
        } catch (MailException $e) {
            $queueItem->setStatus(Status::FAILED);
            $this->queueRepository->save($queueItem);
            return false;
        } catch (NoSuchEntityException $e) {
            $queueItem->setStatus(Status::FAILED);
            $this->queueRepository->save($queueItem);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendById($queueItemId)
    {
        try {
            $queueItem = $this->queueRepository->getById($queueItemId);
            $result = $this->send($queueItem);
            return $result;
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException($e->getMessage());
        }
    }
}
