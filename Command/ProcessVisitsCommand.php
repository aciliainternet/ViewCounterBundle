<?php
namespace Acilia\Bundle\CountVisitsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessVisitsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('acilia:process-visits')
            ->setDescription('Process visits count no realtime');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $visitsCounterService = $this->getContainer()->get('acilia.count.visits');
        $visitsCounterService->processViews();
    }
}
