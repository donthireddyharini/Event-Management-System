<?php 

// Database Configuration — InfinityFree Production
$db_host = 'sql200.infinityfree.com';
$db_user = 'if0_42912700';
$db_pass = 'harini114433';
$db_name = 'if0_42912700_event_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name) or die("Could not connect to mysql: " . mysqli_connect_error());
