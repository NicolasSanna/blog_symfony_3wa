<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * @return Article
     */
   public function findByAuthor(User $user, Article $article): Article|null
   {
       return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->andWhere('a.author = :user')
            ->setParameter('user', $user)
            ->setParameter('id', $article->getId())
            ->getQuery()
            ->getOneOrNullResult()
       ;
   }

   /**
    * @return Article[] Returns an array of Article objects
    */
   public function findByAuthorOrderByTitle(User $user): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.author = :user')
           ->setParameter('user', $user)
           ->orderBy('a.title', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

    /**
    * Moteur de recherche
    */
    public function searchArticles(string $searchTerm): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(

            '   SELECT a
                FROM App\Entity\Article a
                WHERE a.title LIKE :searchTerm
                OR a.content LIKE :searchTerm'
        )
        ->setParameter('searchTerm', '%'.$searchTerm.'%');

        return $query->getResult();
    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
