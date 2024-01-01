<?php

require_once __DIR__ . '/Component.php';

class AnimalFeatures
{
    private $features;

    public function __construct(array $features)
    {
        $this->features = $features;
    }

    public function render()
    {
        $content = '';

        foreach ($this->features as $feature) {
            $id = $feature->getId();
            $name = $feature->getName();

            $content .= <<<HTML
            <div>
                <div>$name</div>
                <div class="checkboxes">
                    <input type="radio" id="charac-$id-yes" name="characteristics[$id]" value="2" />
                    <label class="yes" for="charac-$id-yes">Tak</label>
                    <input type="radio" id="charac-$id-no" name="characteristics[$id]" value="1" />
                    <label class="no" for="charac-$id-no">Nie</label>
                    <input type="radio" id="charac-$id-not-sure" name="characteristics[$id]" value="0" />
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
