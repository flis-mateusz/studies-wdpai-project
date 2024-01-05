<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class DebounceSelectSearchComponent extends Component
{
    private $id;
    private $inputName;
    private $endpointUrl;
    private $timeout;
    private $preLoadedData;
    private $initialValue;

    public function __construct($inputName, string $endpointUrl = null, int $timeout = null, $preLoadedData = null, $initialValue = null)
    {
        $this->id = 'deb-sea-' . uniqid();
        $this->inputName = $inputName;
        $this->endpointUrl = $endpointUrl ? "'$endpointUrl'" : 'null';
        $this->timeout = $timeout ? $timeout : 'null';
        $this->preLoadedData = $preLoadedData;
        $this->initialValue = $initialValue;
    }

    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/debounce-search.css');
        ResourceManager::addScript('/public/js/controllers/debounce-search-controller.js', true, true);
    }

    public function render()
    {
        $inputName = $this->inputName;
        $id = $this->id;
        $initialValue = $this->initialValue;

        echo <<<HTML
        <section class="debonced-search" id="$id">
            <input type="text" class="hidden target-input" name="$inputName">
            <label class="icon-input">
                <i class="material-icons"></i>
                <input type="text" class="main-input search-input" placeholder="Wyszukaj" id="$id-input" value="$initialValue">
            </label>
            <span class="input-error"></span>
            <div class="search-results">
            </div>
        </section>
    HTML;
    }

    public function renderScript()
    {
        $id = $this->id;
        $timeout = $this->timeout;
        $endpointUrl = $this->endpointUrl;
        $preLoadedData = json_encode($this->preLoadedData);

        echo <<<HTML
        <script type="module">
            import { DebounceSelectSearchController } from '/public/js/controllers/debounce-search-controller.js';

            new DebounceSelectSearchController('$id', $endpointUrl, $timeout, JSON.parse($preLoadedData));
        </script>
        HTML;
    }
}
