<?php

namespace App\Core;

use App\Models\Klant;
use App\Models\Admin;

/**
 * Basis-controller met helpers voor views, JSON, redirects, auth-guards,
 * flash-berichten en CSRF. Klant/Admin worden als associatieve array
 * teruggegeven (consistent met de overige modellen).
 */
abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layout'): void
    {
        (new View())->render($view, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    // --- Auth-guards (geven de ingelogde rij terug, of redirecten) ---
    protected function vereisKlant(): array
    {
        $id = Auth::klantId();
        $klant = $id ? Klant::vind($id) : null;
        if (!$klant) {
            $this->redirect('/inloggen');
        }
        return $klant;
    }

    protected function vereisAdmin(): array
    {
        $id = Auth::adminId();
        $admin = $id ? Admin::vind($id) : null;
        if (!$admin) {
            $this->redirect('/admin/inloggen');
        }
        return $admin;
    }

    // --- CSRF ---
    protected function checkCsrf(Request $request): void
    {
        if (!Csrf::check($request->post('_csrf'))) {
            http_response_code(419);
            echo 'Sessie verlopen, probeer opnieuw.';
            exit;
        }
    }

    // --- Flash-berichten ---
    protected function flash(string $type, string $bericht): void
    {
        Auth::start();
        $_SESSION['_flash'][] = ['type' => $type, 'bericht' => $bericht];
    }

    public static function neemFlash(): array
    {
        Auth::start();
        $f = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $f;
    }
}
