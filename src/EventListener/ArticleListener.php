<?php
namespace App\EventListener;

use App\Entity\Articles;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ArticleListener
{
    public function preUpdate(Articles $article, LifecycleEventArgs $event)
    {
        $article->updateEtat(); 
    }
}

