<?php

namespace App\Command;

use App\Service\MyLudo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:refresh-latest-games',
    description: 'Regénère le cache des dernières arrivées sur MyLudo.',
)]
final class RefreshLatestGamesCommand extends Command
{
    public function __construct(private readonly MyLudo $myLudo)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Nombre de jeux à précharger', 12);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $limit = (int) $input->getOption('limit');
        if ($limit <= 0) {
            $io->error('Le paramètre --limit doit être un entier positif.');

            return Command::INVALID;
        }

        $io->section(sprintf('Récupération des %d derniers jeux depuis MyLudo…', $limit));

        $games = $this->myLudo->getLatestArrivals($limit);
        $count = count($games);

        if (0 === $count) {
            $io->warning('Aucun jeu récupéré. Vérifiez les identifiants MyLudo et les logs pour plus de détails.');

            return Command::FAILURE;
        }

        $io->success(sprintf('%d jeu(x) ont été mis en cache.', $count));

        return Command::SUCCESS;
    }
}
