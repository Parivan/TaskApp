<?php

namespace App\Service;

use App\Entity\Task;
use App\Enum\TaskStatus;

final class TaskGrouper
{
    /**
     * @param Task[] $tasks
     * @return array<string, Task[]>
     */
    public function groupByStatus(array $tasks): array
    {
        $grouped = [];
        foreach (TaskStatus::cases() as $case) {
            $grouped[$case->getLabel()] = [];
        }

        foreach ($tasks as $task) {
            $grouped[$task->getStatus()->getLabel()][] = $task;
        }

        return $grouped;
    }
}
