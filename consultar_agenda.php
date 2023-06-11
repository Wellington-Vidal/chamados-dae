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
	
	$msg_erro = "";

	$senha = "SPA" . date("d/m/Y");
	$cripto = md5($senha);
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$acao = $_POST["acao"];

		if (empty($_POST["idEntrada"]))
		{
			$msg_erro = "Clique no botão Concluir da entrada!";
		}
		else
		{
			$idEntrada = FiltraDados($_POST["idEntrada"]);
		}
		
		if (($msg_erro == "") || ($acao == "EXCLUIR"))
		{
			date_default_timezone_set('America/Sao_Paulo');
			$dataSaida = date("d/m/Y");
			$horaSaida = date("H:i");

			$entrada = new Entradas();
			$entrada->setIdEnt($idEntrada);
			$entrada->setDataSaida($dataSaida);
			$entrada->setHoraSaida($horaSaida);

			$entradasDao = new EntradasDAO();

			$res = 0;

			if ($acao == "")
			{
				//SEM AÇÃO
			}
			else if ($acao == "ALTERAR")
			{
				$res = $entradasDao->alteraEntrada($entrada);
			}
			else if ($acao == "EXCLUIR")
			{
				//SEM AÇÃO
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
	
	function ExibirEntradas($dataEntrada, $cnsProf)
	{
		define('TR_I', '<tr>');
		define('TR_F', '</tr>');
		define('TH_I', '<th>');
		define('TH_F', '</th>');
		define('TD_I', '<td>');
		define('TD_F', '</td>');

		$txtDataEntrada = "<input type='text' id='txtDataEntFiltro' onkeydown=\"FormataData(this)\" onkeyup=\"FormataData(this)\" onkeypress=\"ListarEntradasData(this.value, '$cnsProf')\" value=\"$dataEntrada\" placeholder='Data Entrada' maxlength='10' style=\"width: 150px; margin: auto; text-align: center;\">";
		$txtNomeUsuarioFiltro = "<input type='text' id='txtNomeUsuarioFiltro' onkeyup=\"FiltraTabelaEntradas()\" placeholder='Usuário' style=\"text-align: center;\">";

		$tabelaEnt = "<div style=\"height:auto; width:100%; overflow: auto;\">\n";
		$tabelaEnt = $tabelaEnt . "<table id='tab-ent'>";
		$tabelaEnt = $tabelaEnt . TR_I;
		$tabelaEnt = $tabelaEnt . TH_I . "Nº" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . $txtDataEntrada . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Data\Hora Saída" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Profissional" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . $txtNomeUsuarioFiltro . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Concluir" . TH_F;
		$tabelaEnt = $tabelaEnt . TR_F;
		
		$entradasDao = new EntradasDAO();
		$listaEntradas = $entradasDao->listarEntradas($dataEntrada, $cnsProf);

		if (!empty($listaEntradas))
		{
			for ($p = 0 ; $p < count($listaEntradas) ; $p++)
			{
				$idEntrada = $listaEntradas[$p]->getIdEnt();
				$dataEntrada = $listaEntradas[$p]->getDataEntrada();
				$horaEntrada = $listaEntradas[$p]->getHoraEntrada();
				$dataHoraEntrada = "$dataEntrada - $horaEntrada";

				$dataSaida = $listaEntradas[$p]->getDataSaida();
				$horaSaida = $listaEntradas[$p]->getHoraSaida();
				$dataHoraSaida = "$dataSaida - $horaSaida";

				$botaoConcluir = "<button type='button' onclick=\"ConcluirAtendimento('$idEntrada')\" style=\"padding: 0px;\">Concluir</button>";
				$botaoDeclaracao = "<button type='button' onclick=\"GerarDeclaracao('$idEntrada')\" style=\"padding: 0px;\">Declaração</button>";
				$botaoAtivo = $botaoDeclaracao;

				if ($dataSaida == "")
				{
					$dataHoraSaida = "---";
					$botaoAtivo = $botaoConcluir;
				}

				$nomeProf = $listaEntradas[$p]->getProfissionalExec()->getNomePessoa();
				$nomeUsuario = $listaEntradas[$p]->getUsuario()->getNomePessoa();
				$cnsUsuario = $listaEntradas[$p]->getUsuario()->getCnsPessoa();
				$dadosUsuario = "$nomeUsuario ($cnsUsuario)";

				$tabelaEnt = $tabelaEnt . TR_I;
				$tabelaEnt = $tabelaEnt . TD_I . ($p + 1) . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dataHoraEntrada . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dataHoraSaida . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeProf . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dadosUsuario .  TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $botaoAtivo . TD_F;
				$tabelaEnt = $tabelaEnt . TR_F;
			}
		}
		else
		{
			$tabelaEnt = $tabelaEnt . TR_I;
			$tabelaEnt = $tabelaEnt . "<td colspan='6'>" . "Sem Registros" . TD_F;
			$tabelaEnt = $tabelaEnt . TR_F;
		}
		
		return $tabelaEnt;
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Atendimento</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<meta charset="utf-8">
	</head>
		
	<body>
		<script>
			const SSS = <?php echo "'$cripto'"; ?>;

			function ListarEntradasData(dataEnt, cns)
			{
				try
				{
					if (!VerificaData(dataEnt))
					{
						return;
					}
					if (!validaCNS(cns))
					{
						return;
					}

					var xhttp;
					xhttp = new XMLHttpRequest();

					xhttp.onreadystatechange = function(){
						if (this.readyState == 4 && this.status == 200)
						{
							//alert(this.responseText);
							let dadosRes = JSON.parse(this.responseText);
							//alert(JSON.stringify(dadosRes));
							//alert(dadosRes['entradas'].length);

							let tabela = document.getElementById('tab-ent');

							if (dadosRes['entradas'].length > 0)
							{								
								RemoveLinhasTabela('tab-ent');

								for (let i = 0 ; i < dadosRes['entradas'].length ; i++)
								{
									if (dadosRes['entradas'][i])
									{					
										let idEntrada = dadosRes['entradas'][i].idEnt;
										let nomeUsuario = dadosRes['entradas'][i].nomeUsuario;
										let cnsUsuario = dadosRes['entradas'][i].cnsUsuario;
										let nomeProf = dadosRes['entradas'][i].nomeProfissionalExec;
										let dataEntrada = dadosRes['entradas'][i].dataEntrada;
										let horaEntrada = dadosRes['entradas'][i].horaEntrada;
										let dataSaida = dadosRes['entradas'][i].dataSaida;
										let horaSaida = dadosRes['entradas'][i].horaSaida;
										
										let linha = document.createElement('tr');

										let col1 = document.createElement('td');
										col1.innerHTML = (i + 1);					

										let col2 = document.createElement('td');
										col2.innerHTML = dataEntrada + " - " + horaEntrada;

										let col3 = document.createElement('td');
										col3.innerHTML = dataSaida + "-" + horaSaida;

										if (dataSaida == null)
										{
											col3.innerHTML = "---";
										}

										let col4 = document.createElement('td');
										col4.innerHTML = nomeProf;

										let col5 = document.createElement('td');
										col5.innerHTML = nomeUsuario + ' (' + cnsUsuario + ')';

										let hoje = new Date();
										let dia = hoje.getDate() < 10 ? '0' + hoje.getDate() : hoje.getDate();
										let mes = hoje.getMonth() + 1 ? '0' + (hoje.getMonth()*1 + 1) : (hoje.getMonth()*1 + 1);
										let ano = hoje.getFullYear();
										let dataFormatada = dia + '/' + mes + '/' + ano;

										let btnConcluir = document.createElement('button');
										btnConcluir.innerHTML = "Concluir";
										btnConcluir.style.padding = "0px";
										btnConcluir.addEventListener("click", function(){ConcluirAtendimento(idEntrada);});

										let btnDeclaracao = document.createElement('button');
										btnDeclaracao.innerHTML = "Declaração";
										btnDeclaracao.style.padding = "0px";
										btnDeclaracao.addEventListener("click", function(){GerarDeclaracao(idEntrada);});

										let col6 = document.createElement('td');

										if (dataEnt != dataFormatada)
										{
											col6.innerHTML = "---";
										}
										else
										{
											if (dataSaida == null)
											{
												col6.appendChild(btnConcluir);
											}
											else
											{
												col6.appendChild(btnDeclaracao);
											}
										}										

										linha.appendChild(col1);
										linha.appendChild(col2);
										linha.appendChild(col3);
										linha.appendChild(col4);
										linha.appendChild(col5);
										linha.appendChild(col6);

										tabela.appendChild(linha);
									}
								}
							}
							else
							{
								RemoveLinhasTabela('tab-ent');

								let linha = document.createElement('tr');
								let col1 = document.createElement('td');
								col1.innerHTML = "Sem Registros";
								col1.setAttribute("colspan", 6);

								linha.appendChild(col1);
								tabela.appendChild(linha);
							}
						}
					};
					
					xhttp.open("POST", "res_ajax.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("data=" + dataEnt + "&cns=" + cns + "&funcao=6" + "&sss=" + SSS);
				}
				catch(erro)
				{
					console.log(erro.message);
				}
			}

			function ConcluirAtendimento(idEntrada)
			{
				var res = confirm("Deseja Concluir este Atendimento?");

				if (res == true)
				{
					document.getElementById("acao").value = "ALTERAR";
					document.getElementById("idEntrada").value = idEntrada;
					document.frm_cad_op.submit();
				}
				else
				{
					document.getElementById("acao").value = "";
					document.getElementById("idEntrada").value = "";
				}
			}

			function GerarDeclaracao(idEntrada)
			{
				try
				{
					let docUsuario = prompt("Digite o documento do Usuário. Ex: Rg, CPF", "CPF/RG: ");

					if ((docUsuario == null) || (docUsuario == "") || (docUsuario == "CPF/RG: "))
					{
						return;
					}

					var xhttp;
					xhttp = new XMLHttpRequest();

					xhttp.onreadystatechange = function(){
						if (this.readyState == 4 && this.status == 200)
						{
							//alert(this.responseText);
							let dadosRes = JSON.parse(this.responseText);
							//alert(JSON.stringify(dadosRes));

							if ((dadosRes['entrada']) && (dadosRes['unidade']))
							{
								let cnsUsuario = dadosRes['entrada'].cnsUsuario;
								let nomeUsuario = dadosRes['entrada'].nomeUsuario;
								let nomeProf = dadosRes['entrada'].nomeProfissionalExec;
								let dataEntrada = dadosRes['entrada'].dataEntrada;
								let horaEntrada = dadosRes['entrada'].horaEntrada;
								let dataSaida = dadosRes['entrada'].dataSaida;
								let horaSaida = dadosRes['entrada'].horaSaida;

								let nomeUnidade = dadosRes['unidade'].nomeUnidade;
								let cnesUnidade = dadosRes['unidade'].cnesUnidade;
								let logrUnidade = dadosRes['unidade'].logrUnidade;
								let cepUnidade = dadosRes['unidade'].cepUnidade;
								let numUnidade = dadosRes['unidade'].numUnidade;
								let bairroUnidade = dadosRes['unidade'].bairroUnidade;
								let municipioUnidade = dadosRes['unidade'].municipioUnidade;
								let ufUnidade = dadosRes['unidade'].ufUnidade;

								let objEntrada = {'nomeUsuario' : nomeUsuario,
												  'dataEntrada' : dataEntrada,
												  'horaEntrada' : horaEntrada,
												  'nomeProf' : nomeProf, 
												  'dataSaida' : dataSaida, 
												  'horaSaida' : horaSaida, 
												  'docUsuario' : docUsuario, 
												  'nomeUnidade' : nomeUnidade, 
												  'cnesUnidade' : cnesUnidade, 
												  'logrUnidade': logrUnidade, 
												  'cepUnidade': cepUnidade, 
												  'numUnidade': numUnidade, 
												  'bairroUnidade': bairroUnidade, 
												  'municipioUnidade': municipioUnidade, 
												  'ufUnidade': ufUnidade};

								CriarJanela(objEntrada);
							}
						}
					};
					
					xhttp.open("POST", "res_ajax.php", true);
					xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xhttp.send("id=" + idEntrada + "&funcao=7" + "&sss=" + SSS);
				}
				catch(erro)
				{
					console.log(erro.message);
				}
			}

			function CriarJanela(objEntrada)
			{
				var w = window.innerWidth
					|| document.documentElement.clientWidth
					|| document.body.clientWidth;
			
				var h = window.innerHeight
					|| document.documentElement.clientHeight
					|| document.body.clientHeight;

				if (w > 700)
				{
					w = 700;
				}

				var janela = window.open('', '', 'width=' + w + ', height=' + h);

				try
				{
					janela.document.write("<!DOCTYPE html>");
					janela.document.write("<html lang='pt-br'>");
					janela.document.write("<head>");
					janela.document.write("<meta charset='Content-Type: text/html); charset=utf-8'>");
					janela.document.write("<meta http-equiv='X-UA-Compatible' content='IE=edge'>");
					janela.document.write("<meta name='viewport' content='width=device-width, initial-scale=1'>");
					janela.document.write("<title>Declaração</title>");
					
					janela.document.write("</head>");
					janela.document.write("<body>");

					janela.document.write("<div><h4 style=\"text-align: center; margin-bottom: 0px;\">SECRETARIA DE SAÚDE DE " + objEntrada.municipioUnidade + "</h4></div>");
					janela.document.write("<div><h5 style=\"text-align: center; margin-top: 0px;\">" + objEntrada.nomeUnidade + "</h5></div>");
					janela.document.write("<div><h4 style=\"text-align: center; margin-top: 50px;\">DECLARAÇÃO DE COMPARECIMENTO</h4></div>");

					let textoDeclaracao = "Declaro para os devidos fins que NOME_PACIENTE, documento: NUM_DOCUMENTO esteve neste estabelecimento NOME_UNIDADE (CNES_UNIDADE) no período de HORA_INICIAL as HORA_FINAL do dia DATA_ENTRADA para atendimento.";
					textoDeclaracao = textoDeclaracao.replace('NOME_PACIENTE', objEntrada.nomeUsuario);
					textoDeclaracao = textoDeclaracao.replace('NUM_DOCUMENTO', objEntrada.docUsuario);
					textoDeclaracao = textoDeclaracao.replace('NOME_UNIDADE', objEntrada.nomeUnidade);
					textoDeclaracao = textoDeclaracao.replace('CNES_UNIDADE', objEntrada.cnesUnidade);
					textoDeclaracao = textoDeclaracao.replace('DATA_ENTRADA', objEntrada.dataEntrada);
					textoDeclaracao = textoDeclaracao.replace('HORA_INICIAL', objEntrada.horaEntrada);
					textoDeclaracao = textoDeclaracao.replace('HORA_FINAL', objEntrada.horaSaida);

					janela.document.write("<div style=\"padding: 10px; margin-top: 20px; text-align: justify; text-indent: 50px; letter-spacing: 3px;\"><p>" + textoDeclaracao + "</p></div>");

					let assinatura = "_".repeat(objEntrada.nomeProf.length);

					janela.document.write("<div style=\"margin-top: 100px; text-align: center;\">" + assinatura + "</div>");
					janela.document.write("<div style=\"margin-top: 2px;text-align: center;\">" + objEntrada.nomeProf + "</div>");

					let nomeUnidade = objEntrada.nomeUnidade + ' (' + objEntrada.cnesUnidade + ')';
					let endereco = objEntrada.logrUnidade + ', ' + objEntrada.numUnidade + ', ' + objEntrada.bairroUnidade + ', ' + objEntrada.municipioUnidade + '-' + objEntrada.ufUnidade + ', CEP: ' + objEntrada.cepUnidade;
					endereco = endereco.toLowerCase();

					janela.document.write("<div style=\"padding: 10px; position: fixed; bottom: 0; left: 0; text-transform: capitalize;\">" + nomeUnidade + '<br>' + endereco + "</div>");

					janela.document.write("</body>");
					janela.document.write("</html>");

					janela.document.close();
				}
				catch (erro)
				{
					alert(erro.message);					
				}
			}

			function FiltraTabelaEntradas()
			{
				let txtNomeUsuarioFiltro = document.getElementById('txtNomeUsuarioFiltro');
				Maiusculo(txtNomeUsuarioFiltro);

				let tabela = document.getElementById('tab-ent');
				let linhas = tabela.getElementsByTagName('tr');
				let num = 1;
				
				for (let i = 1 ; i < linhas.length ; i++)
				{
					let colunas = linhas[i].getElementsByTagName('td');

					if (colunas)
					{
						let testeFiltroUsuario = ((colunas[4].innerHTML.indexOf(txtNomeUsuarioFiltro.value) != -1) || (txtNomeUsuarioFiltro.value == ''));

						if (testeFiltroUsuario)
						{
							linhas[i].style.display = '';
							colunas[0].innerHTML = num;
							num = num + 1;
						}
						else
						{
							linhas[i].style.display = 'none';
						}
					}
				}
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
				<h2>Atendimento</h2>
			</div>
			<form name="frm_cad_op" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
				<fieldset>
					<legend>Profissional Executante</legend>
					<div style="width: 100%;">
						<div style="width:auto;">
                            <div style="width:50%;">
                                <div>
                                    <?php
										echo $_SESSION["NOME"];
									?>
                                </div>     
							</div>
						</div>
					</div>
				</fieldset>
                
				<input type="hidden" id="acao" name="acao" value=""/>
				<input type="hidden" id="idEntrada" name="idEntrada" value=""/>
			</form>
			
			<?php 
				if ($msg_erro != "")
				{
					echo "<script>alert('$msg_erro');</script>";
				}
			?>
		</div><br>

		<?php
			date_default_timezone_set('America/Sao_Paulo');
			$dataHoje = date("d/m/Y");
            $cnsProf = $_SESSION["CNS"];
			
			echo "<center>" . ExibirEntradas($dataHoje, $cnsProf) . "</center>";
		?>

		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>