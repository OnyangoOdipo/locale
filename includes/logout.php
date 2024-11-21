<?php
session_start();
session_destroy();
header('Location: /Locale/pages/login.php');
exit();
?> 