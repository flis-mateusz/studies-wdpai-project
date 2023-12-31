<?php

require_once __DIR__ . '/ResourceManager.php';
require_once __DIR__ . '/Component.php';

class CustomContentLoader extends Component
{
    public static function initialize()
    {
        ResourceManager::addStyle('/public/css/components/custom_loader.css');
    }

    public function render()
    {
        echo <<<HTML
        <div class="custom-loader-container hidden">
        <div class="custom-loader">
            <div class="track">
                <div class="mouse"></div>
            </div>
            <div class="face">
                <div class="ears-container"></div>
                <div class="eyes-container">
                    <div class="eye"></div>
                    <div class="eye"></div>
                </div>
                <div class="phiz">
                    <div class="nose"></div>
                    <div class="lip"></div>
                    <div class="mouth"></div>
                </div>
            </div>
        </div>
    </div>
    HTML;
    }
}
