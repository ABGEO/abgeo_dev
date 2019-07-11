<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Blog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blog[]    findAll()
 * @method Blog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogRepository extends ServiceEntityRepository
{

    // Directions for getPrevOrNext method.
    const PREVIOUS = 1;
    const NEXT = 2;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * Get query builder.
     *
     * @param array $options
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder(array $options = []): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('b');

        if (array_key_exists('search', $options)) {
            // Search words in title and body.
            $queryBuilder->where('b.title like :search')
                ->orWhere('b.body like :search')
                ->setParameter('search', "%{$options['search']}%");
        }

        return $queryBuilder->orderBy('b.createdOn', 'DESC');
    }

    /**
     * Get previous or next blog for given blogId;
     *
     * @param int $blogId
     * @param int $direction
     *
     * @return mixed|null
     */
    public function getPrevOrNext(int $blogId, int $direction)
    {
        $directions = [
            self::PREVIOUS => [
                'dir' => '<',
                'order' => 'DESC'
            ],
            self::NEXT => [
                'dir' => '>',
                'order' => 'ASC'
            ],
        ];

        $result = null;

        try {
            $result = $this->createQueryBuilder('b')
                ->select('b.id, b.title')
                ->where('b.id ' . $directions[$direction]['dir'] . ' :id')
                ->setFirstResult(0)
                ->setMaxResults(1)
                ->setParameter('id', $blogId)
                ->orderBy('b.id', $directions[$direction]['order'])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
        }

        return $result;
    }

    // /**
    //  * @return Blog[] Returns an array of Blog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
