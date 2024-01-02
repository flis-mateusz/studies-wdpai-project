<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class SectionPrompt extends Component
{
    private $text;
    public  function __construct($text)
    {
        $this->text = $text;
    }

    public static function initialize()
    {
    }

    public function render()
    {
        echo <<<HTML
        <section class="prompt"><span>$this->text</span></section>
    HTML;
    }
}
