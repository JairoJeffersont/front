<?php

$pagina = isset($_GET['secao']) ? $_GET['secao'] :  include 'pages/home/home.php';

$rotas = [
    'home' => 'pages/home/home.php',
    'login' => 'pages/login/login.php',

    'usuarios' => 'pages/usuarios/usuarios.php',
    'usuario' => 'pages/usuarios/usuario.php',
    'proposicoes' => 'pages/proposicoes/proposicoes.php',
    'atualizar-proposicoes' => 'pages/proposicoes/atualizar-proposicoes.php'

];

if (array_key_exists($pagina, $rotas)) {
    include $rotas[$pagina];
} else {
    include 'pages/errors/404.php';
}
