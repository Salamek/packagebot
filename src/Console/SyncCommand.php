<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
namespace Salamek\PackageBot\Console;

use Salamek\PackageBot\PackageBot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends Command
{
    protected function configure()
    {
        $this->setName('packagebot:sync')
            ->setDescription('Synchronize packages')
            ->addOption('transporter', 't', InputOption::VALUE_OPTIONAL, 'Define transporters to sync');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PackageBot $packageBot */
        $packageBot = $this->getHelper('container')->getByType('Salamek\PackageBot\PackageBot');

        $transporterNames = array_filter(explode(',', $input->getOption('transporter')));

        try {
            $packageBot->flush($transporterNames);
            $output->writeLn('Packages has been sent');
            return 0; // zero return code means everything is ok

        } catch (\Exception $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error
        }
    }
}