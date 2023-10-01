<?php

declare(strict_types=1);

namespace App\Component\Grid\TaskEvaluation;

use App\Model\Manager\TaskAssignedManager;
use App\Presenter\TaskPresenter;
use Nette\Localization\Translator;
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
     * @param Translator          $translator
     */
    public function __construct(
        private readonly TaskAssignedManager $taskAssignedManager,
        private readonly User $user,
        private readonly Translator $translator
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

        $labelSummary = $this->translator->translate('labels.task');
        $labelFinishDate = $this->translator->translate('labels.finishDate');
        $labelEvaluate = $this->translator->translate('labels.evaluate');

        $grid = new DataGrid($this, 'grid');
        $grid->setDataSource($dataSource);
        $grid->addColumnText('task', $labelSummary, 'task_catalogue.summary:task_id')
            ->setSortable();
        $grid->addColumnDateTime('finish_date', $labelFinishDate)
            ->setSortable();
        $grid->addAction('edit', $labelEvaluate, $editLink)
            ->setClass('btn btn-sm btn-success');

        return $grid;
    }
}