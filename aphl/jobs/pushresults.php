<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(PHP_SAPI === 'cli') or die('This script runs only on CLI');

require_once('../libs/dhis2.php');

$dhis = new DHIS2API();
LIB::pushResults();
