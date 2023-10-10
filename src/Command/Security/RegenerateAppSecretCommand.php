<?php

namespace App\Command\Security;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'secret:regenerate-app-secret',
    description: 'Regenerate a random value and update APP_SECRET',
)]
class RegenerateAppSecretCommand extends Command
{
    public function __construct(#[Autowire('%kernel.project_dir%')] private readonly string $projectDir)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('envfile', InputArgument::REQUIRED, 'env File {.env, .env.local}');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $envname = $input->getArgument('envfile');

        if ($envname && ($envname == '.env' || $envname == '.env.local')) {
            $io->note(sprintf('You chose to update: %s', $envname));
            $secret = bin2hex(random_bytes(16));
            $filepath = realpath($this->projectDir . '/' . $envname);
            $io->note(sprintf('Editing file: %s', $filepath));

            $fileContent = file_get_contents($filepath);
            $regex = '/^(APP_SECRET=+[0-9a-z]{32})/m';
            $appNewSecret = "APP_SECRET=$secret";
            $result = preg_replace($regex, $appNewSecret, $fileContent);
            file_put_contents($filepath, $result);
            $io->success('New APP_SECRET was generated: ' . $secret);
            return Command::SUCCESS;
        }
        $io->error("You did not provide a valid environment file to change");
        return Command::INVALID;
    }
}