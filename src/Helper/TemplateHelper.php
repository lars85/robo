<?php

namespace LarsMalach\Robo\Helper;

class TemplateHelper
{
    public static function renderString(string $string, array $variables = []): string
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(['main' => $string]));
        return $twig->render('main', $variables);
    }
}