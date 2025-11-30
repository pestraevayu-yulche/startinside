<?php
session_start();
session_unset();
session_destroy();
header("Location: avtor.php?success=logout");
exit();
?>