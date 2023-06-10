<?php
	require_once 'valida_sessao.php';
	require_once 'configuracoes/configuracoes.php';

	require_once 'objetos/Profissionais.php';
	require_once 'objetosDAO/ProfissionaisDAO.php';

	function PegaDadosProfissionalLogado($cnsOp)
	{
		$dadosOperador = "";
				
		$profissionaisDao =  new ProfissionaisDAO();
		$profissional = $profissionaisDao->selecionaProfissional($cnsOp);

		$dadosOperador = $dadosOperador . "<br><center><label class='labelaviso'>" . $profissional->getNomePessoa() . ' (' . $profissional->getCnsPessoa() . ")<br>";
		$dadosOperador = $dadosOperador . $profissional->getPerfilProf() . "</label></center><br>";

		return $dadosOperador;
	}
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>DAE - OLINDA</title>
		<link rel="icon" href="imagens/favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon" />
		<meta name="viewport" content="device-width, initial-scale=1"/>
		<link rel="stylesheet" href="css/estilo_fv.css?v=<?php echo VERSAO;?>"/>
		<meta charset="utf-8"/>
	</head>
	<body onresize="TamanhoIframe()" onload="TamanhoIframe()">
		
		<div class="container_menu">
			<img src="imagens/LogoSPA.png" alt="DAE - CHAMADOS"/>
			
			<ul class="menu">
				<li><a href="#">Acesso</a>
					<ul class="submenu">
						<li><a href="">Início</a></li>
						<li><a href='alterar_senha.php' target='pagina'>Alterar Senha</a></li>
					</ul>
				</li>
				<?php 
					if($_SESSION["PERFIL"] == "ADMINISTRADOR")
					{
						echo "<li><a href=\"#\">Cadastrar</a>";
						echo "<ul class=\"submenu\">";
						echo "<li><a href='cadastrar_entrada.php' target='pagina'>Cadastrar Entrada</a></li>";
						echo "<li><a href='cadastrar_profissionais.php' target='pagina'>Cadastro de Profissionais</a></li>";
						echo "<li><a href='cadastrar_unidade_exe.php' target='pagina'>Cadastro de Unidade</a></li>";
						echo "</ul>";
						echo "</li>";
					}
				?>
				<li><a href="#">Consultar</a>
					<ul class="submenu">
						<?php
							if ($_SESSION["PERFIL"] == "EXECUTANTE")
							{
								echo "<li><a href='consultar_agenda.php' target='pagina'>Atendimento</a></li>";
							}
						?>
						<li class="Saida"><a href="chamado_recepcao.php" target='blank'>Chamado Recepção</a></li>
					</ul>
				</li>
				<li class="Saida"><a href="logout.php">Sair</a></li>
			</ul>
			
			<?php
				$cnsOp = $_SESSION["CNS"];
				if (isset($cnsOp))
				{
					echo PegaDadosProfissionalLogado($cnsOp);
				}
			?>
		</div>
		
		<div class="container_frame">
			<iframe name="pagina" id="pagina" title="pagina" src="atend_spa_inicio.php"></iframe>
		</div>
		
		<footer>
			<span>&copy;<?php echo date("Y");?> - Sistema CHAMADO - DAE</span>
		</footer>
		
		<script src="javascripts/app.js?v=<?php echo VERSAO;?>"></script>
	</body>
</html>