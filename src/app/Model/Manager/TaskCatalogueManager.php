<?php

declare(strict_types=1);

namespace App\Model\Manager;

use Nette\Database\Table\ActiveRow;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Rdurica\Core\Model\Manager\Manager;

final class TaskCatalogueManager extends Manager
{
    /** @var string Table name. */
    private const TABLE = 'task_catalogue';

    /** @inheritDoc */
    protected function getTableName(): string
    {
        return self::TABLE;
    }

    /**
     * Update rules.
     *
     * @param int       $id
     * @param ArrayHash $data
     * @return void
     */
    public function updateById(int $id, ArrayHash $data): void
    {
        $this->find()->get($id)->update($data);
    }


    /**
     * Find available task. If taskId provided, task is validated and can be started.
     *
     * @param int[]    $excludedTasks
     * @param int|null $taskId
     * @return ActiveRow|null
     */
    public function findNewTask(array $excludedTasks = [], ?int $taskId = null): ?ActiveRow
    {
        $date = (new DateTime())->format('Y-m-d');
        $data = $this
            ->find()
            ->where('start_date >= ?', $date)
            ->where('due_date >= ?', $date)
            ->where('is_enabled = ?', 1);
        if (count($excludedTasks) > 0) {
            $data->where('id NOT IN (?)', $excludedTasks);
        }

        if ($taskId) {
            $data->where('id = ?', $taskId);
        }

        return $data->fetch();
    }
}