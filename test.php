<?php
require 'vendor/autoload.php';
session_start();
require 'App/config.php';
require 'App/Core/Database.php';
require 'App/Models/Model.php';
require 'App/Models/Produto.php';

$p = new \App\Models\Produto();
print_r($p->findWithCategoria(32));
print_r($p->getLotesComQuantidadeDisponivel(32));
