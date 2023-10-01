<?php

declare(strict_types=1);

namespace App\Component\Grid\TaskCatalogue;

use App\Model\Constant\Resource;
use App\Model\Manager\TaskCatalogueManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Localization\Translator;
use Nette\Security\User;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;
use Rdurica\Core\Constant\Privileges;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

/**
 * TaskCatalogueGrid.
 *
 * @package   App\Component\Grid\TaskCatalogue
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class TaskCatalogueGrid extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param TaskCatalogueManager $taskCatalogueManager
     * @param User                 $user
     * @param Translator           $translator
     */
    public function __construct(
        private readonly TaskCatalogueManager $taskCatalogueManager,
        private readonly User $user,
        private readonly Translator $translator
    ) {
    }

    public function test()
    {
    }

    /**
     * Task catalogue grid.
     *
     * @throws DataGridException
     */
    public function createComponentGrid(): DataGrid
    {
        $grid = new DataGrid($this, 'grid');
        $booleanHtml = [
            false => '<h6><span class="badge badge-danger">Ne</span></h6>',
            true => '<h6><span class="badge badge-success">Ano</span></h6>'
        ];

        $labelSummary = $this->translator->translate('labels.task');
        $labelStartDate = $this->translator->translate('labels.startDate');
        $labelEndDate = $this->translator->translate('labels.endDate');
        $labelRequirePhotos = $this->translator->translate('labels.requirePhotos');
        $labelRequireVideo = $this->translator->translate('labels.requireVideo');
        $labelRequireText = $this->translator->translate('labels.requireText');
        $labelIsEnabled = $this->translator->translate('labels.isEnabled');
        $labelButtonEdit = $this->translator->translate('labels.edit');
        $labelButtonDelete = $this->translator->translate('labels.delete');


        $grid->setDataSource($this->taskCatalogueManager->find()->order('id DESC'));
        $grid->addColumnText('summary', $labelSummary)
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDateTime('start_date', $labelStartDate)
            ->setSortable();
        $grid->addColumnDateTime('due_date', $labelEndDate)
            ->setSortable();
        $grid->addColumnText('require_photos', $labelRequirePhotos)
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_photos"]];
            });
        $grid->addColumnText('require_video', $labelRequireVideo)
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_video"]];
            });
        $grid->addColumnText('require_text', $labelRequireText)
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["require_text"]];
            });
        $grid->addColumnText('is_enabled', $labelIsEnabled)
            ->setSortable()
            ->setTemplateEscaping(false)
            ->setRenderer(function ($row) use ($booleanHtml) {
                return $booleanHtml[$row["is_enabled"]];
            });

        if ($this->user->isAllowed(Resource::TASK_CATALOGUE, Privileges::EDIT)) {
            $grid->addAction('edit', $labelButtonEdit, 'Task:edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed(Resource::TASK_CATALOGUE, Privileges::DELETE)) {
            $grid->addAction('delete', $labelButtonDelete, 'delete!')
                ->setClass('btn btn-sm btn-danger');
        }

        return $grid;
    }

    public function handleEdit(int $id): void
    {
    }

    /**
     * @param int $id
     * @return void
     * @throws AbortException
     */
    public function handleDelete(int $id): void
    {
        try {
            $this->taskCatalogueManager->findById($id)->delete();
            $message = $this->translator->translate('messages.success.taskDeleted');
            $this->getPresenter()->flashMessage($message, FlashType::SUCCESS);
        } catch (Exception) {
            $message = $this->translator->translate('messages.error.generalError');
            $this->getPresenter()->flashMessage($message, FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('this');
    }

}