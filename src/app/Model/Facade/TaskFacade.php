<?php

declare(strict_types=1);

namespace App\Model\Facade;

use App\Exception\NewTaskException;
use App\Model\Constant\TaskStatus;
use App\Model\Data\HomeTaskData;
use App\Model\Manager\TaskAssignedManager;
use App\Model\Manager\TaskCatalogueManager;
use Nette\Security\User;
use Nette\Utils\DateTime;

/**
 * TaskFacade.
 *
 * @package   App\Model\Factory
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskFacade
{

    /**
     * Constructor.
     *
     * @param TaskAssignedManager  $taskAssignedManager
     * @param TaskCatalogueManager $taskCatalogueManager
     * @param User                 $user
     */
    public function __construct(
        private readonly TaskAssignedManager $taskAssignedManager,
        private readonly TaskCatalogueManager $taskCatalogueManager,
        private readonly User $user
    ) {
    }

    /**
     * Data for template.
     *
     * @return HomeTaskData
     */
    public function prepareHomeDefaultData(): HomeTaskData
    {
        $data = new HomeTaskData();
        $data->assignedActiveTask = $this->taskAssignedManager->findActiveTask($this->user->getId());
        if (!$data->assignedActiveTask) {
            $excludedTasks = $this->taskAssignedManager->findIdsOfTasksForExclude($this->user->getId());
            $newTask = $this->taskCatalogueManager->findNewTask($excludedTasks);
            $data->newTask = (bool)$newTask;
            $data->assignedTaskHistory = $this->taskAssignedManager->findTaskHistory($this->user->getId());
        }

        return $data;
    }

    /**
     * Assign task for user.
     *
     * @throws NewTaskException
     */
    public function assignTask(): void
    {
        $excludedTasks = $this->taskAssignedManager->findIdsOfTasksForExclude($this->user->getId());
        $newTask = $this->taskCatalogueManager->findNewTask($excludedTasks);

        if (!$newTask) {
            throw new NewTaskException();
        }

        $this->taskAssignedManager->assignTaskToUser($newTask->id, $this->user->getId());
    }

    public function finishTask(int $taskId): void
    {
        // Todo: validate
        $this->taskAssignedManager
            ->find()
            ->get($taskId)
            ->update([
                'status_id' => TaskStatus::CONFIRMED,
                'finish_date' => new DateTime()
            ]);
    }
}
