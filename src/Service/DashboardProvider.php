<?php

namespace App\Service;

use App\Entity\Team;
use App\Enum\TaskStatus;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;

final readonly class DashboardProvider
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private TaskRepository $taskRepository,
    ) {
    }

    /**
     * @return array{
     *   activeProjectsCount:int,
     *   tasksByStatus:list<array{status: TaskStatus, label: string, count: int}>,
     *   totalTasks:int,
     *   doneTasks:int,
     *   progressPercent:int
     * }
     */
    public function getStats(?Team $team): array
    {
        $activeProjectsCount = $this->projectRepository->countActiveProjects($team);

        $byStatus = $this->taskRepository->countTasksByStatus($team);

        $tasksByStatus = [];
        foreach (TaskStatus::cases() as $case) {
            $tasksByStatus[] = [
                'status' => $case,
                'label' => $case->getLabel(),
                'count' => $byStatus[$case->value] ?? 0,
            ];
        }

        $totalTasks = array_sum(array_column($tasksByStatus, 'count'));
        $doneTasks = $byStatus[TaskStatus::DONE->value] ?? 0;

        $progressPercent = $totalTasks > 0
            ? (int) round(($doneTasks * 100) / $totalTasks)
            : 0;

        return [
            'activeProjectsCount' => $activeProjectsCount,
            'tasksByStatus' => $tasksByStatus,
            'totalTasks' => $totalTasks,
            'doneTasks' => $doneTasks,
            'progressPercent' => $progressPercent,
        ];
    }
}
