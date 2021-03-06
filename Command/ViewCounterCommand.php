<?php
namespace Acilia\Bundle\ViewCounterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ViewCounterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('acilia:process-views')
            ->setDescription('Process views count no realtime');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('acilia.view.counter')->processViews();

        return 0;
    }
}
