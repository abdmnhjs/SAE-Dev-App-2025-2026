<?php

class EnvLoader
{
    public static function load($path)
    {
        if (!file_exists($path)) {
            throw new Exception("Le fichier .env est introuvable : $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) continue;

            // Séparer clé et valeur
            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);

            // Supprimer les guillemets si présents
            $value = trim($value, "\"'");

            // Stocker dans $_ENV et getenv()
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}
