<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Constant\TaskStatus;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
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
     * Find task history for user (confirmed+).
     *
     * @param int $userId
     * @return ActiveRow[]
     */
    public function findTaskHistory(int $userId): array
    {
        return $this
            ->find()
            ->where('user_id = ?', $userId)
            ->where('status_id > ?', TaskStatus::ACTIVE)
            ->order('id DESC')
            ->fetchAll();
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

    /**
     * Set assigned task as expired.
     *
     * @param int $id
     * @return void
     */
    public function setExpired(int $id): void
    {
        $this->findById($id)->update([
            'status_id' => TaskStatus::EXPIRED,
        ]);
    }

    /**
     * Find all tasks which requires evaluation.
     *
     * @return Selection
     */
    public function findNotEvaluatedTasks(): Selection
    {
        return $this->find()
            ->where('status_id = ?', TaskStatus::CONFIRMED);
    }
}