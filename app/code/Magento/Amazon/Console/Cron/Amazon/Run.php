<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Console\Cron\Amazon;

use Magento\Amazon\Cron\ListingStateMachineActionFactory;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Framework\App\State;
use Magento\Framework\Lock\LockManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Run
 *
 * Adding in a console command to isolate ASC listing state machine
 * logic that is normally run via ASC cron task.
 */
class Run extends Command
{
    /**
     * Lock name used to ensure that there's only one instance of synchronization running
     */
    public const LOCK_NAME = 'cli_channels_amazon';

    /** @var State */
    private $appState;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;
    /**
     * @var ListingStateMachineActionFactory
     */
    private $listingStateMachineActionFactory;
    /**
     * @var LockManagerInterface
     */
    private $lockManager;

    /**
     * Constructor
     *
     * @param State $appState
     * @param ListingStateMachineActionFactory $listingStateMachineActionFactory
     * @param AscClientLogger $ascClientLogger
     * @param LockManagerInterface $lockManager
     */
    public function __construct(
        State $appState,
        ListingStateMachineActionFactory $listingStateMachineActionFactory,
        AscClientLogger $ascClientLogger,
        LockManagerInterface $lockManager
    ) {
        $this->appState = $appState;
        $this->ascClientLogger = $ascClientLogger;
        $this->listingStateMachineActionFactory = $listingStateMachineActionFactory;
        $this->lockManager = $lockManager;
        parent::__construct(null);
    }

    /**
     * Renders the CLI command name and description to the user.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('channel:amazon:run');
        $this->setDescription('Run the ASC listing state machine.');
    }

    /**
     * Execute the ASC listing state machine logic if the command from the user is invoked.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ascClientLogger->info('CLI used to run ASC cron jobs.');

        if ($this->lockManager->isLocked('CRON_GROUP_channel_amazon')) {
            $this->ascClientLogger->info('Stopping CLI execution in parallel to cron job.');
            $output->writeln('Stopping command execution because it runs in parallel to cron job');
            return;
        }
        if ($this->lockManager->isLocked(self::LOCK_NAME)) {
            $this->ascClientLogger->info('Stopping CLI execution in parallel to another command.');
            $output->writeln(
                'Stopping command execution because it runs in parallel to another instance of the command'
            );
            return;
        }

        if (!$this->lockManager->lock(self::LOCK_NAME, 5)) {
            $this->ascClientLogger->info('Cannot acquire lock for the command.');
            $output->writeln('Cannot acquire lock for the command.');
            return;
        }

        try {
            //note: appears this is not immediately set, but takes effect eventually?
            $this->appState->setAreaCode('adminhtml');

            // Executes scheduled cron actions
            /** @var \Magento\Amazon\Cron\ListingStateMachineAction $action */
            $action = $this->listingStateMachineActionFactory->create();
            $action->runTasks();
            $this->ascClientLogger->info(
                'CLI command for ASC cron jobs completed.'
            );
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $this->ascClientLogger->critical(
                'Exception occurred during Amazon Cron run using CLI',
                [
                    'exception' => $e
                ]
            );
        } finally {
            $this->lockManager->unlock(self::LOCK_NAME);
        }
    }
}
