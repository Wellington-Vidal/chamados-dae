<?php
session_start();
unset($_SESSION["CNS"]);
unset($_SESSION["NOME"]);
unset($_SESSION["LOGADO"]);
unset($_SESSION["DATA"]);
unset($_SESSION["PERFIL"]);
session_destroy();
Header("Location: index.php");
exit();
?>