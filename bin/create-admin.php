<?php
// Maakt een admin-account aan (wachtwoord wordt veilig gehasht).
// Gebruik:  php bin/create-admin.php "Naam" email@adres.nl wachtwoord
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Models\Admin;

Config::load(__DIR__ . '/../config/config.php');

$naam  = $argv[1] ?? 'Beheerder';
$email = strtolower($argv[2] ?? 'admin@luminosity-os.nl');
$ww    = $argv[3] ?? bin2hex(random_bytes(6));

if (Admin::vindOpEmail($email)) {
    echo "Admin bestaat al: $email\n";
    exit(0);
}
$id = Admin::maak($naam, $email, password_hash($ww, PASSWORD_DEFAULT));
echo "Admin #$id aangemaakt.\n  E-mail: $email\n  Wachtwoord: $ww\n";
