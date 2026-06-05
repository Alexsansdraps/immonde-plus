<?php

namespace App\Repository;

use App\Entity\FilmSeries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FilmSeries>
 */
class FilmSeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FilmSeries::class);
    }

    /**
     * @return FilmSeries[]
     */
    public function findAllWithPublishedFilms(?string $search = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.films', 'f')
            ->addSelect('f')
            ->where('f.isPublished = :published OR f.id IS NULL')
            ->setParameter('published', true);

        if ($search) {
            $qb->andWhere('s.title LIKE :search OR f.title LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
