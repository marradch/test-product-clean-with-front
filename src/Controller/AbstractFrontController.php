<?php

namespace App\Controller;

class AbstractFrontController
{
    protected function render(string $template, array $params = []): string
    {
        $viewPath = __DIR__ . '/../Views/' . $template;

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View file not found: $viewPath");
        }

        extract($params, EXTR_SKIP);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}