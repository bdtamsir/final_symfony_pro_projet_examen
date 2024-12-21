<?php

namespace App\Command;

use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-article-etat',
    description: 'Met à jour l\'état des articles existants.'
)]
class UpdateArticleEtatCommand extends Command
{
    private ArticlesRepository $articlesRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->articlesRepository = $articlesRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $articles = $this->articlesRepository->findAll();
        foreach ($articles as $article) {
            $article->updateEtat(); // Recalculer l'état
            $this->entityManager->persist($article);
        }
        $this->entityManager->flush();

        $output->writeln('Mise à jour des états des articles terminée.');
        return Command::SUCCESS;
    }
}
