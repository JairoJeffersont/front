<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include 'pages/home/home.php';

$rotas = [
    'home' => 'pages/home/home.php',
    'proposicoes' => 'pages/proposicoes/proposicoes.php'


];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include 'pages/errors/404.php';
}
