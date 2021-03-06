<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findByText($text): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT a.description, u.nickname, u.image, a.date FROM article a
            LEFT JOIN user u
            ON u.id = a.user_id
            WHERE a.description LIKE :text
            ORDER BY a.date DESC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['text' => "%#" . $text . "%"]);

        return $stmt->fetchAll();
    }

    public function findByFollower($id): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT a.description, u.nickname, u.image, a.date from article a
            LEFT JOIN user u
            ON u.id = a.user_id
            INNER JOIN follower f
            ON f.user_id = a.user_id
            WHERE f.follower_id = :id
            OR a.user_id = :id
            ORDER BY a.date DESC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => "$id"]);

        return $stmt->fetchAll();
    }

    public function findByPublic(): array {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT a.description, u.nickname, u.image, a.date from article a
            LEFT JOIN user u
            ON u.id = a.user_id
            WHERE a.public = 1
            AND u.public = 1
            ORDER BY a.date DESC
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
