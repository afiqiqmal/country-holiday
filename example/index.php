<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ .'/../vendor/autoload.php';

use Holiday\Holiday;

$result = Holiday::for('albania')
    ->fromState('Abia')
    ->get();


header('Content-Type: application/json');
echo json_encode($result);
