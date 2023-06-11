<?php
    require_once 'objetosDAO/Conexao.php';
    require_once 'objetos/Entradas.php';
    require_once 'objetos/Usuarios.php';
    require_once 'objetos/Profissionais.php';
    require_once 'objetos/Endereco.php';

    class EntradasDAO
    {
        private $conexao;

        function __construct()
        {
            $this->conexao = new Conexao();
        }

        function __destruct()
        {
            $this->conexao = null;
        }

        function selecionaEntrada($idEntrada)
        {
            $entrada = new Entradas();

            $sql = "SELECT e.*, u.*, p.nome_prof
                    FROM tbl_entradas e, tbl_usuarios u, tbl_profissionais p
                    WHERE e.cns_usuario=u.cns_usuario
                    AND e.cns_prof=p.cns_prof
                    AND e.id_entrada=$idEntrada;";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $entrada->setIdEnt($linha['id_entrada']);
                    $entrada->setDataEntrada($linha['data_entrada']);
                    $entrada->setHoraEntrada($linha['hora_entrada']);

                    $usuario = new Usuarios();
                    $usuario->setCnsPessoa($linha['cns_usuario']);
                    $usuario->setNomePessoa($linha['nome_usuario']);

                    $entrada->setUsuario($usuario);
                    
                    $profissional = new Profissionais(null, null);
                    $profissional->setCnsPessoa($linha['cns_prof']);
                    $profissional->setNomePessoa($linha['nome_prof']);

                    $entrada->setProfissionalExec($profissional);

                    $entrada->setDataSaida($linha['data_saida']);
                    $entrada->setHoraSaida($linha['hora_saida']);
                }
            }
            
            return $entrada;
        }

        function listarEntradas($dataEntrada, $cnsProfissional)
        {
            $entradas = array();

            if ($cnsProfissional == null)
            {
                $sql = "SELECT e.*, u.nome_usuario, p.nome_prof
                        FROM tbl_entradas e, tbl_usuarios u, tbl_profissionais p
                        WHERE e.cns_usuario=u.cns_usuario 
                        AND e.cns_prof=p.cns_prof 
                        AND e.data_entrada='$dataEntrada'
                        ORDER BY e.id_entrada;";
            }
            else
            {
                $sql = "SELECT e.*, u.nome_usuario, p.nome_prof
                        FROM tbl_entradas e, tbl_usuarios u, tbl_profissionais p
                        WHERE e.cns_usuario=u.cns_usuario 
                        AND e.cns_prof=p.cns_prof 
                        AND e.cns_prof='$cnsProfissional'
                        AND e.data_entrada='$dataEntrada'
                        ORDER BY e.id_entrada;";
            }

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $entrada = new Entradas();

                    $entrada->setIdEnt($linha['id_entrada']);
                    $entrada->setDataEntrada($linha['data_entrada']);
                    $entrada->setHoraEntrada($linha['hora_entrada']);

                    $usuario = new Usuarios();
                    $usuario->setCnsPessoa($linha['cns_usuario']);
                    $usuario->setNomePessoa($linha['nome_usuario']);

                    $entrada->setUsuario($usuario);
                    
                    $profissional = new Profissionais(null, null);
                    $profissional->setCnsPessoa($linha['cns_prof']);
                    $profissional->setNomePessoa($linha['nome_prof']);

                    $entrada->setProfissionalExec($profissional);

                    $entrada->setDataSaida($linha['data_saida']);
                    $entrada->setHoraSaida($linha['hora_saida']);

                    array_push($entradas, $entrada);
                }
            }
            
            return $entradas;
        }

        function selecionaEntradaAtivaUsuario(Usuarios $usuarioPassado)
        {
            $entrada = new Entradas();

            $cnsUsuario = $usuarioPassado->getCnsPessoa();

            $sql = "SELECT e.*, u.nome_usuario, u.data_nasc, u.sexo, u.hipertenso, u.diabetico
                    FROM tbl_entradas e, tbl_usuarios u
                    WHERE e.cns_usuario=u.cns_usuario
                    AND e.cns_usuario='$cnsUsuario'
                    AND e.alta='0';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $entrada->setIdEnt($linha['id_ent']);

                    $usuarioRet = new Usuarios();
                    $usuarioRet->setCnsPessoa($linha['cns_usuario']);
                    $usuarioRet->setNomePessoa($linha['nome_usuario']);

                    $entrada->setUsuario($usuarioRet);

                    
                    $profissional = new Profissionais(null, null);
                    $profissional->setCnsPessoa($linha['cns_medico_i']);

                }
            }

            return $entrada;
        }

        function selecionaEntradaSeguinte($perfilProf)
        {
            $entrada = new Entradas();

            if ($perfilProf == 'MEDICO')
            {
                $sql = "SELECT e.*, u.nome_usuario, u.data_nasc, u.sexo, u.hipertenso, u.diabetico
                        FROM tbl_entradas e, tbl_usuarios u
                        WHERE e.cns_usuario=u.cns_usuario
                        AND e.alta='0'
                        AND e.situacao_ent IN ('Triagem', 'Retorno')
                        AND NOT EXISTS (SELECT c.id_ent_sala FROM tbl_consultorio c WHERE c.id_ent_sala=e.id_ent)
                        ORDER BY e.alta, e.classificacao, e.id_ent, e.data_hora_i_atend
                        LIMIT 1;";
            }
            else if ($perfilProf == 'ENFERMEIRO')
            {
                $sql = "SELECT e.*, u.*
                        FROM tbl_entradas e, tbl_usuarios u
                        WHERE e.cns_usuario=u.cns_usuario
                        AND e.alta='0'
                        AND e.situacao_ent IN ('---')
                        AND NOT EXISTS (SELECT c.id_ent_sala FROM tbl_consultorio c WHERE c.id_ent_sala=e.id_ent)
                        ORDER BY e.alta, e.classificacao, e.id_ent, e.data_hora_i_atend
                        LIMIT 1;";
            }

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $entrada->setIdEnt($linha['id_ent']);
                    

                    $usuario = new Usuarios();
                    $usuario->setCnsPessoa($linha['cns_usuario']);
                    $usuario->setNomePessoa($linha['nome_usuario']);
                    

                    if ($perfilProf == 'ENFERMEIRO')
                    {
                        

                        $endereco = new Endereco($linha['cep'], 
                                                 $linha['logradouro'], 
                                                 $linha['compl'], 
                                                 $linha['numero'], 
                                                 $linha['bairro'], 
                                                 $linha['municipio'], 
                                                 $linha['estado']);

                        
                    }

                    $entrada->setUsuario($usuario);

                    

                    $profissional = new Profissionais(null, null);
                    $profissional->setCnsPessoa($linha['cns_medico_i']);

                    
                }
            }
            
            return $entrada;
        }

        function insereEntrada(Entradas $entrada)
        {
            $msg = 0;
            
            $cnsUsuario = $entrada->getUsuario()->getCnsPessoa();
            $cnsProf = $entrada->getProfissionalExec()->getCnsPessoa();
            $dataEntrada = $entrada->getDataEntrada();
            $horaEntrada = $entrada->getHoraEntrada();
            
            $sql = "INSERT INTO tbl_entradas (cns_usuario, 
                                              cns_prof, 
                                              data_entrada, 
                                              hora_entrada) 
                    VALUES ('$cnsUsuario', 
                            '$cnsProf', 
                            '$dataEntrada', 
                            '$horaEntrada')";

            if ($this->conexao->getConn()->query($sql) === TRUE)
            {
                $msg = 1;
            }
            else
            {
                $erro = mysqli_errno($this->conexao->getConn());

				if ($erro == 1062)
				{
                    $msg = 2;
				}
            }

            return $msg;
        }

        function alteraEntrada(Entradas $entrada)
        {
            $msg = 0;
            
            $idEntrada = $entrada->getIdEnt();
            $dataSaida = $entrada->getDataSaida();
            $horaSaida = $entrada->getHoraSaida();
            
            $sql = "UPDATE tbl_entradas 
                    SET data_saida='$dataSaida',
                        hora_saida='$horaSaida' 
                    WHERE id_entrada=$idEntrada";

            if ($this->conexao->getConn()->query($sql) === TRUE)
            {
                $msg = 1;
            }
            else
            {
                $erro = mysqli_errno($this->conexao->getConn());

				if ($erro == 1062)
				{
                    $msg = 2;
				}
            }

            return $msg;
        }

        function excluiEntrada($idEntrada)
        {
            $msg = 0;
            
            $sql = "DELETE FROM tbl_entradas WHERE id_entrada=$idEntrada";

            if ($this->conexao->getConn()->query($sql) === TRUE)
            {
                $msg = 1;
            }
            else
            {
                $erro = mysqli_errno($this->conexao->getConn());

				if ($erro == 1062)
				{
                    $msg = 2;
				}
            }

            return $msg;
        }
    }
?>