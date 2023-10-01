<?php

declare(strict_types=1);

namespace App\Model\Facade;

use App\Exception\NewTaskException;
use App\Exception\NoResultException;
use App\Model\Constant\TaskStatus;
use App\Model\Data\HomeTaskData;
use App\Model\Manager\TaskAssignedManager;
use App\Model\Manager\TaskCatalogueManager;
use App\Util\FileUploadUtil;
use Exception;
use Nette\Database\Table\ActiveRow;
use Nette\InvalidStateException;
use Nette\Security\User;
use Nette\Utils\DateTime;

/**
 * TaskFacade.
 *
 * @package   App\Model\Factory
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final readonly class TaskFacade
{

    /**
     * Constructor.
     *
     * @param TaskAssignedManager  $taskAssignedManager
     * @param TaskCatalogueManager $taskCatalogueManager
     * @param User                 $user
     */
    public function __construct(
        private TaskAssignedManager $taskAssignedManager,
        private TaskCatalogueManager $taskCatalogueManager,
        private User $user
    ) {
    }

    /**
     * Prepare data for task evaluation.
     *
     * @param int $assignedTaskId
     * @return ActiveRow
     * @throws NoResultException
     * @throws InvalidStateException
     */
    public function prepareEvaluateData(int $assignedTaskId): ActiveRow
    {
        $data = $this->taskAssignedManager->findById($assignedTaskId);

        if (!$data) {
            throw new NoResultException('Nepodarilo se najit ukol k vyhodnoceni.');
        }
        if ($data->status_id !== TaskStatus::CONFIRMED) {
            throw new InvalidStateException('Ukol neni mozne vyhodnotit.');
        }

        return $data;
    }

    /**
     * Return all paths to images.
     *
     * @param int $assignedTaskId
     * @return array
     */
    public function prepareTaskImages(int $assignedTaskId): array
    {
        $imagesDir = FileUploadUtil::getAssignedTaskImgDir($assignedTaskId, true);

        if (file_exists($imagesDir) === false) {
            return [];
        }

        $images = scandir($imagesDir);
        unset($images[0], $images[1]);

        foreach ($images as $key => $image) {
            $images[$key] = FileUploadUtil::getAssignedTaskImgDir($assignedTaskId) . $image;
        }

        return $images;
    }

    /**
     * Return video path.
     *
     * @param int $assignedTaskId
     * @return string
     */
    public function prepareTaskVideo(int $assignedTaskId): string
    {
        try {
            $videoDir = FileUploadUtil::getAssignedTaskVideoDir($assignedTaskId, true);
            if (is_dir($videoDir) === false) {
                return '';
            }
            $directory = scandir($videoDir);
            $video = $directory[2];

            return FileUploadUtil::getAssignedTaskVideoDir($assignedTaskId) . $video;
        } catch (Exception) {
            return '';
        }
    }

    /**
     * Data for template.
     *
     * @return HomeTaskData
     */
    public function prepareHomeDefaultData(): HomeTaskData
    {
        $assignedActiveTask = $this->taskAssignedManager->findActiveTask($this->user->getId());

        $data = new HomeTaskData();
        $now = (new DateTime())->format('Y-m-d');


        if ($assignedActiveTask && $assignedActiveTask->task->due_date < $now) {
            $data->expiredOldTask = true;
            $this->taskAssignedManager->setExpired($assignedActiveTask->id);
            $assignedActiveTask = null;
        }


        $data->assignedActiveTask = $assignedActiveTask;
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

    public function finishTask(int $assignedTaskId, ?string $text): void
    {
        // Todo: validate
        $this->taskAssignedManager
            ->find()
            ->get($assignedTaskId)
            ->update([
                'status_id' => TaskStatus::CONFIRMED,
                'text_answer' => $text,
                'finish_date' => new DateTime()
            ]);
    }

    public function rejectTask(int $assignedTaskId): void
    {
        $this->taskAssignedManager
            ->find()
            ->get($assignedTaskId)
            ->update([
                'status_id' => TaskStatus::REJECTED,
                'finish_date' => new DateTime()
            ]);
    }

    public function acceptTask(int $assignedTaskId): void
    {
        $this->taskAssignedManager
            ->find()
            ->get($assignedTaskId)
            ->update([
                'status_id' => TaskStatus::DONE,
                'finish_date' => new DateTime()
            ]);
    }
}
