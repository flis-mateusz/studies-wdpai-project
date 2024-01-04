<?php

require_once __DIR__ . '/Component.php';

class AnimalFeatures
{
    private $features;
    private $initialValues;

    public function __construct(array $features, array $initialValues = null)
    {
        $this->features = $features;

        $this->initialValues = [];
        if ($initialValues) {
            $this->initialValues = AnnouncementDetail::featuresToAssociativeArray($initialValues);
        }
    }

    public function render()
    {
        $content = '';

        foreach ($this->features as $feature) {
            $id = $feature->getId();
            $name = $feature->getName();

            $yesChecked = array_key_exists($id, $this->initialValues) && $this->initialValues[$id]['value']  ? 'checked' : '';
            $noNhecked = array_key_exists($id, $this->initialValues) && !$this->initialValues[$id]['value']  ? 'checked' : '';

            $content .= <<<HTML
            <div>
                <div>$name</div>
                <div class="checkboxes">
                    <input type="radio" id="charac-$id-yes" name="pet-characteristics[$id]" value="2"  $yesChecked/>
                    <label class="yes" for="charac-$id-yes">Tak</label>
                    <input type="radio" id="charac-$id-no" name="pet-characteristics[$id]" value="1" $noNhecked/>
                    <label class="no" for="charac-$id-no">Nie</label>
                    <input type="radio" id="charac-$id-not-sure" name="pet-characteristics[$id]" value="0" />
                    <label class="not-sure" for="charac-$id-not-sure">Nie wiem</label>
                </div>
            </div>
            HTML;
        }

        echo <<<HTML
            <div class="animal-features">
                $content
            </div>
        HTML;
    }
}
