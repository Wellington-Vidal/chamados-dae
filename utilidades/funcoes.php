<?php
    function ListarClassificacoes($risco)
	{
		$classificacoes = array('VERMELHO (Emergência)', 
								'AMARELO (Urgência)', 
								'VERDE (Não Urgente)', 
								'AZUL (Eletivo)');
		$opcoes = '';

		for ($i = 0 ; $i < count($classificacoes) ; $i++)
		{
			$sel = '';

			if ($risco == "P$i")
			{
				$sel = ' selected';
			}

			$opcoes = $opcoes . "<option value='P$i'$sel>" . $classificacoes[$i] . '</option>';
		}

		return $opcoes;
	}

    function FiltraDados($valor)
	{
		$valor = trim($valor);
		$valor = stripslashes($valor);
		$valor = htmlspecialchars($valor);
		$valor = str_replace("'", "", $valor);
		$valor = str_replace(";", "", $valor);
		$valor = str_replace("=", "", $valor);
		$valor = str_replace("Drop ", "", $valor);
		$valor = str_replace("Table ", "", $valor);
		$valor = str_replace("Select ", "", $valor);
		$valor = str_replace(" From ", "", $valor);
		$valor = str_replace("\"", "", $valor);
		$valor = str_replace(" * ", "", $valor);
		$valor = str_replace("|", "", $valor);
		return $valor;
	}

    function ExibirEntradas1($alta)
	{
		define('TR_I', '<tr>');
		define('TR_F', '</tr>');
		define('TH_I', '<th>');
		define('TH_F', '</th>');
		define('TD_I', '<td>');
		define('TD_F', '</td>');

		$situacoes = array('Triagem' => 'Triagem',
						   'Retorno' => 'Retorno', 
						   'Transferido' => 'Transferido', 
						   'Abandonou' => 'Abandonou', 
						   'Medicacao' => 'Medicação', 
						   'Observacao' => 'Observação', 
						   'Erro' => 'Erro',
						   'Obito' => 'Óbito');

		$corRisco = array('P0' => '#DD0000', 
						  'P1' => '#FFEE61', 
						  'P2' => '#03FE6E', 
						  'P3' => '#0193FF');

		$selectSituacao = "<select id='tabSelSituacao' onchange=\"CarregaEntradasStatus(this)\" style=\"padding: 2px; text-align:center;\">";
		$selectSituacao = $selectSituacao . "<option value='0'>Ativos</option>";
		$selectSituacao = $selectSituacao . "<option value='1'>Inativos</option>";
		$selectSituacao = $selectSituacao . "</select>";

		$tabelaEnt = "<div style=\"height:auto; width:100%; overflow: auto;\">\n";
		$tabelaEnt = $tabelaEnt . "<table border='1' id='tabEntradas'>";
		$tabelaEnt = $tabelaEnt . TR_I;
		$tabelaEnt = $tabelaEnt . TH_I . "Nº" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Risco" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Data/Hora" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Usuário" . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Idade" . TH_F;
        $tabelaEnt = $tabelaEnt . TH_I . $selectSituacao . TH_F;
		$tabelaEnt = $tabelaEnt . TH_I . "Editar" . TH_F;
		$tabelaEnt = $tabelaEnt . TR_F;
		
		$entradasDao = new EntradasDAO();
		$listaEntradas = $entradasDao->listarEntradas($alta);

		if (!empty($listaEntradas))
		{
			for ($e = 0 ; $e < count($listaEntradas) ; $e++)
			{
				$idEnt = $listaEntradas[$e]->getIdEnt();
				$risco = $listaEntradas[$e]->getClassificacao();
				$dataHoraEnt = $listaEntradas[$e]->getDataHoraEnt();
				$nomeUsuario = $listaEntradas[$e]->getUsuario()->getNomePessoa();
				$cnsUsuario = $listaEntradas[$e]->getUsuario()->getCnsPessoa();
				$dataNasc = $listaEntradas[$e]->getUsuario()->getDataNasc();
				$situacao = $listaEntradas[$e]->getSituacao();

				if (array_key_exists($situacao, $situacoes))
				{
					$situacao = $situacoes[$situacao];
				}

				$sitProibidasEdicao = array('Abandonou', 'Transferido', 'Erro');

                if (!in_array($situacao, $sitProibidasEdicao))
                {
                    $botaoEditar = "<button type='button' onclick=\"EditarEntrada('$idEnt')\" style=\"padding: 0px;\">Editar</button>";
                }
                else
                {
                    $botaoEditar = '---';
                }

				$arraydataNasc = explode('/', $dataNasc);
				$data1 = date_create($arraydataNasc[2] . '-' . $arraydataNasc[1] . '-' . $arraydataNasc[0]);
				$data2 = date_create(date('Y-m-d'));
				$diff = date_diff($data1, $data2);
				$idade = $diff->format("%y ano(s)");

				if (array_key_exists($risco, $corRisco))
				{
					$cor = $corRisco[$risco];
					$tdCorRisco = "<td style=\"background-color: $cor;\">";	
				}
				else
				{
					$tdCorRisco = TD_I;
				}

				$tabelaEnt = $tabelaEnt . TR_I;
				$tabelaEnt = $tabelaEnt . TD_I . $idEnt . TD_F;
				$tabelaEnt = $tabelaEnt . $tdCorRisco . $risco  . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dataHoraEnt . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $nomeUsuario . " ($cnsUsuario)" . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $dataNasc . " ($idade)" . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $situacao . TD_F;
				$tabelaEnt = $tabelaEnt . TD_I . $botaoEditar . TD_F;
				$tabelaEnt = $tabelaEnt . TR_F;
			}
		}
		else
		{
			$tabelaEnt = $tabelaEnt . TR_I;
			$tabelaEnt = $tabelaEnt . "<td colspan='7'>" . "Sem Registros" . TD_F;
			$tabelaEnt = $tabelaEnt . TR_F;
		}
		
		return $tabelaEnt;
	}

	function ValidaCNES($cnes)
	{
		$valido = true;

		if ((strlen($cnes) != 7) || (!is_numeric($cnes)))
		{
			$valido = false;
		}

		return $valido;
	}
?>