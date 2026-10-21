<?php 

namespace App\Policy;

use App\Model\Entity\News;
use Authorization\IdentityInterface;

class NewsPolicy
{
    public function canAdd(IdentityInterface $user, News $article)
    {
        // All logged in users can create articles.
        return true;
    }

    public function canEdit(IdentityInterface $user, News $article)
    {
        // logged in users can edit their own articles.
        return $this->isAuthor($user, $article);
    }

    public function canDelete(IdentityInterface $user, News $article)
    {
        // logged in users can delete their own articles.
        return $this->isAuthor($user, $article);
    }

    protected function isAuthor(IdentityInterface $user, News $article)
    {
        return $article->user_id === $user->getIdentifier();
    }
}

// Добавить в каждый метод контроллера для проверки доступа
// $this->Authorization->authorize($entity);


 ?>