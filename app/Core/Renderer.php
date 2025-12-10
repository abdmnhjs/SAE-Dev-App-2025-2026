<?php

class Renderer
{


    public function __construct(private string $viewPath, private array $params = [])
    {

    }

    public function view()
    {
        ob_start(); // met en pause et lance tout d'un coup (buffer)
        //var id;
        //var tableau;...
        extract($this->params);

        //require BASE_VIEW_PATH . 'includes/barnav.php';
        require BASE_VIEW_PATH . $this->viewPath . '.php';
        return ob_get_clean();
    }

    public static function make(string $viewPath, array $params = []): static
    {
        return new static($viewPath, $params);
    }

    /*
     * converti l'objet Renderer donner dans les controlleurs en string pour que le require accept.
     */
    public function __toString(): string
    {
        return $this->view();
    }
}