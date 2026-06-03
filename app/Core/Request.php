<?php

namespace App\Core;

/**
 * Bundelt de inkomende HTTP-aanvraag: methode, pad en (JSON-)body.
 */
final class Request
{
    public readonly string $method;
    public readonly string $path;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';
    }

    /** Leest de JSON-body en geeft een veld terug (of standaardwaarde). */
    public function json(string $key, mixed $default = null): mixed
    {
        static $body = null;
        if ($body === null) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            $body = is_array($decoded) ? $decoded : [];
        }
        return $body[$key] ?? $default;
    }

    /** Een POST-veld (formulier), getrimd. */
    public function post(string $key, ?string $default = null): ?string
    {
        $v = $_POST[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }

    /** Een GET-queryparameter. */
    public function query(string $key, ?string $default = null): ?string
    {
        $v = $_GET[$key] ?? $default;
        return is_string($v) ? trim($v) : $default;
    }
}
