<?php

class ResourceManager
{
    private static $scripts = [];
    private static $styles = [];

    public static function addScript($script)
    {
        if (!in_array($script, self::$scripts)) {
            self::$scripts[] = $script;
        }
    }

    public static function addStyle($style)
    {
        if (!in_array($style, self::$styles)) {
            self::$styles[] = $style;
        }
    }

    public static function appendResources()
    {
        foreach (self::$styles as $style) {
            echo "<link rel='stylesheet' href='$style'>";
        }
        foreach (self::$scripts as $script) {
            echo "<script src='$script' defer></script>";
        }
    }
}
