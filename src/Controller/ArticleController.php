<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticlesType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticlesFormSearchType;
use App\Dto\ArticlesFormSearch;

class ArticleController extends AbstractController
{
    #[Route('/admin/articles/liste', name: 'article.index')]
    public function index(ArticlesRepository $articlesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $articlesFormSearch = new ArticlesFormSearch();
        $searchArticlesForm = $this->createForm(ArticlesFormSearchType::class, $articlesFormSearch);
        $searchArticlesForm->handleRequest($request);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 9;

        if ($searchArticlesForm->isSubmitted() && $searchArticlesForm->isValid()) {
            $libelle = $articlesFormSearch->getLibelle();
            $etat = $articlesFormSearch->getEtat();

            $articles = $articlesRepository->searchArticles($libelle, $etat, $page, $limit);
        } else {
            $articles = $articlesRepository->findPaginatedArticles($page, $limit);
        }


         // Récupérer le total des résultats
         $totalResults = $articles->count();

         // Calculer le nombre de pages
         $nbrePage = ceil($totalResults / $limit);
 
         // Assurer que la page demandée est valide
         $page = min($page, $nbrePage);

        //------------------------------------------//
        // Mettre à jour l'état des articles existants
        foreach ($articles as $article) {
            $article->updateEtat(); // Recalculer l'état
            $entityManager->persist($article);
        }
        $entityManager->flush();

        
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'searchArticlesForm' => $searchArticlesForm->createView(),
            'nbrePage' => $nbrePage,
            'page' => $page
        ]);
    }


    #[Route('/admin/articles/create', name: 'article.create')]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $articles = new Articles();
        $form = $this->createForm(ArticlesType::class, $articles);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $qteStock = $articles->getQteStock();
            $etat = $qteStock > 0 ? 'Disponible' : 'En rupture';
            $articles->setEtat($etat);

            $entityManagerInterface->persist($articles);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('article.index');
        }


        return $this->render('article/form.html.twig', [
            'formArticle' => $form->createView(),
        ]);
    }



    
    
    
    #[Route('/admin/articles/{id}/update-stock', name: 'article.update_stock')]
    public function updateStock(Articles $article, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer un formulaire simple pour la mise à jour de la quantité
        $form = $this->createFormBuilder($article)
            ->add('qteStock', null, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entrer la nouvelle quantité'
                ]
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-dark'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour l'état dynamiquement
            $article->setEtat($article->getQteStock() > 0 ? 'Disponible' : 'En rupture');

            $entityManager->flush();

            return $this->redirectToRoute('article.index');
        }

        return $this->render('article/update_stock.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }
}
