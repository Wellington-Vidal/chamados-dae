<?php
session_start();

	require_once 'objetos/Profissionais.php';
	require_once 'objetosDAO/ProfissionaisDAO.php';
	require_once 'configuracoes/configuracoes.php';
	require_once 'utilidades/funcoes.php';

	$cns = "";
	$senha = "";
	
	$acesso_negado = "";
	$msg_erro = "";
	
	if (isset($_SESSION["CNS"]))
	{
		Header("Location: administrador.php");
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{	
		if (empty($_POST["cns"]))
        {
            $msg_erro = "* Cartão do SUS: Campo Obrigatório";
        }
        else
        {
            $cns = FiltraDados($_POST["cns"]);
            
            if (empty($_POST["senha"]))
            {
                $msg_erro = "* Senha: Campo Obrigatório";
            }
            else
            {
                $senha = FiltraDados($_POST["senha"]);
            }
        }

		if ($msg_erro == "")
		{
			$senha = md5($senha);
			
			$op = new Profissionais('', '');
			$op->setCnsPessoa($cns);
			$op->setSenha($senha);

			$profissionalDao = new ProfissionaisDAO();
			$profissional = $profissionalDao->verificaProfissional($op);

			echo "<script>alert('$senha');</script>";

			if ($profissional == null)
			{
				$msg_erro = "Login ou Senha incorretos.";
			}
			else
			{
				if ($profissional->getStatusProf() == 'ATIVO')
				{
					date_default_timezone_set('America/Sao_Paulo');
					$dataHora = date("d/m/Y H:i:s");
					
					$_SESSION["CNS"] = $profissional->getCnsPessoa();
					$_SESSION["NOME"] = $profissional->getNomePessoa();
					$_SESSION["LOGADO"] = "SIM";
					$_SESSION["DATA"] = $dataHora;
					$_SESSION["PERFIL"] = $profissional->getPerfilProf();
					
					Header("Location: administrador.php");
				}
				else
				{
					$msg_erro = "Operador Inativo, entre em contato com o Administrador!";
				}
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>DAE OLINDA</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
	</head>
	<body>
		<script>
			function noIframe() 
			{
				try
				{
					var res = "FALSO";
					
					if (self.location != top.location)
					{
						res = "VERDADEIRO";
					}
					
					return res;
				}
				catch (e)
				{
					return "VERDADEIRO";
				}
			}
			
			if (noIframe() != "FALSO")
			{
				top.location.href = <?php echo "'" . URL_APP . "/logout.php'"; ?>;
			}
		</script>

		<script src="javascripts/app.js?v=<?php echo VERSAO; ?>"></script>
	
		<div class="container_login">
			<header>
				<img src="imagens/LogoSPA.png" alt="DAE - OLINDA">
			</header>
			
			<form class="frm_login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<input type="text" name="cns" id="cns" size="30" maxlength="15" value="" placeholder="Cartão do SUS" onkeyup="Numerico(this)" onkeydown="Numerico(this)" />
				<input type="password" name="senha" id="senha" size="15" maxlength="15" value="" placeholder="Senha"/>
				<input id="btn_frm_login" type="submit" value="Entrar"/>
			</form>	
		</div>
		
		<?php if ($msg_erro != ""){echo "<script>alert('$msg_erro');</script>";}?>
		
		<footer>
			<span>&copy;<?php echo date("Y")?> - Sistema CHAMADO - DAE</span>
		</footer>
	</body>
</html>