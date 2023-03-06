<?php
	require_once 'valida_sessao.php';
	require_once 'configuracoes/configuracoes.php';
	require_once 'utilidades/funcoes.php';

	require_once 'objetos/Pessoa.php';
	require_once 'objetos/Profissionais.php';
	require_once 'objetos/Usuarios.php';
	require_once 'objetos/Entradas.php';

	require_once 'objetosDAO/ProfissionaisDAO.php';
	require_once 'objetosDAO/UsuariosDAO.php';
	require_once 'objetosDAO/EntradasDAO.php';
	
	header('Content-Type: text/html; charset=utf-8');

	$cnsProf = "";
	$horaChegada = "";
	$minutoChegada = "";
	$cnsUsuario = "";
	$nomeUsuario = "";

	$dataEntrada = "";
	$dataSaida = "";
	
	$msg_erro = "";

	$senha = "SPA" . date("d/m/Y");
	$cripto = md5($senha);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$acao = $_POST["acao"];
		
		if (empty($_POST["nomeUsuario"]))
		{
			$msg_erro = "Digite o nome do Usuário!";
		}
		else
		{
			$nomeUsuario = FiltraDados($_POST["nomeUsuario"]);
		}

		$usuario = new Usuarios();

		if (empty($_POST["cnsUsuario"]))
		{
			$msg_erro = "Digite o Cartão do SUS do Usuário!";
		}
		else
		{
			//Validar Cartão do SUS
			$cnsUsuario = FiltraDados($_POST["cnsUsuario"]);
			$usuarioDao = new UsuariosDAO();
			$usuario = $usuarioDao->selecionaUsuario($cnsUsuario);

			if (!Pessoa::validaCNS($cnsUsuario))
			{
				$msg_erro = "Digite um Cartão do SUS Válido!";
			}
			else if (!empty($nomeUsuario))
			{
				if ($usuario->getCnsPessoa() == null)
				{
					$usuario->setCnsPessoa($cnsUsuario);
					$usuario->setNomePessoa($nomeUsuario);
					$usuarioDao->insereUsuario($usuario);
				}
				else
				{
					$usuario->setNomePessoa($nomeUsuario);
					$usuarioDao->alteraUsuario($usuario);
				}
			}
		}
		
		if ((empty($_POST["minutoChegada"])) || (($_POST["minutoChegada"]) == "selecione"))
		{
			$msg_erro = "Selecione o tempo em minutos da hora atual que o usuário chegou!";
		}
		else
		{
			$minutoChegada = FiltraDados($_POST["minutoChegada"]);
		}

		if ((empty($_POST["horaChegada"])) || (($_POST["horaChegada"]) == "selecione"))
		{
			$msg_erro = "Selecione a hora que o usuário chegou!";
		}
		else
		{
			$horaChegada = FiltraDados($_POST["horaChegada"]);
		}
		
		$profissional = new Profissionais(null, null);

		if (empty($_POST["profExec"]) || (($_POST["profExec"]) == "selecione"))
		{
			$msg_erro = "Selecione o Profissional que aguarda o usuário!";
		}
		else
		{
			//Validar Cartão do SUS
			$cnsProf = FiltraDados($_POST["profExec"]);
			$profissionaisDao = new ProfissionaisDAO();
			$profissional = $profissionaisDao->selecionaProfissional($cnsProf);

			if ((!Pessoa::validaCNS($cnsProf)) && ($profissional == null))
			{
				$msg_erro = "Profissional não selecionado!";
			}
		}
		
		if (($msg_erro == "") || ($acao == "EXCLUIR"))
		{
			date_default_timezone_set('America/Sao_Paulo');
			$dataAgora = date("d/m/Y");
			$horaAgora = "$horaChegada:$minutoChegada";

			$entrada = new Entradas();
			$entrada->setProfissionalExec($profissional);
			$entrada->setUsuario($usuario);
			$entrada->setDataEntrada($dataAgora);
			$entrada->setHoraEntrada($horaAgora);

			$entradasDao = new EntradasDAO();

			$res = 0;

			if ($acao == "")
			{
				$res = $entradasDao->insereEntrada($entrada);
			}
			else if ($acao == "ALTERAR")
			{
				$res = $entradasDao->alteraEntrada($entrada);
			}
			else if ($acao == "EXCLUIR")
			{
				//FAZER
			}

			if ($res == 1)
			{
				$msg_erro = "Registro salvo com sucesso!";
			}
			else if ($res == 2)
			{
				$msg_erro = "Erro ao alterar registro!";
			}
			else
			{
				$msg_erro = "Ação não realizada!";
			}
		}
	}
	
	function ExibirEntradas($dataEntrada, $dataSaida)
	{
		date_default_timezone_set('America/Sao_Paulo');
		$dataHoje = date("d-m-Y");

		define('TR_I', '<tr>');
		define('TR_F', '</tr>');
		define('TH_I', '<th>');
		define('TH_F', '</th>');
		define('TD_I', '<td>');
		define('TD_F', '</td>');

		$txtDataEntrada = "<input type='date' pattern='dd-mm-yyyy' onchange=\"ExibirEntrada()\" value=\"$dataHoje\" style=\"width: 150px; margin: auto;\">";

		$tabelaEnt = "<div style=\"height:auto; width:100%; overflow: auto;\">\n";
		$tabelaEnt = $tabelaEnt . "<table border='1'>";
		$tabelaEnt = $tabelaEnt . TR_I;
		$tabelaEnt = $tabelaEnt . TH_I . $txtDataEntrada . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Data\Hora Saída" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Profissional" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Usuário" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Excluir" . TH_F;
		$tabelaEnt = $tabelaEnt . TR_F;
		
		$entradasDao = new EntradasDAO();
		$listaEntradas = $entradasDao->listarEntradas($dataEntrada, $dataSaida);

		if (!empty($listaEntradas))
		{
			for ($p = 0 ; $p < count($listaEntradas) ; $p++)
			{
				$idEntrada = $listaEntradas[$p]->getIdEnt();
				$dataEntrada = $listaEntradas[$p]->getDataEntrada();
				$horaEntrada = $listaEntradas[$p]->getHoraEntrada();
				$dataHoraEntrada = "$dataEntrada $horaEntrada";

				$dataSaida = $listaEntradas[$p]->getDataSaida();
				$horaSaida = $listaEntradas[$p]->getHoraSaida();
				$dataHoraSaida = "$dataSaida $horaSaida";

				if ($dataSaida == "")
				{
					$dataHoraSaida = "---";
				}

				$nomeProf = $listaEntradas[$p]->getProfissionalExec()->getNomePessoa();
				$nomeUsuario = $listaEntradas[$p]->getUsuario()->getNomePessoa();

				$botaoExcluir = "<button type='button' onclick=\"ExcluirEntrada('$idEntrada')\" style=\"padding: 0px;\">Editar</button>";

				$tabelaEnt = $tabelaEnt . TR_I;
				$tabelaEnt = $tabelaEnt . TD_I . $dataHoraEntrada . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dataHoraSaida . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeProf . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeUsuario .  TD_F;
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

    function ListarProfissionais($cns)
    {
        $opcoes = '';

        $profissionaisDao = new ProfissionaisDAO();
		$listaProfissionais = $profissionaisDao->listaProfissionais();

        if (!empty($listaProfissionais))
        {
            for ($p = 0 ; $p < count($listaProfissionais) ; $p++)
            {
                if ($listaProfissionais[$p]->getPerfilProf() == "EXECUTANTE")
                {
                    $nomeProf = $listaProfissionais[$p]->getNomePessoa();
                    $cnsProf = $listaProfissionais[$p]->getCnsPessoa();

                    $sel = '';

                    if ($cnsProf == $cns)
                    {
                        $sel = ' selected';
                    }
        
                    $opcoes = $opcoes . "<option value='$cnsProf'$sel>$nomeProf</option>";
                }
            }
        }

        return $opcoes;
    }

    function ListarHoras($horaChegada)
    {
        $opcoes = '';

        for ($h = 0 ; $h < 24 ; $h++)
        {
            $hora = $h < 10 ? '0' . $h : $h;

			$sel = '';

			if ($horaChegada == $hora)
			{
				$sel = ' selected';
			}

            $opcoes = $opcoes . "<option value='$hora'$sel>$hora</option>";
        }

        return $opcoes;
    }

    function ListarMinutos($minutoChegada)
    {
        $opcoes = '';

        for ($m = 0 ; $m < 60 ; $m++)
        {
            $minuto = $m < 10 ? '0' . $m : $m;

			$sel = '';

			if ($minutoChegada == $minuto)
			{
				$sel = ' selected';
			}

            $opcoes = $opcoes . "<option value='$minuto'$sel>$minuto</option>";
        }

        return $opcoes;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Entradas</title>
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
				
				if (acao == "")
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
				document.getElementById("profExec").value = "selecione";
				document.getElementById("horaChegada").value = "selecione";
				document.getElementById("minutoChegada").value = "selecione";
				document.getElementById("nomeUsuario").value = "";
				document.getElementById("cnsUsuario").value = "";
				
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
				<h2>Cadastro de Entradas</h2>
			</div>
			<form name="frm_cad_op" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<fieldset>
					<legend>Dados da Entrada</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
                            <div style="width:50%;">
                                <select id='profExec' name='profExec'>
									<option value='selecione'>Profissional Executante</option>
									<?php
										if (!isset($cnsProf))
										{
											$cnsProf = '';
										}

										echo ListarProfissionais($cnsProf);
									?>
								</select>
							</div>
                            <div style="width:100%;">
                                <div style="width:10%; float: left; margin-right: 5px;">
                                    <select id='horaChegada' name='horaChegada'>
                                        <option value='selecione'>HH</option>
                                        <?php
											if (!isset($horaChegada))
											{
												$horaChegada = '';
											}

                                            echo ListarHoras($horaChegada);
                                        ?>
                                    </select>
                                </div>
                                <div style="width:10%; float: left;">
                                    <select id='minutoChegada' name='minutoChegada'>
                                        <option value='selecione'>MM</option>
                                        <?php
											if (!isset($minutoChegada))
											{
												$minutoChegada = '';
											}

                                            echo ListarMinutos($minutoChegada);
                                        ?>
                                    </select>
                                </div>
                            </div>
						</div>
					</div>
				</fieldset>

                <fieldset>
					<legend>Dados do Usuário</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
                            <div style="width:20%;">
								<input type="text" id="cnsUsuario" name="cnsUsuario" maxlength="15" placeholder="Cartão do SUS" onkeyup="Numerico(this)" onkeydown="Numerico(this)" value="<?php if (isset($cnsUsuario)){echo $cnsUsuario;}?>"/>
							</div>
							<div style="width:50%;">
								<input type="text" id="nomeUsuario" name="nomeUsuario" maxlength="100" placeholder="Nome do Usuário" onkeyup="Maiusculo(this)" onkeydown="Maiusculo(this)" value="<?php if (isset($nomeUsuario)){echo $nomeUsuario;} ?>" />
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
			echo "<center>" . ExibirEntradas($dataEntrada, $dataSaida) . "</center>";
		?>

		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>