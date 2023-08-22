<?php

declare(strict_types=1);

namespace App\Model\Factory;

use App\Model\Constant\TaskStatus;
use App\Model\Data\HomeTaskData;
use App\Model\Manager\TaskAssignedManager;
use App\Model\Manager\TaskCatalogueManager;
use Nette\Security\User;

/**
 * TaskFactory.
 *
 * @package   App\Model\Factory
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskFactory
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
            $data->newTask = $this->taskCatalogueManager->findNewTask($excludedTasks);
        }

        return $data;
    }

    public function assignTask(int $taskId): void
    {
        $task = $this->taskCatalogueManager->findNewTask([], $taskId);

        // Todo: validate


        $this->taskAssignedManager->assignTaskToUser($taskId, $this->user->getId());
    }

    public function finishTask(int $taskId): void
    {
        // Todo: validate
        $this->taskAssignedManager->find()->get($taskId)->update(['status_id' => TaskStatus::CONFIRMED]);
    }
}
