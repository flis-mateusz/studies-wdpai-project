<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class AttachmentDragDrop extends Component
{
    private $inputName;
    private $initialPhoto;

    public function __construct($inputName, $initialPhoto = null)
    {
        $this->inputName = $inputName;
        $this->initialPhoto = $initialPhoto;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/attachment_drag_drop.css');
    }

    public function render()
    {
        $id = uniqid();
        $initialPhoto = $this->initialPhoto ? '<img src="' . $this->initialPhoto . '"/>' : null;

        echo <<<HTML
        <div class="attachment-dropdown">
            <input type="file" class="main-input" name="$this->inputName" id="$id" accept="image/*">
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
