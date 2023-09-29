<?php

declare(strict_types=1);

namespace App\Component\Grid\TaskEvaluation;

use App\Model\Manager\TaskAssignedManager;
use App\Presenter\TaskPresenter;
use Nette\Security\User;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Util\LinkBuilderUtil;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\NetteDatabaseTableDataSource;
use Ublaboo\DataGrid\Exception\DataGridException;


/**
 * TaskCatalogueGrid.
 *
 * @package   App\Component\Grid\TaskCatalogue
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskEvaluationGrid extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param TaskAssignedManager $taskAssignedManager
     * @param User                $user
     */
    public function __construct(
        private readonly TaskAssignedManager $taskAssignedManager,
        private readonly User $user
    ) {
    }

    /**
     * Task catalogue grid.
     *
     * @return DataGrid
     * @throws DataGridException
     */
    public function createComponentGrid(): DataGrid
    {
        $editLink = LinkBuilderUtil::simpleLink(TaskPresenter::PRESENTER_NAME, TaskPresenter::ACTION_EVALUATE);

        $data = $this->taskAssignedManager->findNotEvaluatedTasks();
        $dataSource = new NetteDatabaseTableDataSource($data, 'id');

        $grid = new DataGrid($this, 'grid');
        $grid->setDataSource($dataSource);
        $grid->addColumnText('username', 'Uzivatel', 'core_acl_user.username:user_id')
            ->setSortable();
        $grid->addColumnText('task', 'Ukol', 'task_catalogue.summary:task_id')
            ->setSortable();
        $grid->addColumnDateTime('finish_date', 'Dokonceno')
            ->setSortable();
        $grid->addAction('edit', 'Vyhodnotit', $editLink)
            ->setClass('btn btn-sm btn-success');

        return $grid;
    }
}