<?php

declare(strict_types=1);

namespace App\Component\Form\CompleteTask;

use App\Model\Factory\TaskFactory;
use App\Presenter\HomePresenter;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;

/**
 * CompleteTaskForm.
 *
 * @package   App\Component\Form\CompleteTask
 * @author    Robert Durica <r.durica@gmail.com>
 * @copyright Copyright (c) 2023, Robert Durica
 */
final class CompleteTaskForm extends Component
{
    use ComponentRenderer;

    /**
     * Constructor.
     *
     * @param TaskFactory $taskFactory
     */
    public function __construct(private readonly TaskFactory $taskFactory)
    {
    }

    /**
     * Finish task form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $form = new Form();
        $form->addMultiUpload('files', 'FileUpload')
            ->addRule($form::Image, 'Obrazek musí být JPEG, PNG, GIF, WebP or AVIF.');
        $form->addSubmit('finish', 'Dokoncit');
        $form->onSuccess[] = [$this, 'formOnSuccess'];

        return $form;
    }

    /**
     * Process form.
     *
     * @param Form      $form
     * @param ArrayHash $data
     * @return void
     * @throws AbortException
     */
    public function formOnSuccess(Form $form, ArrayHash $data): void
    {
        /** @var HomePresenter $presenter */
        $presenter = $this->getPresenter();
        $taskId = $presenter->getIntParameter('taskAssignedId');

        /** @var FileUpload[] $files */
        $files = $data->files;
        foreach ($files as $key => $file) {
            $this->saveFile($key, $file, $taskId);
        }


        $this->taskFactory->finishTask($taskId);
        $this->getPresenter()->redirect('Home:');
    }

    /**
     * Save file to disk.
     *
     * @param int        $id
     * @param FileUpload $file
     * @param int        $taskId
     * @return void
     */
    public function saveFile(int $id, FileUpload $file, int $taskId): void
    {
        if ($file->isImage() and $file->isOk()) {
            $file_ext = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));

            $filePath = sprintf(
                '%s%s%s%s%s%s%s',
                '/www-data/b_day_challenge/',
                'assignment_',
                $taskId,
                '/img-',
                $id,
                '.',
                $file_ext
            );
            $file->move($filePath);
        }
    }

}