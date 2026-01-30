<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Team;
use App\Enum\ProjectStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function countActiveProjects(?Team $team = null): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :status')
            ->setParameter('status', ProjectStatus::ACTIVE);

        if ($team) {
            $qb->andWhere('p.team = :team')->setParameter('team', $team);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
