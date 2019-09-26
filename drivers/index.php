<?php

session_start();


print '<pre>';

$_SESSION['user'] = 'new user';

print_r($_SESSION);

print '<br>';

print_r($_COOKIE);

print '</pre>';

?>