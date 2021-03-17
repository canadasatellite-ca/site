<?php

declare(strict_types=1);

namespace Magento\Amazon\Console;

use Magento\Amazon\Model\ResourceModel\LogProcessing;
use Symfony\Component\Console\Input\InputOption;

class CleanUpLogs extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var LogProcessing
     */
    private $logProcessing;

    public function __construct(
        LogProcessing $logProcessing
    ) {
        parent::__construct(null);
        $this->logProcessing = $logProcessing;
    }

    protected function configure()
    {
        $this->setName('channel:amazon:logs-cleanup');
        $this->setDescription('Delete logs records that are being processed.');
        $this->setHelp(<<<HELP
During synchronization, the client temporary locks identifiers in a table to prevent duplicated processing of the same
record. This works quite well, but if an error have happened during synchronization,
some records could stay in a locked state for a while without being processed.

There is a cron job that is doing this kind of clean up on a regular basis,
but if you've been told by support to do clean up manually, this command can do this for you.

Please be careful with this command.
Once it would be executed, there's a chance some records could be processed twice,
which could lead to an unpredictable state of the data.
HELP
);
        $this->addOption('minutes', 'm', InputOption::VALUE_REQUIRED, 'Minutes to keep logs before deleting', 10);
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force deleting');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $minutesToKeepLogs = (int)$input->getOption('minutes');
        $logsFound = $this->logProcessing->countLogsOlderThan($minutesToKeepLogs);
        if (!$logsFound) {
            $output->writeln('No logs that needs to be cleaned up');
            return 0;
        }
        $output->writeln(
            'Logs to be deleted: ' . $logsFound
        );
        if (!($input->getOption('force'))) {
            $output->writeln(
                'Command execution aborted. Please add --force flag if you sure you want to clean up these logs'
            );
            return 1;
        }
        $this->logProcessing->deleteLogsOlderThan($minutesToKeepLogs);
    }
}
