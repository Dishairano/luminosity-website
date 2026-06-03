<?php

namespace App\Core;

/**
 * Rendert een view binnen de layout. Views zijn gewone PHP-bestanden in
 * app/Views/. Variabelen worden als losse variabelen beschikbaar gemaakt.
 */
final class View
{
    /**
     * Rendert een view binnen een layout. $layout = 'layout' (marketing),
     * 'portaal/layout' (klantpaneel), 'admin/layout' (adminpaneel), of null
     * voor geen layout (bv. printbare factuur).
     */
    public function render(string $view, array $data = [], ?string $layout = 'layout'): void
    {
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View niet gevonden: ' . htmlspecialchars($view);
            return;
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }
        require dirname(__DIR__) . '/Views/' . $layout . '.php';
    }

    /** Veilige HTML-escape voor in views. */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
