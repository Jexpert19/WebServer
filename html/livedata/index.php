<?php
header('Content-Type: application/json'); 

// ========== Database connection ==========

$servername = $_SERVER['SERVER_ADDR'];
$dbIp = "mariadb";
$username = 'root';
$password = 'root';

set_error_handler("error_handler", E_WARNING);
function error_handler($errno, $errstr) { 
  die(json_encode("No DB connection"));
}

$conn = mysqli_connect("$dbIp:3306", $username, $password,'hydroponic_test_system');

restore_error_handler();

$sql = "SELECT * FROM `parameters`";

$rs = mysqli_query($conn, $sql);

$rows = array();
while($row = mysqli_fetch_assoc($rs)) {
  $rows[$row['parameter_name']] = $row['parameter_value'];
}
print json_encode($rows);

$conn->close();
?>