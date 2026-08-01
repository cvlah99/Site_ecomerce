<?php
session_start();
session_destroy();
header("location:../acceulle/acceulle.php");
exit();
?>