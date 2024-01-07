<?php

require_once __DIR__ . '/Component.php';

class FilterSelect extends Component
{
    private $values;
    private $name;
    private $id;

    public function __construct(array $values, string|null $name, string|null $id)
    {
        $this->values = $values;
        $this->name = $name;
        $this->id = $id;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/filter-select.css');
    }

    public function render()
    {
        $nameElement = $this->name ? '<div>' . $this->name . '</div>' : '';
        $content = '';

        foreach ($this->values as $value) {
            if (!$value) {
                continue;
            }
            $id = is_array($value) && isset($value['id']) ? $value['id'] : $value->getId();
            $name = is_array($value) && isset($value['name']) ? $value['name'] : $value->getName();

            $content .= <<<HTML
                <div class="label" data-id="$id">
                    $name
                </div>
            HTML;
        }

        echo <<<HTML
        <div class="filter-container">
            $nameElement
            <div class="options" id="{$this->id}">
                $content
            </div>
        </div>
        HTML;
    }
}
