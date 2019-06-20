<?php

header('content-type: application/json');
ini_set('max_execution_time', 0);
require './list-module.php';


requestListData($_GET['q']);