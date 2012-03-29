<?php
/*Logout*/
require 'constants/access-functions.php';
$message = urlencode("You have logged out successfully!");
logout($message);