<?php
session_start();

if ((isset($_SESSION["CNS"])) && ($_SESSION["LOGADO"] == "SIM"))
{
	//LOGADO
	//echo "Conectado";
}
else
{
	Header("Location: index.php");
	exit();
}
?>