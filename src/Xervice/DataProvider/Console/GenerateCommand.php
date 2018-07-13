<?php
declare(strict_types=1);


namespace Xervice\DataProvider\Console;


use Core\Locator\Dynamic\ServiceNotParseable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Xervice\Console\Command\AbstractCommand;
use Xervice\Core\Locator\Locator;
use Xervice\DataProvider\Generator\Exception\GenerateDirectoryNotWriteable;

/**
 * @method \Xervice\DataProvider\DataProviderFacade getFacade()
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
     * @throws \Core\Locator\Dynamic\ServiceNotParseable
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start generating...');
        try {
            $generated = $this->getFacade()->generateDataProvider();
        } catch (GenerateDirectoryNotWriteable $e) {
            Locator::getInstance()->exceptionHandler()->facade()->handleException($e);
        }
        if ($output->isVerbose()) {
            foreach ($generated as $provider) {
                $output->writeln($provider . ' generated');
            }
        }
    }

}