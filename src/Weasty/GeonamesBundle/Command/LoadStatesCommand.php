<?php

namespace Weasty\GeonamesBundle\Command;

use JJs\Common\Console\OutputLogger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Load States Command
 *
 * Loads states from a geonames.org data file.
 *
 */
class LoadStatesCommand extends ContainerAwareCommand
{
    /**
     * Configures this command
     */
    protected function configure()
    {
        $this
            ->setName('geonames:load:states')
            ->setDescription('Loads states into the state repository from a geonames.org data file')
            ->addArgument(
                'country', 
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                "Country to load the states defaults to all countries")
            ->addOption(
                'info', null,
                InputOption::VALUE_NONE,
                "Prints information about the states importer");
    }

    /**
     * Executes the load states command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $importer = $container->get('weasty_geonames.state.importer');

        $countries = $input->getArgument('country');

        // Display importer information if requested
        if ($input->getOption('info')) {
            $table = $this->getHelper('table');
            $table->setLayout(TableHelper::LAYOUT_BORDERLESS);
            $table->setHeaders(['Feature', 'Repository']);

            foreach ($importer->getLocalityRepositories() as $feature => $repository) {
                $table->addRow([$feature, get_class($repository)]);
            }

            $table->render($output);

            return;
        }

        // Import the specified countries
        $importer->import($countries, new OutputLogger($output));
    }
}