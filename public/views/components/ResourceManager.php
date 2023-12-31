<?php

class ResourceManager
{
    private static $scripts = [];
    private static $styles = [];

    public static function addScript($script, $defer = true, $module = false)
    {
        $scriptData = ['src' => $script, 'defer' => $defer, 'module' => $module];

        if (!in_array($scriptData, self::$scripts)) {
            self::$scripts[] = $scriptData;
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
            $defer = $script['defer'] ? 'defer' : '';
            $type = $script['module'] ? "type='module'" : '';
            echo "<script src='{$script['src']}' $defer $type></script>";
        }
    }
}
