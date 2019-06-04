<?php

namespace App\Command;

use App\Manager\DashboardManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatisticalGenerateCommand extends Command
{
    /**
     * @var DashboardManager
     */
    private $dashboardManager;

    /**
     * @param DashboardManager $dashboardManager
     */
    public function __construct(
        DashboardManager $dashboardManager
    ) {
        $this->dashboardManager = $dashboardManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:statistical:generate')
            ->setDescription('Statistical generated')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dashboardManager->generate();
    }
}
