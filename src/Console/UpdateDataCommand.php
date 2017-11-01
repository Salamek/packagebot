<?php

namespace Salamek\PackageBot\Console;

use Salamek\PackageBot\PackageBot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 1.11.17
 * Time: 13:10
 */
class UpdateDataCommand extends Command
{
    protected function configure()
    {
        $this->setName('packagebot:data:update')
            ->setDescription('Updates all data for transporters')
            ->addOption('transporter', 't', InputOption::VALUE_OPTIONAL, 'Define transporters to sync');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PackageBot $packageBot */
        $packageBot = $this->getHelper('container')->getByType('Salamek\PackageBot\PackageBot');
        $transporter = $input->getOption('transporter');

        $transporterNames = array_filter(explode(',', $transporter));
        
        try {
            $packageBot->dataUpdate($transporterNames);
            $output->writeLn('All presenters successfully generated');
            return 0; // zero return code means everything is ok
        } catch (\Exception $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error
        }
    }
}