<?php


namespace Xervice\DataProvider\Console;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;

/**
 * @method \Xervice\DataProvider\DataProviderFacade getFacade()
 */
class GenerateCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('dataprovider:generate')
            ->setDescription('Generate all data provider classes');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start generating...');
        $generated = $this->getFacade()->generateDataProvider();
        if ($output->isVerbose()) {
            foreach ($generated as $provider) {
                $output->writeln($provider . ' generated');
            }
        }
    }

}