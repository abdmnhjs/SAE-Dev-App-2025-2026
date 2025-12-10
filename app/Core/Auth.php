<?php

class Auth
{
    public static function requireRole(int $minLevel): void
    {
        $userLevel = $_SESSION['rank'] ?? 0;
        if ($userLevel < $minLevel) {
            $errorController = new ErrorController();

            // Choose the right response
            if ($userLevel === 0) {
                // Not logged in
                $errorController->unauthorized();
                exit;
            } else {
                // Logged in but insufficient permissions
                $errorController->forbidden();
                exit;
            }

        }
    }
}
