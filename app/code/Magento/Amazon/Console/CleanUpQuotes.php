<?php

declare(strict_types=1);

namespace Magento\Amazon\Console;

use Magento\Amazon\Model\DeadlockRetriesTrait;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Quote\Api\Data\CartInterface;
use Symfony\Component\Console\Input\InputOption;

class CleanUpQuotes extends \Symfony\Component\Console\Command\Command
{
    use DeadlockRetriesTrait;

    private const QUOTES_LIMIT = 1000;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * CleanUpQuotes constructor.
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->cartRepository = $cartRepository;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->setName('channel:amazon:quotes-cleanup');
        $this->setDescription('Delete quotes for Amazon customer accounts');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force deleting');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $totalQuotesFound = $this->getTotalQuotesCount();
        if (!$totalQuotesFound) {
            $output->writeln('No quotes found that belong to amazon customers');
            return 0;
        }
        $output->writeln('Quotes to be deleted: ' . $totalQuotesFound);
        if (!($input->getOption('force'))) {
            $output->writeln(
                'Command execution aborted. Please add --force flag if you want to delete these addresses.'
            );
            return 1;
        }

        $this->deleteQuotes($totalQuotesFound, $output);
    }

    /**
     * @param int $totalQuotesFound
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function deleteQuotes(
        int $totalQuotesFound,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {
        $i = 1;
        $collection = $this->getQuotesCollection();
        while ($collection->getSize()) {
            $quotes = $collection->getItems();
            /** @var CartInterface $quote */
            foreach ($quotes as $quote) {
                $output->writeln(
                    sprintf(
                        'Deleting quote %d of %s (%s%%)',
                        $i,
                        $totalQuotesFound,
                        round($i / $totalQuotesFound * 100)
                    )
                );
                try {
                    $this->doWithDeadlockRetries(
                        function () use ($quote) {
                            $this->cartRepository->delete($quote);
                        },
                        20
                    );
                } catch (DeadlockException | LockWaitException $exception) {
                    // will try it again next time
                }

                $i++;
            }
            $collection = $this->getQuotesCollection();
        }
    }

    /**
     * @return \Magento\Quote\Model\ResourceModel\Quote\Collection
     */
    private function getQuotesCollection(): \Magento\Quote\Model\ResourceModel\Quote\Collection
    {
        /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $quoteCollection */
        $quoteCollection = $this->quoteCollectionFactory->create();
        $quoteCollection->removeAllFieldsFromSelect();
        $quoteCollection->addFieldToFilter('customer_email', ['like' => '%@marketplace.amazon.com']);
        $quoteCollection->setPageSize(self::QUOTES_LIMIT);
        return $quoteCollection;
    }

    private function getTotalQuotesCount(): int
    {
        $quoteCollection = $this->getQuotesCollection();
        return $quoteCollection->getSize();
    }
}
