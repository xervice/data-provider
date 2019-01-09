<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Communication\Console;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Business\Model\Command\AbstractCommand;

/**
 * @method \Xervice\DataProvider\Business\DataProviderFacade getFacade()
 */
class GenerateCommand extends AbstractCommand
{
    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
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
     * @throws \Nette\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
