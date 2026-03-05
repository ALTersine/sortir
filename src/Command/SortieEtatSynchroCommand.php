<?php

namespace App\Command;

use App\Repository\SortieRepository;
use App\SortieService\EtatManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sortie-etat-synchro',
    description: 'Permet de sychroniser les états de toutes les sorties hors déjà archivé',
)]
class SortieEtatSynchroCommand extends Command
{
    public function __construct(
        private readonly SortieRepository       $sortieRepo,
        private readonly EtatManager            $etatService,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('agree', null, InputOption::VALUE_OPTIONAL, 'Confirmer et poursuivre');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /**
         * VERSION AVEC CONFIRMATION POUR LE FAIRE EN CONSOLE
         *
        $confirmOption = ['oui', 'non'];
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Êtes-vous sûr de vouloir mettre à jour tous les états des sorties ? -Les sorties déjà archivées ne sont pas concernées-',
            $confirmOption,
            'oui');

        $question->setErrorMessage('Veuillez indiquer "oui" pour continuer ou "non" pour annuler l\'opération');

        $confirm = $helper->ask($input, $output, $question);

        if ($confirm !== 'oui') {
            $io->warning('Aucune mise à jour n\'a été opérée');

            return Command::SUCCESS;
        }
         */

        foreach ($this->sortieRepo->findUnarchived() as $sortie) {
            $this->etatService->settingEtat($sortie);
            $this->em->persist($sortie);
        }

        $this->em->flush();

        $io->success('Les états de vos sorties sont à jour');

        return Command::SUCCESS;
    }
}
