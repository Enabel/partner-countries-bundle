<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Command\PartnerCountries;

use Doctrine\ORM\EntityManagerInterface;
use Enabel\PartnerCountriesBundle\Entity\Country;
use Enabel\PartnerCountriesBundle\Repository\CountryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'enabel:partner-countries:init',
    description: 'Add a short description for your command',
)]
class InitCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CountryRepository $countryRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp($this->getCommandHelp())
        ;
    }

    /**
     * Optional method, first one executed for a command after configure()
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('partner-countries-init-command');

        // Retrieve country class
        $countryClass = $this->countryRepository->getClassName();

        // Get countries list
        $countryCodes = Countries::getCountryCodes();

        // Create partner countries
        foreach ($countryCodes as $countryCode) {
            /** @var Country $country */
            $country = new $countryClass();
            $country->setAlpha2code($countryCode);
            if (in_array($countryCode, Country::PARTNER_COUNTRIES)) {
                $country->setIsPartner(true);
            }

            $this->entityManager->persist($country);
        }

        $this->entityManager->flush();

        $this->io->success('Base data for the partner countries table has been loaded successfully.');

        $event = $stopwatch->stop('partner-countries-init-command');
        if ($output->isVerbose()) {
            $this->io->comment(
                sprintf(
                    'New countries : %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
                    count($countryCodes),
                    $event->getDuration(),
                    $event->getMemory() / (1024 ** 2)
                )
            );
        }

        return Command::SUCCESS;
    }

    /**
     * The command help is usually included in the configure() method, but it's too long.
     * define a separate method to maintain the code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command loads the base data for the partner countries table:

  <info>php %command.full_name%</info>

This command fetches country data from the Intl\Country component and inserts it into the partner countries table. 
It ensures that your application has a consistent, up-to-date list of countries based on international standards.

<comment>Examples:</comment>
  <info>php %command.full_name%</info> 
    This example loads all the countries into the partner countries table.

Note: Ensure your database connection is correctly configured before running this command 
and also make sure the bundle is appropriately configured.

HELP;
    }
}
