<?php

require_once __DIR__ . '/Component.php';

class SelectAnimalFeatures extends Component
{
    private $features;
    private $initialValues;
    private $deselectText;
    private $minimal;

    public function __construct(array $features, array $initialValues = null, string $deselectText = 'Nie wiem', bool $minimal = false)
    {
        $this->features = $features;
        $this->deselectText = $deselectText;
        $this->minimal = $minimal;

        $this->initialValues = [];
        if ($initialValues) {
            $this->initialValues = AnnouncementDetail::featuresToAssociativeArray($initialValues);
        }
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/animal-features-select.css');
    }

    public function render()
    {
        $content = '';
        $addClass = $this->minimal ? 'min' : '';

        foreach ($this->features as $feature) {
            $id = $feature->getId();
            $name = $feature->getName();

            $yesChecked = array_key_exists($id, $this->initialValues) && $this->initialValues[$id]['value']  ? 'checked' : '';
            $noNhecked = array_key_exists($id, $this->initialValues) && !$this->initialValues[$id]['value']  ? 'checked' : '';

            $content .= <<<HTML
            <div data-id="$id">
                <div data-id="$id">$name</div>
                <div class="checkboxes">
                    <input type="radio" id="charac-$id-yes" name="pet-characteristics[$id]" value="2"  $yesChecked/>
                    <label class="yes" for="charac-$id-yes">Tak</label>
                    <input type="radio" id="charac-$id-no" name="pet-characteristics[$id]" value="1" $noNhecked/>
                    <label class="no" for="charac-$id-no">Nie</label>
                    <input type="radio" id="charac-$id-not-sure" name="pet-characteristics[$id]" value="0" />
                    <label class="not-sure" for="charac-$id-not-sure">{$this->deselectText}</label>
                </div>
            </div>
            HTML;
        }

        echo <<<HTML
            <div class="animal-features {$addClass}">
                $content
            </div>
        HTML;
    }
}
