<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class AttachmentDragDrop extends Component
{
    private $inputName;
    private $initialPhoto;
    private $multiple;
    private $acceptedFilesTypes;

    public function __construct($inputName, $initialPhoto = null, bool $multiple = false, string $acceptedFilesTypes = 'image/png, image/jpeg, image/jpg')
    {
        $this->inputName = $inputName;
        $this->initialPhoto = $initialPhoto;
        $this->multiple = $multiple;
        $this->acceptedFilesTypes = $acceptedFilesTypes;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/attachment_drag_drop.css');
    }

    public function render()
    {
        $id = uniqid();
        $inputName = $this->inputName . ($this->multiple ? '[]' : '');
        $initialPhoto = $this->initialPhoto ? '<img src="' . $this->initialPhoto . '"/>' : null;
        $multiple = $this->multiple ? 'multiple' : null;

        echo <<<HTML
        <div class="attachment-dropdown">
            <input type="file" class="main-input" name="$inputName" id="$id" accept="{$this->acceptedFilesTypes}" {$multiple}>
            <label for="$id">
                <div>
                    <i class="material-icons">attach_file</i>
                    <span>Przeciągnij lub dodaj zdjęcie</span>
                </div>
                <span class="animated-hidden-span error-output"></span>
            </label>
            <div class="attachment-preview">
                $initialPhoto
            </div>
        </div>
    HTML;
    }
}
