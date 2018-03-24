<?php


namespace Xervice\DataProvider\Console;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

/**
 * @method \Xervice\DataProvider\DataProviderFacade getFacade()
 */
class CleanCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('dataprovider:clean')
            ->setDescription('Remove all data provider classes');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     * @throws \Xervice\Config\Exception\ConfigNotFound
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->getFacade()->cleanDataProvider();
    }

}