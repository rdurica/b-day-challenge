<?php

declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Data\RulesData;
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
     * @return RulesData
     */
    public function findMessage(): RulesData
    {
        $row = $this->find()->fetch();

        $rulesFormData = new RulesData();
        $rulesFormData->message = $row?->message;

        return $rulesFormData;
    }

    /**
     * Update rules.
     *
     * @param RulesData $rulesData
     * @return void
     */
    public function update(RulesData $rulesData): void
    {
        $this->find()->get(1)->update($rulesData);
    }
}
