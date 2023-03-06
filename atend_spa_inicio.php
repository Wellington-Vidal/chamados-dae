<?php
	require_once 'configuracoes/configuracoes.php';
	require_once 'objetos/Unidades.php';
	require_once 'objetosDAO/UnidadesDAO.php';

	$unidadesDao = new UnidadesDAO();
	$listaUnidades = $unidadesDao->listaUnidades();

	$nomeUnidade = "Unidade Não Cadastrada";

	if (!empty($listaUnidades))
	{
		for ($u = 0 ; $u < count($listaUnidades) ; $u++)
		{
			$nomeUnidade = $listaUnidades[$u]->getNomeUnidade();
		}
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<meta charset="utf-8">
		<title>DAE - OLINDA</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
		<meta charset="utf-8"/>
	</head>
		
	<body>
		<script>
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
				<h2><?php echo $nomeUnidade;?></h2>
			</div>
			
			<div>
				<table>
					<caption>Prefeitura Municipal de Olinda</caption>
					<tr>
						<th colspan='2'>Secretaria de Saúde de Olinda</th>
					</tr>
					<tr>
						<th>Aplicativo:</th>
						<td>CHAMADO - DAE</td>
					</tr>
					<tr>
						<th>Versão:</th>
						<td>1.0.0.0</td>
					</tr>
					<tr>
						<th>Atualizado em:</th>
						<td>23/02/2023</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>