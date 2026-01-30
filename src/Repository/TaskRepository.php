<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return list<Task>
     */
    public function findForProjectWithAssignee(Project $project): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.project = :project')
            ->setParameter('project', $project)
            ->leftJoin('t.assignee', 'a')->addSelect('a')
            ->addOrderBy('t.status', 'ASC')
            ->addOrderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, int>
     */
    public function countTasksByStatus(?Team $team = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.status AS status, COUNT(t.id) AS cnt')
            ->join('t.project', 'p');

        if ($team) {
            $qb->andWhere('p.team = :team')->setParameter('team', $team);
        }

        $rows = $qb->groupBy('t.status')->getQuery()->getArrayResult();

        $out = [];
        foreach ($rows as $row) {
            /** @var \App\Enum\TaskStatus $status */
            $status = $row['status'];

            $out[$status->value] = (int) $row['cnt'];
        }


        return $out;
    }
}
