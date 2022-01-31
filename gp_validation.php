<?php
$res = $_GET['result'];
$res = json_encode(array('res'=>$res));
echo $res;exit;