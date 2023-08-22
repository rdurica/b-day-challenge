<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Constant\TaskStatus;
use Nette\Database\Table\ActiveRow;
use Rdurica\Core\Model\Manager\Manager;

/**
 * TaskAssignedManager.
 *
 * @package   App\Model\Manager
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskAssignedManager extends Manager
{
    /** @var string Table name. */
    private const TABLE = 'task_assigned';

    /** @inheritDoc */
    protected function getTableName(): string
    {
        return self::TABLE;
    }


    /**
     * Find confirmed task for user (started but not finished).
     *
     * @param int $userId
     * @return ActiveRow|null
     */
    public function findActiveTask(int $userId): ?ActiveRow
    {
        return $this
            ->find()
            ->where('user_id = ?', $userId)
            ->where('status_id = ?', TaskStatus::ACTIVE)
            ->fetch();
    }

    /**
     * Find all task for user which was assigned in the past.
     *
     * @param int $userId
     * @return array<int, int>
     */
    public function findIdsOfTasksForExclude(int $userId): array
    {
        return $this
            ->find()
            ->where('user_id = ?', $userId)
            ->fetchPairs('id', 'task_id');
    }

    /**
     * Assign task to user.
     *
     * @param int $taskId
     * @param int $userId
     * @return void
     */
    public function assignTaskToUser(int $taskId, int $userId): void
    {
        $this->insert([
            'task_id' => $taskId,
            'user_id' => $userId,
            'status_id' => TaskStatus::ACTIVE,
        ]);
    }
}