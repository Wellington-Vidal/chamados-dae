<?php
	require_once 'objetos/Profissionais.php';
	require_once 'objetos/Usuarios.php';
	require_once 'objetos/Entradas.php';
	require_once 'objetos/Endereco.php';
	require_once 'objetos/Unidades.php';

	require_once 'objetosDAO/ProfissionaisDAO.php';
	require_once 'objetosDAO/EntradasDAO.php';
	require_once 'objetosDAO/UsuariosDAO.php';
	require_once 'objetosDAO/EnderecosDAO.php';
	require_once 'objetosDAO/UnidadesDAO.php';

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		$senha = "SPA" . date("d/m/Y");
		$cripto = md5($senha);

		if (isset($_POST["sss"]) && ($_POST["sss"] == $cripto))
		{
			if ((isset($_POST["dados"])) && (($_POST["funcao"] == 1) || ($_POST["funcao"] == 2)))
			{
				$cnsCpf = LimparDados($_POST["dados"]);
				$tipo = LimparDados($_POST["funcao"]);
				$resConf = LocalizarUsuario($tipo, $cnsCpf);
				
				echo $resConf;
			}
			else if ((isset($_POST["cnes"])) && ($_POST["funcao"] == 3))
			{
				$cnes = LimparDados($_POST["cnes"]);
				$resConf = PegaDadosUnidadeExe($cnes);
				
				echo $resConf;
			}
			else if ((isset($_POST["data"])) && ($_POST["funcao"] == 4))
			{
				$dataEntrada = LimparDados($_POST["data"]);
				$resConf = PegaEntradasData($dataEntrada);
				
				echo $resConf;
			}
			else if ((isset($_POST["cns"])) && ($_POST["funcao"] == 5))
			{
				$cnsProf = LimparDados($_POST["cns"]);
				$resConf = PegaDadosProfissional($cnsProf);
				
				echo $resConf;
			}
		}
	}
	
	function LocalizarUsuario($tipo, $dados)
	{
		$dadosUsuario = array();

		$valido = true;

		if ($tipo == 1)
		{
			$valido = Pessoa::validaCNS($dados);
		}

		if ($valido)
		{
			$usuariosDao = new UsuariosDAO();
			$usuario = $usuariosDao->selecionaUsuario($dados);
	
			if (!empty($usuario))
			{
				$arrayUsuario = $usuario->geraArrayAtributos('');

				array_push($dadosUsuario, $arrayUsuario);
			}
		}
		
		return json_encode($dadosUsuario);
	}

	function PegaDadosUnidadeExe($cnes)
	{
		$dadosUnidade = array();

		$unidadesDao = new UnidadesDAO();
		$unidade = $unidadesDao->selecionaUnidade($cnes);

		array_push($dadosUnidade, ["unidade" => $unidade->geraArrayAtributos('')]);
		
		return json_encode($dadosUnidade);
	}

	function PegaEntradasData($data)
	{
		$listaEntradas = array("entradas" => []);

		$entradasDao = new EntradasDAO();
		$entradas = $entradasDao->listarEntradas($data);

		$arrayEntradas = array();

		for ($e = 0 ; $e < count($entradas) ; $e++)
		{
			array_push($arrayEntradas, $entradas[$e]->geraArrayAtributos(''));
		}

		$listaEntradas["entradas"] = $arrayEntradas;

		return json_encode($listaEntradas);
	}

	function LimparDados($dados)
	{
		$dados = trim($dados);
		$dados = stripslashes($dados);
		$dados = htmlspecialchars($dados);
		return $dados;
	}
	/*
	function ChamaPrimeiroDaFila($cnsProf)
	{
		define("ATENDIMENTO_MEDICO", "Atendimento Médico");
		
		date_default_timezone_set('America/Sao_Paulo');
		$dataHoraHoje = date("d/m/Y H:i:s");

		$dadosUsuario = array();

		$profissonalDao = new ProfissionaisDAO();
		$profissional = $profissonalDao->selecionaProfissional($cnsProf);
		
		$consultorioDao = new ConsultorioDAO();
		$consultorio = $consultorioDao->selecionaConsultorioProfissional($profissional);
		
		if ($consultorio->getIdSala() != null)
		{
			if ($consultorio->getEntrada()->getIdEnt() == null)
			{
				$entradasDao = new EntradasDAO();
				$entrada = $entradasDao->selecionaEntradaSeguinte($profissional->getPerfilProf()); //PEGA O PRIMEIRO DA FILA
				
				if ($entrada->getUsuario()->getCnsPessoa() != null)
				{
					if ($entrada->getSituacao() != 'Retorno')
					{
						//ATUALIZA A ENTRADA COM O INICIO DO ATENDIMENTO
						$entrada->setMedicoIniciou($profissional);
						$entrada->setDataHoraIAtend($dataHoraHoje);
						$entradasDao->alteraEntrada($entrada, 'INICIA_ATENDIMENTO');
					}

					//OCUPA O CONSULTÓRIO COM A ENTRADA
					$consultorio->setEntrada($entrada);
					$consultorioDao->vinculaUsuario($consultorio);
	
					$historiaClinicaDao = new HistoriaClinicaDAO();
	
					$historiaClinica = new HistoriaClinica($entrada->getUsuario(), 
														   $profissional, 
														   $dataHoraHoje, 
														   null, 
														   null, 
														   null, 
														   ATENDIMENTO_MEDICO);
	
					//ADICIONA NOVA HISTÓRIA CLÍNICA (Atendimento Médico)
					$res = $historiaClinicaDao->insereHistoriaClinica($historiaClinica);
	
					//LISTA HISTÓRIA CLÍNICA
					$listaHistoriaClinica = $historiaClinicaDao->listaHistoriaClinicaUsuario($entrada->getUsuario());
	
					array_push($dadosUsuario, ["RES" => array($res, 'MSG')]);
					array_push($dadosUsuario, ["usuario" => $entrada->getUsuario()->geraArrayAtributos('')]);
					array_push($dadosUsuario, ["entrada" => $entrada->geraArrayAtributos('')]);
					array_push($dadosUsuario, ["historia" => $listaHistoriaClinica]);
				}
			}
			else
			{
				$entradasDao = new EntradasDAO();
				$entrada = $entradasDao->selecionaEntrada($consultorio->getEntrada()->getIdEnt());
	
				$historiaClinicaDAO = new HistoriaClinicaDAO();
				$arrayHistoriaClinica = $historiaClinicaDAO->listaHistoriaClinicaUsuario($entrada->getUsuario());
	
				array_push($dadosUsuario, ["usuario" => $entrada->getUsuario()->geraArrayAtributos('')]);
				array_push($dadosUsuario, ["entrada" => $entrada->geraArrayAtributos('')]);
				array_push($dadosUsuario, ["historia" => $arrayHistoriaClinica]);
			}
		}

		return json_encode($dadosUsuario);
	}*/
	/*
	function ChamaPrimeiroDaFilaTriagem($cnsProf)
	{
		$dadosUsuario = array();

		$profissonalDao = new ProfissionaisDAO();
		$profissional = $profissonalDao->selecionaProfissional($cnsProf);
		
		$consultorioDao = new ConsultorioDAO();
		$consultorio = $consultorioDao->selecionaConsultorioProfissional($profissional);

		if (!empty($consultorio))
		{
			if ($consultorio->getEntrada()->getIdEnt() == null)
			{
				$entradasDao = new EntradasDAO();
				$entrada = $entradasDao->selecionaEntradaSeguinte($profissional->getPerfilProf()); //PEGA O PRIMEIRO DA FILA

				if ($entrada->getUsuario()->getCnsPessoa() != null)
				{
					//OCUPA O CONSULTÓRIO COM A ENTRADA
					$consultorio->setEntrada($entrada);
					$consultorioDao->vinculaUsuario($consultorio);

					$arrayEndereco = array("endereco" => $entrada->getUsuario()->getEndereco()->geraArrayAtributos(''));
					$arrayUsuario = array_merge($entrada->getUsuario()->geraArrayAtributos(''), $arrayEndereco);
					
					array_push($dadosUsuario, ["usuario" => $arrayUsuario]);
					array_push($dadosUsuario, ["entrada" => $entrada->geraArrayAtributos('')]);
				}
			}
			else
			{
				$entradasDao = new EntradasDAO();
				$entrada = $entradasDao->selecionaEntrada($consultorio->getEntrada()->getIdEnt());

				$arrayEndereco = array("endereco" => $entrada->getUsuario()->getEndereco()->geraArrayAtributos(''));
				$arrayUsuario = array_merge($entrada->getUsuario()->geraArrayAtributos(''), $arrayEndereco);
				
				array_push($dadosUsuario, ["usuario" => $arrayUsuario]);
				array_push($dadosUsuario, ["entrada" => $entrada->geraArrayAtributos('')]);
			}
		}

		return json_encode($dadosUsuario);
	}*/

	function PegaDadosDaEntrada($idEnt)
	{
		$entradasDao = new EntradasDAO();
		$entrada = $entradasDao->selecionaEntrada($idEnt);

		$arrayEntrada = array();

		if (!empty($entrada))
		{
			$usuariosDao = new UsuariosDAO();
			$usuario = $usuariosDao->selecionaUsuario($entrada->getUsuario()->getCnsPessoa());
			$entrada->setUsuario($usuario);

			$arrayDadosUsuario = $usuario->geraArrayAtributos('');
			//$arrayDadosUsuario = array_merge($usuario->geraArrayAtributos(''), $usuario->getEndereco()->geraArrayAtributos(''));

			array_push($arrayEntrada, ['usuario' => $arrayDadosUsuario]);
		}

		array_push($arrayEntrada, ['entrada' => $entrada->geraArrayAtributos('')]);

		return json_encode($arrayEntrada);
	}
	/*
	function ExibePacientesVezConsultorio()
	{
		$arrayConsultorios = array();

		$consultorioDao = new ConsultorioDAO();
		$consultorios = $consultorioDao->listaConsultoriosOcupados();

		if (!empty($consultorios))
		{
			for ($c = 0 ; $c < count($consultorios) ; $c++)
			{
				$entrada = $consultorios[$c]->getEntrada();
				$arrayEntrada = $entrada->geraArrayAtributos('');

				$usuario = $consultorios[$c]->getEntrada()->getUsuario();
				$arrayUsuario = $usuario->geraArrayAtributos('');

				$arrayConsultorio = array_merge($consultorios[$c]->geraArrayAtributos(''), $arrayEntrada, $arrayUsuario);

				array_push($arrayConsultorios, ['consultorios' => $arrayConsultorio]);
			}
		}

		return json_encode($arrayConsultorios);
	}*/

	function PegaDadosProfissional($cnsProf)
	{
		$arrayProfissonal = array();
		
		$profissionaisDao = new ProfissionaisDAO();
		$profissional = $profissionaisDao->selecionaProfissional($cnsProf);

		if (!empty($profissional->getCnsPessoa()))
		{
			array_push($arrayProfissonal, ["profissional" => $profissional->geraArrayAtributos('')]);
		}

		return json_encode($arrayProfissonal);
	}
	/*
	function PegaDadosDoConsultorio($idSala)
	{
		$arrayConsultorio = array();

		$consultorioDao = new ConsultorioDAO();
		$consultorio = $consultorioDao->selecionaConsultorio($idSala);

		if ($consultorio->getIdSala() != null)
		{
			array_push($arrayConsultorio, ["consultorio" => $consultorio->geraArrayAtributos('')]);
		}

		return json_encode($arrayConsultorio);
	}*/
?>