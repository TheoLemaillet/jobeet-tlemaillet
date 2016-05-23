<?php
// src/Ens/TotoBundle/Command/TotoCleanupCommand.php

namespace Ens\TotoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ens\TotoBundle\Entity\Job;

class TotoCleanupCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('ens:toto:cleanup')
            ->setDescription('Cleanup Toto database')
            ->addArgument('days', InputArgument::OPTIONAL, 'The email', 90)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getArgument('days');

        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $nb = $em->getRepository('EnsTotoBundle:Job')->cleanup($days);

        $output->writeln(sprintf('Removed %d stale jobs', $nb));
    }
}