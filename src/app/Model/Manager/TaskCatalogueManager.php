<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Data\TaskCatalogueData;
use Nette\Utils\ArrayHash;
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
}