<?php
	require_once 'valida_sessao.php';
	require_once 'configuracoes/configuracoes.php';
	require_once 'utilidades/funcoes.php';

	require_once 'objetos/Unidades.php';

	require_once 'objetosDAO/UnidadesDAO.php';
	
	header('Content-Type: text/html; charset=utf-8');

	$cnesUnidade = "";
	$nomeUnidade = "";
	$cepUnidade = "";
	$logrUnidade = "";
	$numUnidade = "";
	$bairroUnidade = "";
	$municipioUnidade = "";
	$ufUnidade = "";
	
	$msg_erro = "";

	$senha = "SPA" . date("d/m/Y");
	$cripto = md5($senha);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$acao = $_POST["acao"];
		
		if ((empty($_POST["selUf"])) || (($_POST["selUf"]) == "selecione"))
		{
			$msg_erro = "Selecione o Uf da Unidade!";
		}
		else
		{
			$ufUnidade = FiltraDados($_POST["selUf"]);
		}
		
		if (empty($_POST["municipioUnidade"]))
		{
			$msg_erro = "Digite o Município da Unidade!";
		}
		else
		{
			$municipioUnidade = FiltraDados($_POST["municipioUnidade"]);
		}
		
		if (empty($_POST["bairroUnidade"]))
		{
			$msg_erro = "Digite a Bairro da Unidade!";
		}
		else
		{
			$bairroUnidade = FiltraDados($_POST["bairroUnidade"]);
		}

		if (empty($_POST["numUnidade"]))
		{
			$msg_erro = "Digite a Número do Endereço da Unidade!";
		}
		else
		{
			$numUnidade = FiltraDados($_POST["numUnidade"]);
		}

		if (empty($_POST["logrUnidade"]))
		{
			$msg_erro = "Digite o Logradouro da Unidade!";
		}
		else
		{
			$logrUnidade = FiltraDados($_POST["logrUnidade"]);
		}

		if (empty($_POST["cepUnidade"]))
		{
			$msg_erro = "Digite o CEP da Unidade!";
		}
		else
		{
			$cepUnidade = FiltraDados($_POST["cepUnidade"]);
		}

		if (empty($_POST["nomeUnidade"]))
		{
			$msg_erro = "Digite o Nome da Unidade!";
		}
		else
		{
			$nomeUnidade= FiltraDados($_POST["nomeUnidade"]);
		}
		
		if (empty($_POST["cnesUnidade"]))
		{
			$msg_erro = "Digite o CNES da Unidade!";
		}
		else
		{
			$cnesUnidade = FiltraDados($_POST["cnesUnidade"]);

			if (!validaCNES($cnesUnidade))
			{
				$msg_erro = "Digite um CNES Válido!";
			}
		}

		$unidadesDao = new UnidadesDAO();
		$listaUnidades = $unidadesDao->listaUnidades();

		if ((!empty($listaUnidades)) && ($acao == ""))
		{
			$msg_erro = "Aplicação permite apenas uma unidade de saúde!";

			$cnesUnidade = "";
			$nomeUnidade = "";
			$cepUnidade = "";
			$logrUnidade = "";
			$numUnidade = "";
			$bairroUnidade = "";
			$municipioUnidade = "";
			$ufUnidade = "";
		}
		
		if (($msg_erro == "") || ($acao == "EXCLUIR"))
		{
			$endereco = new Endereco($cepUnidade, 
									 $logrUnidade, 
									 "", 
									 $numUnidade, 
									 $bairroUnidade, 
									 $municipioUnidade, 
									 $ufUnidade);

			$unidade = new Unidades($cnesUnidade, $nomeUnidade, $endereco);
			
			$unidadesDao = new UnidadesDAO();
			$res = 0;

			if ($acao == "")
			{
				$res = $unidadesDao->insereUnidade($unidade);
			}
			else if ($acao == "ALTERAR")
			{
				$res = $unidadesDao->alteraUnidade($unidade);
			}
			else if ($acao == "EXCLUIR")
			{
				$res = $unidadesDao->excluiUnidade($cnesUnidade);
			}

			if ($res == 1)
			{
				$msg_erro = "Ação salva com sucesso!";
			}
			else if ($res == 2)
			{
				$msg_erro = "Erro ao alterar unidade!";
			}
			else
			{
				$msg_erro = "Ação não realizada!";
			}
		}
	}
	
	function ExibirUnidades()
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
		$tabelaEnt = $tabelaEnt . TH_I . "CNES Unidade" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Nome Unidade" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Endereço Unidade" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Editar" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Excluir" . TH_F;
		$tabelaEnt = $tabelaEnt . TR_F;
		
		$unidadesDao = new UnidadesDAO();
		$listaUnidades = $unidadesDao->listaUnidades();

		if (!empty($listaUnidades))
		{
			for ($p = 0 ; $p < count($listaUnidades) ; $p++)
			{
				$cnesUnidade = $listaUnidades[$p]->getCnesUnidade();
				$nomeUnidade = $listaUnidades[$p]->getNomeUnidade();

				$cepUnidade = $listaUnidades[$p]->getEndereco()->getCep();
				$logrUnidade = $listaUnidades[$p]->getEndereco()->getLogradouro();
				$numUnidade = $listaUnidades[$p]->getEndereco()->getNumero();
				$bairroUnidade = $listaUnidades[$p]->getEndereco()->getBairro();
				$municipioUnidade = $listaUnidades[$p]->getEndereco()->getMunicipio();
				$ufUnidade = $listaUnidades[$p]->getEndereco()->getUf();

				$endereco = "$logrUnidade, $numUnidade, $bairroUnidade, $municipioUnidade-$ufUnidade, CEP: $cepUnidade";

				$botaoEditar = "<button type='button' onclick=\"EditarUnidade('$cnesUnidade')\" style=\"padding: 0px;\">Editar</button>";
				$botaoExcluir = "<button type='button' onclick=\"ExcluirUnidade('$cnesUnidade')\" style=\"padding: 0px;\">Excluir</button>";

				$tabelaEnt = $tabelaEnt . TR_I;
				$tabelaEnt = $tabelaEnt . TD_I . $cnesUnidade . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeUnidade . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $endereco . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $botaoEditar . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $botaoExcluir . TD_F;
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
	
	function UfListaBrasil($uf)
	{
		$opcoes = '';

		$ufs = array('AC' => 'AC',
                     'AL' => 'AL',
                     'AP' => 'AP',
                     'AM' => 'AM',
                     'BA' => 'BA',
                     'CE' => 'CE',
                     'DF' => 'DF',
                     'ES' => 'ES',
                     'GO' => 'GO',
                     'MA' => 'MA',
                     'MT' => 'MT',
                     'MS' => 'MS',
                     'MG' => 'MG',
                     'PA' => 'PA',
                     'PB' => 'PB',
                     'PR' => 'PR',
                     'PE' => 'PE',
                     'PI' => 'PI',
                     'RJ' => 'RJ',
                     'RN' => 'RN',
                     'RS' => 'RS',
                     'RO' => 'RO',
                     'RR' => 'RR',
                     'SC' => 'SC',
                     'SP' => 'SP',
                     'SE' => 'SE',
                     'TO' => 'TO');

		foreach ($ufs as $chave => $valor)
		{
			$sel = '';

			if ($uf == $chave)
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
		<title>Unidade</title>
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
				var cnes = document.getElementById("cnesUnidade").value;
				
				if (acao == "ALTERAR")
				{
					var res = confirm("Deseja Alterar esta Unidade (" + cnes + ")?");

					if (res == true)
					{
						document.getElementById("cnesUnidade").disabled = false;
						document.frm_cad_op.submit();
					}
				}
				else if (acao == "")
				{
					document.frm_cad_op.submit();
				}
			}

			function EditarUnidade(cnes)
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
									if (dadosRes[i]['unidade'])
									{
										let cnesUnidade = dadosRes[i]['unidade'].cnesUnidade;
										let nomeUnidade = dadosRes[i]['unidade'].nomeUnidade;
										let cepUnidade = dadosRes[i]['unidade'].cepUnidade;
										let logrUnidade = dadosRes[i]['unidade'].logrUnidade;
										let numUnidade = dadosRes[i]['unidade'].numUnidade;
										let bairroUnidade = dadosRes[i]['unidade'].bairroUnidade;
										let municipioUnidade = dadosRes[i]['unidade'].municipioUnidade;
										let ufUnidade = dadosRes[i]['unidade'].ufUnidade;

										document.getElementById("cnesUnidade").value = cnesUnidade;
										document.getElementById("nomeUnidade").value = nomeUnidade;
										document.getElementById("cepUnidade").value = cepUnidade;
										document.getElementById("logrUnidade").value = logrUnidade;
										document.getElementById("numUnidade").value = numUnidade;
										document.getElementById("bairroUnidade").value = bairroUnidade;
										document.getElementById("municipioUnidade").value = municipioUnidade;
										document.getElementById("selUf").value = ufUnidade;
										
										document.getElementById("cnesUnidade").disabled = true;
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
					xhttp.send("cnes=" + cnes + "&funcao=3" + "&sss=" + SSS);
				}
				catch(erro)
				{
					console.log(erro.message);
				}
			}

			function ExcluirUnidade(cnes)
			{
				var res = confirm("Deseja Excluir esta Unidade (" + cnes + ")?");

				if (res == true)
				{
					document.getElementById("cnesUnidade").value = cnes;
					
					document.getElementById("acao").value = "EXCLUIR";
					document.getElementById("cnesUnidade").disabled = false;
					document.frm_cad_op.submit();
				}
				else
				{
					document.getElementById("cnesUnidade").value = "";
					
					document.getElementById("acao").value = "";
					document.getElementById("cnesUnidade").disabled = false;
				}
			}

			function Limpar()
			{
				document.getElementById("cnesUnidade").value = "";
				document.getElementById("nomeUnidade").value = "";
				document.getElementById("cepUnidade").value = "";
				document.getElementById("logrUnidade").value = "";
				document.getElementById("numUnidade").value = "";
				document.getElementById("bairroUnidade").value = "";
				document.getElementById("municipioUnidade").value = "";
				document.getElementById("selUf").value = "selecione";
				
				document.getElementById("cnesUnidade").disabled = false;
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
				<h2>Cadastro de Unidade</h2>
			</div>
			<form name="frm_cad_op" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<fieldset>
					<legend>Dados da Unidade</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
                            <div style="width:20%;">
								<input type="text" id="cnesUnidade" name="cnesUnidade" maxlength="7" placeholder="CNES da Unidade" onkeyup="Numerico(this)" onkeydown="Numerico(this)" value="<?php if (isset($cnesUnidade)){echo $cnesUnidade;} ?>" />
							</div>
							<div style="width:50%;">
								<input type="text" id="nomeUnidade" name="nomeUnidade" maxlength="100" placeholder="Nome da Unidade" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($nomeUnidade)){echo $nomeUnidade;} ?>" />
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset>
					<legend>Endereço da Unidade</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
                            <div style="width:20%;">
								<input type="text" id="cepUnidade" name="cepUnidade" maxlength="8" placeholder="Cep Unidade" onkeyup="Numerico(this)" onkeydown="Numerico(this)" value="<?php if (isset($cepUnidade)){echo $cepUnidade;}?>"/>
							</div>
							<div style="width:50%;">
                                <input type="text" id="logrUnidade" name="logrUnidade" maxlength="100" placeholder="Logradouro da Unidade" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($logrUnidade)){echo $logrUnidade;} ?>" />
							</div>
							<div style="width:20%;">
                                <input type="text" id="numUnidade" name="numUnidade" maxlength="5" placeholder="Número da Unidade" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($numUnidade)){echo $numUnidade;} ?>" />
							</div>
                            <div style="width:40%;">
                                <input type="text" id="bairroUnidade" name="bairroUnidade" maxlength="50" placeholder="Bairro da Unidade" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($bairroUnidade)){echo $bairroUnidade;} ?>" />
							</div>
                            <div style="width:40%;">
                                <input type="text" id="municipioUnidade" name="municipioUnidade" maxlength="50" placeholder="Município da Unidade" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($municipioUnidade)){echo $municipioUnidade;} ?>" />
							</div>
                            <div style="width:10%;">
                                <select id="selUf" name="selUf">
                                    <option value="selecione">UF</option>
                                    <?php
										if (!isset($ufUnidade))
										{
											$ufUnidade = '';
										}

										echo UfListaBrasil($ufUnidade);
									?>
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
			echo "<center>" . ExibirUnidades() . "</center>";
		?>

		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>