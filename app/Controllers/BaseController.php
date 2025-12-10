<?php

class BaseController
{
    protected ?string $username;

    public function __construct()
    {
        $this->username = $_SESSION['username'] ?? null;
    }

    protected function render(string $path, array $data = []): Renderer
    {
        //WIP : erreur si l'utilisateur n'est plus connecter entre chaque changement
        //WIP : isLoggedIn() de User.php pour ca.

        $globalVars = [
            'username' => $this->username
        ];

        // Merge global + controller-specific data
        $vars = array_merge($globalVars, $data);

        // Pass everything to Renderer
        return Renderer::make($path, $vars);
    }

    protected function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }
}
