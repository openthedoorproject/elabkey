<?php

    class ErrorController{
        public function index(){
            $loader = new \Twig\Loader\FilesystemLoader('app/view');
            $twig = new \Twig\Environment($loader);
            $template = $twig->load('error.html');

            $parametros = array();

            $conteudo = $template->render($parametros);
            echo $conteudo;
        }
    }

?>
