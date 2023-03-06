<?php
	require_once 'valida_sessao.php';
	require_once 'configuracoes/configuracoes.php';
	require_once 'utilidades/funcoes.php';

	require_once 'objetos/Pessoa.php';
	require_once 'objetos/Profissionais.php';

	require_once 'objetosDAO/ProfissionaisDAO.php';
	
	header('Content-Type: text/html; charset=utf-8');

	$nomeProf = "";
	$cnsProf = "";
	$senha = "";
	$confSenha = "";
	$status = "";
	$perfil = "";
	
	$msg_erro = "";

	$senha = "SPA" . date("d/m/Y");
	$cripto = md5($senha);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$acao = $_POST["acao"];
		
		if ((empty($_POST["status"])) || (($_POST["status"]) == "selecione"))
		{
			$msg_erro = "Selecione o Status de Acesso do Profissional!";
		}
		else
		{
			$status = FiltraDados($_POST["status"]);
		}
		
		if ((empty($_POST["perfil"])) || (($_POST["perfil"]) == "selecione"))
		{
			$msg_erro = "Selecione o Perfil de Acesso do Profissional!";
		}
		else
		{
			$perfil = FiltraDados($_POST["perfil"]);
		}
		
		if (empty($_POST["confSenha"]))
		{
			$msg_erro = "Digite a Confirmação da Senha!";
		}
		else
		{
			$confSenha = md5($_POST["confSenha"]);
		}

		if (empty($_POST["senha"]))
		{
			$msg_erro = "Digite a Senha do Profissional!";
		}
		else
		{
			$senha = md5($_POST["senha"]);
		}
		
		if (empty($_POST["cnsProf"]))
		{
			$msg_erro = "Digite o Cartão do SUS do Profissional!";
		}
		else
		{
			//Validar Cartão do SUS
			$cnsProf = FiltraDados($_POST["cnsProf"]);

			if (!Pessoa::validaCNS($cnsProf))
			{
				$msg_erro = "Digite um Cartão do SUS Válido!";
			}
		}
		
		if (empty($_POST["nomeProf"]))
		{
			$msg_erro = "Digite o Nome do Profissional!";
		}
		else
		{
			$nomeProf = FiltraDados($_POST["nomeProf"]);
			$nomeProf = trim($nomeProf);
		}
		
		if (($msg_erro == "") || ($acao == "EXCLUIR"))
		{
			if (((!empty($_POST["senha"])) && (!empty($_POST["confSenha"])) && ($_POST["senha"] == $_POST["confSenha"])) || ($acao == "EXCLUIR"))
			{
				$profissional = new Profissionais($perfil, $status);
				$profissional->setNomePessoa($nomeProf);
				$profissional->setCnsPessoa($cnsProf);
				$profissional->setSenha($senha);

				$profissionaisDao = new ProfissionaisDAO();
				$res = 0;

				if ($acao == "")
				{
					$res = $profissionaisDao->insereProfissional($profissional);
				}
				else if ($acao == "ALTERAR")
				{
					$res = $profissionaisDao->alteraProfissional($profissional);
				}
				else if ($acao == "EXCLUIR")
				{
					//FAZER
				}

				if ($res == 1)
				{
					$msg_erro = "Profissional salvo com sucesso!";
				}
				else if ($res == 2)
				{
					$msg_erro = "Erro ao alterar profissional!";
				}
				else
				{
					$msg_erro = "Ação não realizada!";
				}
			}
			else
			{
				$msg_erro = "Digite a mesma senha nos campos SENHA e CONFIRMA SENHA!";
			}
		}
	}
	
	function ExibirProfissionais()
	{
		define('TR_I', '<tr>');
		define('TR_F', '</tr>');
		define('TH_I', '<th>');
		define('TH_F', '</th>');
		define('TD_I', '<td>');
		define('TD_F', '</td>');

		$tabelaEnt = "<div style=\"height:auto; width:100%; overflow: auto;\">\n";
		$tabelaEnt = $tabelaEnt . "<table border='1'>";
		$tabelaEnt = $tabelaEnt . TR_I;
		$tabelaEnt = $tabelaEnt . TH_I . "CNS Profissional" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Nome Profissional" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Perfil" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Situação" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Editar" . TH_F;
		$tabelaEnt = $tabelaEnt . TR_F;
		
		$profissionaisDao = new ProfissionaisDAO();
		$listaProfissionais = $profissionaisDao->listaProfissionais();

		if (!empty($listaProfissionais))
		{
			for ($p = 0 ; $p < count($listaProfissionais) ; $p++)
			{
				$cnsProf = $listaProfissionais[$p]->getCnsPessoa();
				$nomeProf = $listaProfissionais[$p]->getNomePessoa();
				$perfilProf = $listaProfissionais[$p]->getPerfilProf();
				$statusProf = $listaProfissionais[$p]->getStatusProf();

				$botaoEditar = "<button type='button' onclick=\"EditarProfissional('$cnsProf')\" style=\"padding: 0px;\">Editar</button>";

				$tabelaEnt = $tabelaEnt . TR_I;
				$tabelaEnt = $tabelaEnt . TD_I . $cnsProf . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeProf . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $perfilProf . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $statusProf .  TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $botaoEditar . TD_F;
				$tabelaEnt = $tabelaEnt . TR_F;
			}
		}
		else
		{
			$tabelaEnt = $tabelaEnt . TR_I;
			$tabelaEnt = $tabelaEnt . "<td colspan='5'>" . "Sem Registros" . TD_F;
			$tabelaEnt = $tabelaEnt . TR_F;
		}
		
		return $tabelaEnt;
	}
	
	function PerfisDeAcesso($perfil)
	{
		$opcoes = '';

		$perfis = array('EXECUTANTE' => 'Executante', 
						'ADMINISTRADOR' => 'Administrador');

		foreach ($perfis as $chave => $valor)
		{
			$sel = '';

			if ($perfil == $chave)
			{
				$sel = ' selected';
			}

			$opcoes = $opcoes . "<option value='$chave'$sel>$valor</option>";
		}

		return $opcoes;
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Profissionais</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<meta charset="utf-8">
	</head>
		
	<body>
		<script>
			const SSS = <?php echo "'$cripto'"; ?>;

			function Salvar()
			{
				var acao = document.getElementById("acao").value;
				var cns = document.getElementById("cnsProf").value;
				
				if (acao == "ALTERAR")
				{
					var res = confirm("Deseja Alterar este Profissional (" + cns + ")?");

					if (res == true)
					{
						document.getElementById("cnsProf").disabled = false;
						document.getElementById("perfil").disabled = false;
						document.frm_cad_op.submit();
					}
				}
				else if (acao == "")
				{
					document.frm_cad_op.submit();
				}
			}

			function EditarProfissional(cnsProf)
			{
				try
				{
					var xhttp;
					xhttp = new XMLHttpRequest();

					xhttp.onreadystatechange = function(){
						if (this.readyState == 4 && this.status == 200)
						{
							let dadosRes = JSON.parse(this.responseText);
							//alert(JSON.stringify(dadosRes));
							if (dadosRes.length > 0)
							{
								for (let i = 0 ; i < dadosRes.length ; i++)
								{
									if (dadosRes[i]['profissional'])
									{
										let cns = dadosRes[i]['profissional'].cns;
										let nomeProf = dadosRes[i]['profissional'].nome;
										let perfilProf = dadosRes[i]['profissional'].perfil;
										let statusProf = dadosRes[i]['profissional'].status;

										document.getElementById("nomeProf").value = nomeProf;
										document.getElementById("cnsProf").value = cns;
										document.getElementById("perfil").value = perfilProf;
										document.getElementById("status").value = statusProf;
										
										document.getElementById("cnsProf").disabled = true;
										document.getElementById("perfil").disabled = true;
										document.getElementById("acao").value = "ALTERAR";
										document.getElementById("salvar").innerHTML = "Alterar";
										
										document.body.scrollTop = 0;
										document.documentElement.scrollTop = 0;
									}
								}
							}
						}
					};
					
					xhttp.open("POST", "res_ajax.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("cns=" + cnsProf + "&funcao=9" + "&sss=" + SSS);
				}
				catch(erro)
				{
					console.log(erro.message);
				}
			}

			function ExcluirOperador(nomeOp, cns, perfil, status)
			{
				var res = confirm("Deseja Excluir este Operador (" + cns + ")?");

				if (res == true)
				{
					document.getElementById("nome_op").value = nomeOp;
					document.getElementById("cns").value = cns;
					
					document.getElementById("acao").value = "EXCLUIR";
					document.getElementById("cns").disabled = false;
					document.frm_cad_op.submit();
				}
				else
				{
					ocument.getElementById("nome_op").value = "";
					document.getElementById("cns").value = "";
					
					document.getElementById("acao").value = "";
					document.getElementById("cns").disabled = false;
				}
			}

			function Limpar()
			{
				document.getElementById("nomeProf").value = "";
				document.getElementById("cnsProf").value = "";
				document.getElementById("senha").value = "";
				document.getElementById("confSenha").value = "";
				document.getElementById("perfil").value = "selecione";
				document.getElementById("status").value = "selecione";
				
				document.getElementById("cnsProf").disabled = false;
				document.getElementById("perfil").disabled = false;
				document.getElementById("acao").value = "";
				document.getElementById("salvar").innerHTML = "Cadastrar";
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
				<h2>Cadastro de Profissionais</h2>
			</div>
			<form name="frm_cad_op" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<fieldset>
					<legend>Dados do Profissional</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
							<div style="width:50%;">
								<input type="text" id="nomeProf" name="nomeProf" maxlength="100" placeholder="Nome do Profissional" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($nomeProf)){echo $nomeProf;} ?>" />
							</div>
							<div style="width:30%;">
								<input type="text" id="cnsProf" name="cnsProf" maxlength="15" placeholder="Cartão do SUS" onkeyup="Numerico(this)" onkeydown="Numerico(this)" value="<?php if (isset($cnsProf)){echo $cnsProf;}?>"/>
							</div>
							<div style="width:20%;">
								<input type="password" id="senha" name="senha" placeholder="Senha" maxlength="30"/>
							</div>
							<div style="width:20%;">
								<input type="password" id="confSenha" name="confSenha" placeholder="Confirma Senha" maxlength="30"/>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Parâmetros do Acesso</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
							<div style="width:30%;">
								<select id='perfil' name='perfil'>
									<option value='selecione'>Perfil</option>
									<?php
										if (!isset($perfil))
										{
											$perfil = '';
										}

										echo PerfisDeAcesso($perfil);
									?>
								</select>
							</div>
							<div style="width:20%;">
								<select id='status' name='status'>
									<option value='selecione'>Status</option>
									<option value='ATIVO'<?php if ((isset($status)) && ($status == 'ATIVO')){echo ' selected';}?>>Ativo</option>
									<option value='INATIVO'<?php if ((isset($status)) && ($status == 'INATIVO')){echo ' selected';}?>>Inativo</option>
								</select>
							</div>
						</div>
					</div>
				</fieldset>
				
				<button type="button" id="salvar" name="salvar" onClick="Salvar()">Cadastrar</button>
				<button type="button" onclick="Limpar()">Limpar</button>
                
				<input type="hidden" id="acao" name="acao" value=""/>
			</form>
			
			<?php 
				if ($msg_erro != "")
				{
					echo "<script>alert('$msg_erro');</script>";
				}
			?>
		</div><br>

		<?php
			echo "<center>" . ExibirProfissionais() . "</center>";
		?>

		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>