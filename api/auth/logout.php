<?php 

session_start();

require_once __DIR__ . '/../../utils/auth.php';

$role = $_SESSION['role'];

session_unset();
session_destroy();

if($role === 'admin') {
    redirectUser('../../views/admin/auth/admin_login.php');
} 

if($role === 'bus_rep') {
    redirectUser('../../views/business_rep/auth/business_rep_login.php');
} 

if($role === 'driver') {
    redirectUser('../../views/driver/auth/driver_login.php');
}

if($role === 'basic') {
    echo "Redirecting to user to journeolink landing page...";
}

exit();

?>