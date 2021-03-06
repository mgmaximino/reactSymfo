<?php

namespace App\Doctrine;

use App\Entity\User;
use App\Entity\Commentaires;
use App\Entity\Recettes;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;


class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface{
    private $security;
    private $auth;

    public function __construct(Security $security, AuthorizationCheckerInterface $checker)
    {
        $this->security = $security;
        $this->auth = $checker;
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass){
        // obtenir l'utilisateur connecté
        $user = $this->security->getUser();
        if(($resourceClass === Commentaires::class || $resourceClass === Recettes::class) && !$this->auth->isGranted('ROLE_ADMIN') && $user instanceof User){
            $rootAlias = $queryBuilder->getRootAliases()[0]; // permet de récupèrer l'alias de la queryBuilder. Attention, ici on récupère un tableau et on veut le 1er

            if($resourceClass === Commentaires::class){
                $queryBuilder->andWhere("$rootAlias.user = :user"); // on veut que ça soit relié à notre utilisateur connecté
            } elseif($resourceClass === Recettes::class){
                $queryBuilder->join("$rootAlias.customer", "c")
                    ->andWhere("c.user = :user"); // on veut voir les factures de notre utilisateur connecté
            }

            $queryBuilder->setParameter("user", $user);

        }
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null)
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }


}