<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Aheadworks\AdvancedReviews\Model\Import as ImportModel;
use Magento\Framework\Console\Cli;
use Aheadworks\AdvancedReviews\Model\Import\Exception\ImportReviewsException;

/**
 * Class Import
 * @package Aheadworks\AdvancedReviews\Console\Command
 */
class Import extends Command
{
    /**
     * @var ImportModel
     */
    private $importModel;

    /**
     * @param ImportModel $importModel
     */
    public function __construct(
        ImportModel $importModel
    ) {
        $this->importModel = $importModel;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function configure()
    {
        $this->setName('advanced-reviews:import')
            ->setDescription('Imports native reviews data to Aheadworks Advanced Reviews');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws ImportReviewsException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Reviews import started');
        $totalImported = $this->importModel->importExistingReviews();
        $returnValue = Cli::RETURN_SUCCESS;
        $output->writeln(
            'Reviews import finished. ' . $totalImported . ' reviews have been imported successfully'
        );

        return $returnValue;
    }
}
