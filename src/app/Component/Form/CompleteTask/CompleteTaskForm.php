<?php

declare(strict_types=1);

namespace App\Component\Form\CompleteTask;

use App\Model\Facade\TaskFacade;
use App\Model\Manager\TaskAssignedManager;
use App\Presenter\HomePresenter;
use App\Util\FileUploadUtil;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Rdurica\Core\Component\Component;
use Rdurica\Core\Component\ComponentRenderer;
use Rdurica\Core\Constant\FlashType;

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
     * @param TaskFacade          $taskFacade
     * @param TaskAssignedManager $taskAssignedManager
     */
    public function __construct(
        private readonly TaskFacade $taskFacade,
        private readonly TaskAssignedManager $taskAssignedManager
    ) {
    }

    /**
     * Finish task form.
     *
     * @return Form
     */
    public function createComponentForm(): Form
    {
        $assignedTaskId = $this->getParameterAssignedTaskId();
        $catalogueTask = $this->taskAssignedManager->findById($assignedTaskId)->task;

        $form = new Form();
        if ($catalogueTask->require_photos) {
            $form->addMultiUpload('pictures')
                ->addRule($form::Image, 'Obrazek musí být JPEG, PNG, GIF, WebP or AVIF.')
                ->setRequired();
        }
        if ($catalogueTask->require_video) {
            $form->addUpload('video')
                ->addRule($form::MimeType, 'Nahrej video.', 'video/*')
                ->setRequired();
        }
        if ($catalogueTask->require_text) {
            $form->addTextArea('text')
                ->setRequired();
        }
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
        try {
            $assignedTaskId = $this->getParameterAssignedTaskId();
            $this->processForm($assignedTaskId, $data);
            $this->getPresenter()->flashMessage('Ukol dokoncen', FlashType::SUCCESS);
        } catch (Exception $e) {
            $this->getPresenter()->flashMessage('Oops. Něco se pokazilo', FlashType::ERROR);
            $this->getPresenter()->redirect('this');
        }

        $this->getPresenter()->redirect('Home:');
    }


    /**
     * @param int       $assignedTaskId
     * @param ArrayHash $data
     * @return void
     */
    public function processForm(int $assignedTaskId, ArrayHash $data): void
    {
        if (isset($data->pictures)) {
            foreach ($data->pictures as $key => $picture) {
                $this->saveImageFile($key, $picture, $assignedTaskId);
            }
        }
        if (isset($data->video)) {
            $this->saveVideoFile($data->video, $assignedTaskId);
        }

        $text = $data->text ?? null;

        $this->taskFacade->finishTask($assignedTaskId, $text);
    }

    /**
     * Save image file to disk.
     *
     * @param int        $id
     * @param FileUpload $file
     * @param int        $assignedTaskId
     * @return void
     */
    public function saveImageFile(int $id, FileUpload $file, int $assignedTaskId): void
    {
        if ($file->isImage() and $file->isOk()) {
            $fileExt = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
            $fileName = sprintf('%s%s%s', 'img-', $id, $fileExt);
            $filePath = sprintf('%s%s', FileUploadUtil::getAssignedTaskImgDir($assignedTaskId, true), $fileName);

            $file->move($filePath);
        }
    }

    /**
     * Save video file to disk.
     *
     * @param FileUpload $file
     * @param int        $assignedTaskId
     * @return void
     */
    public function saveVideoFile(FileUpload $file, int $assignedTaskId): void
    {
        if ($file->isOk()) {
            $fileExt = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
            $fileName = sprintf('%s%s', 'video', $fileExt);
            $filePath = sprintf('%s%s', FileUploadUtil::getAssignedTaskVideoDir($assignedTaskId, true), $fileName);

            $file->move($filePath);
        }
    }

    private function getParameterAssignedTaskId(): int
    {
        /** @var HomePresenter $presenter */
        $presenter = $this->getPresenter();
        return $presenter->getIntParameter('taskAssignedId');
    }

    public function render()
    {
        $assignedTaskId = $this->getParameterAssignedTaskId();
        $catalogueTask = $this->taskAssignedManager->findById($assignedTaskId)->task;

        $this->getTemplate()->setFile(__DIR__ . '/default.latte');
        $this->getTemplate()->task = $catalogueTask;
        $this->getTemplate()->render();
    }


}