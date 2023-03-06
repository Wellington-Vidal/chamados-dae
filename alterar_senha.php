<?php
	require_once 'valida_sessao.php';
	require_once 'configuracoes/configuracoes.php';
	require_once 'utilidades/funcoes.php';

	require_once 'objetos/Profissionais.php';

	require_once 'objetosDAO/ProfissionaisDAO.php';

	$nomeProf = $_SESSION["NOME"];
	$cnsProf = $_SESSION["CNS"];
	$perfil = $_SESSION["PERFIL"];
	$senhaAtual = "";
	$novaSenha = "";
	$confSenha = "";
	
	$msg_erro = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$acao = $_POST["acao"];
		
		if (empty($_POST["antigaSenha"]))
		{
			$msg_erro = "Digite a senha atual!";
		}
		else
		{
			$senhaAtual = md5($_POST["antigaSenha"]);
		}
		
		if (empty($_POST["novaSenha"]))
		{
			$msg_erro = "Digite a Nova Senha do Profissional!";
		}
		else
		{
			$novaSenha = md5($_POST["novaSenha"]);
		}
		
		if (empty($_POST["confSenha"]))
		{
			$msg_erro = "Digite a Confirmação da Nova Senha!";
		}
		else
		{
			$confSenha = md5($_POST["confSenha"]);
		}
		
		if (($msg_erro == "") || ($acao == "EXCLUIR"))
		{
			if ((!empty($_POST["novaSenha"])) && (!empty($_POST["confSenha"])) && ($_POST["novaSenha"] == $_POST["confSenha"]))
			{
				if ($cnsProf == $_SESSION['CNS'])
				{
					$profissional = new Profissionais($perfil, 'ATIVO');
					$profissional->setCnsPessoa($cnsProf);
					$profissional->setSenha($senhaAtual);

					$profissionalDao = new ProfissionaisDAO();
					$profissional = $profissionalDao->verificaProfissional($profissional);

					if (empty($profissional))
					{
						$msg_erro = "Senha atual não corresponde!";
					}
					else
					{
						$profissional->setSenha($novaSenha);
						$res = $profissionalDao->alteraProfissional($profissional);

						if ($res == 1)
						{
							$msg_erro = "Senha alterada com sucesso!";
						}
						else if ($res == 2)
						{
							$msg_erro = "Erro ao alterar a senha!";
						}
						else
						{
							$msg_erro = "Senha não alterada!";
						}
					}
				}
			}
			else
			{
				$msg_erro = "Digite a mesma senha nos campos NOVA SENHA e CONFIRMA SENHA!";
			}
		}
	}
?>
<!DOCTYPE html>
<html lang='pt-BR'>
	<head>
		<title>Alterar Senha</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<meta charset="utf-8">
	</head>
	
	<body>
		<script>
			function Salvar()
			{
				let acao = document.getElementById("acao").value;
				let nomeProf = document.getElementById("nomeProf").value;
				
				if (acao == "ALTERAR")
				{
					var res = confirm("Deseja Alterar sua Senha (" + nomeProf + ")?");

					if (res == true)
					{
						document.getElementById("cnsProf").disabled = false;
						document.frm_cad_op.submit();
					}
				}
			}
			function Limpar()
			{
				document.getElementById("antigaSenha").value = "";
				document.getElementById("novaSenha").value = "";
				document.getElementById("confSenha").value = "";
				
				document.getElementById("acao").value = "ALTERAR";
			}
			function noIframe () 
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
			
			if (noIframe() != "VERDADEIRO")
			{
				top.location.href = <?php echo "'" . URL_APP . "/administrador.php'"; ?>;
			}
		</script>

		<div class="container_dois">
			<div class="titulo">
				<h2>Alterar Senha</h2>
			</div>
			<form name="frm_cad_op" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<fieldset>
					<legend>Dados do Profissional</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
							<div style="width:50%;">
								<input type="text" id="nomeProf" name="nomeProf" placeholder="<?php if (isset($nomeProf)){echo $nomeProf;} ?>" value="<?php if (isset($nomeProf)){echo $nomeProf;} ?>" readonly disabled />
							</div>
							<div style="width:30%;">
								<input type="text" id="cnsProf" name="cnsProf" placeholder="<?php if (isset($cnsProf)){echo $cnsProf;} ?>" value="<?php if (isset($cnsProf)){echo $cnsProf;}?>" readonly disabled/>
							</div>
							<div style="width:20%;">
								<input type="password" id="antigaSenha" name="antigaSenha" placeholder="Senha Atual" maxlength="30"/>
							</div>
							<div style="width:20%;">
								<input type="password" id="novaSenha" name="novaSenha" placeholder="Nova Senha" maxlength="30"/>
							</div>
							<div style="width:20%;">
								<input type="password" id="confSenha" name="confSenha" placeholder="Confirma Nova Senha" maxlength="30"/>
							</div>
						</div>
					</div>
				</fieldset>
				
				<button type="button" id="salvar" name="salvar" onClick="Salvar()">Alterar Senha</button>
				<button type="button" onclick="Limpar()">Limpar</button>
                
				<input type="hidden" id="acao" name="acao" value="ALTERAR"/>
			</form>
			
			<?php 
				if ($msg_erro != "")
				{
					echo "<script>alert('$msg_erro');</script>";
				}
			?>
		</div><br>

		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>