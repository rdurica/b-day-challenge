<?php

declare(strict_types=1);

namespace App\Model\Manager;

use Nette\Database\Table\ActiveRow;
use Rdurica\Core\Model\Manager\Manager;

/**
 * RulesManager.
 *
 * @package   App\Model\Manager
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class RulesManager extends Manager
{
    /** @var string Table name. */
    private const TABLE = 'rules';

    /** @inheritDoc */
    protected function getTableName(): string
    {
        return self::TABLE;
    }

    /**
     * Get rules.
     *
     * @return ActiveRow|null
     */
    public function findRules(): ?ActiveRow
    {
        return $this->find()->fetch();
    }
}
