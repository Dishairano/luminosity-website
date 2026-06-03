<?php

declare(strict_types=1);

/**
 * Front controller: enige toegangspunt. nginx stuurt alle verzoeken hierheen.
 */

use App\Core\Config;
use App\Core\Request;
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ApiController;
use App\Controllers\PrijzenController;
use App\Controllers\KlantAuthController;
use App\Controllers\PortaalController;
use App\Controllers\AdminAuthController;
use App\Controllers\AdminController;
use App\Controllers\WebhookController;

require dirname(__DIR__) . '/vendor/autoload.php';

Config::load(dirname(__DIR__) . '/config/config.php');

if (Config::get('app')['debug'] ?? false) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

$router = new Router();

// --- Marketing / demo ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/prijzen', [PrijzenController::class, 'index']);
$router->post('/api/verwerk',   [ApiController::class, 'verwerk']);
$router->post('/api/uitvoeren', [ApiController::class, 'uitvoeren']);
$router->get('/api/logboek',    [ApiController::class, 'logboek']);

// --- Klant: auth ---
$router->get('/registreren',  [KlantAuthController::class, 'toonRegistreren']);
$router->post('/registreren', [KlantAuthController::class, 'registreren']);
$router->get('/inloggen',     [KlantAuthController::class, 'toonInloggen']);
$router->post('/inloggen',    [KlantAuthController::class, 'inloggen']);
$router->post('/uitloggen',   [KlantAuthController::class, 'uitloggen']);

// --- Klantpaneel ---
$router->get('/portaal',                       [PortaalController::class, 'dashboard']);
$router->get('/portaal/downloads',             [PortaalController::class, 'downloads']);
$router->get('/portaal/abonneren/{code}',      [PortaalController::class, 'abonneren']);
$router->post('/portaal/abonneren',            [PortaalController::class, 'bevestigAbonnement']);
$router->get('/portaal/facturen',              [PortaalController::class, 'facturen']);
$router->get('/portaal/facturen/{id}',         [PortaalController::class, 'factuur']);
$router->get('/portaal/facturen/{id}/betalen', [PortaalController::class, 'betalen']);
$router->get('/portaal/facturen/{id}/terug',   [PortaalController::class, 'terugVanBetaling']);
$router->get('/portaal/facturen/{id}/simuleer',[PortaalController::class, 'simuleer']);
$router->get('/portaal/facturen/{id}/print',   [PortaalController::class, 'factuurPrint']);
$router->get('/portaal/profiel',               [PortaalController::class, 'profiel']);
$router->post('/portaal/profiel',              [PortaalController::class, 'profielOpslaan']);
$router->post('/portaal/abonnement/opzeggen',  [PortaalController::class, 'zegOp']);

// --- Admin: auth ---
$router->get('/admin/inloggen',  [AdminAuthController::class, 'toon']);
$router->post('/admin/inloggen', [AdminAuthController::class, 'inloggen']);
$router->post('/admin/uitloggen',[AdminAuthController::class, 'uitloggen']);

// --- Adminpaneel ---
$router->get('/admin',                          [AdminController::class, 'dashboard']);
$router->get('/admin/klanten',                  [AdminController::class, 'klanten']);
$router->get('/admin/klanten/{id}',             [AdminController::class, 'klant']);
$router->post('/admin/klanten/{id}/status',     [AdminController::class, 'klantStatus']);
$router->get('/admin/abonnementen',             [AdminController::class, 'abonnementen']);
$router->post('/admin/abonnementen/{id}/status',[AdminController::class, 'abonnementStatus']);
$router->get('/admin/facturen',                 [AdminController::class, 'facturen']);
$router->get('/admin/facturen/nieuw',           [AdminController::class, 'nieuweFactuurForm']);
$router->post('/admin/facturen/nieuw',          [AdminController::class, 'nieuweFactuur']);
$router->get('/admin/facturen/{id}',            [AdminController::class, 'factuur']);
$router->post('/admin/facturen/{id}/betaald',   [AdminController::class, 'factuurBetaald']);
$router->get('/admin/facturen/{id}/print',      [AdminController::class, 'factuurPrint']);
$router->get('/admin/plannen',                  [AdminController::class, 'plannen']);
$router->post('/admin/plannen/{id}',            [AdminController::class, 'planOpslaan']);

// --- Mollie webhook ---
$router->post('/webhook/mollie', [WebhookController::class, 'mollie']);

$router->dispatch(new Request());
