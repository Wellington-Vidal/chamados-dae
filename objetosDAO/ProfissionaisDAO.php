<?php
    require_once 'objetosDAO/Conexao.php';
    require_once 'objetos/Profissionais.php';

    class ProfissionaisDAO
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

        function selecionaProfissional($cnsProf)
        {
            $profissional = new Profissionais(null, null);

            $sql = "SELECT p.*
                    FROM tbl_profissionais p
                    WHERE p.cns_prof='$cnsProf';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $profissional->setCnsPessoa($linha['cns_prof']);
                    $profissional->setNomePessoa($linha['nome_prof']);
                    $profissional->setStatus($linha['status_prof']);
                    $profissional->setPerfilProf($linha['perfil_prof']);
                }
            }
            
            return $profissional;
        }

        function listaProfissionais()
        {
            $arrayProfissionais = array();

            $sql = "SELECT p.* 
                    FROM tbl_profissionais p
                    ORDER BY nome_prof;";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $profissional = new Profissionais($linha['perfil_prof'], $linha['status_prof']);
                    $profissional->setCnsPessoa($linha['cns_prof']);
                    $profissional->setNomePessoa($linha['nome_prof']);

                    array_push($arrayProfissionais, $profissional);
                }
            }
            
            return $arrayProfissionais;
        }

        function verificaProfissional(Profissionais $profissional)
        {
            $profissionalRet = null;

            $cnsProf = $profissional->getCnsPessoa();
            $senhaProf = $profissional->getSenha();

            $sql = "SELECT p.*
                    FROM tbl_profissionais p
                    WHERE p.cns_prof='$cnsProf'
                    AND p.senha_prof='$senhaProf';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $profissionalRet = new Profissionais($linha['perfil_prof'], $linha['status_prof']);
                    $profissionalRet->setCnsPessoa($linha['cns_prof']);
                    $profissionalRet->setNomePessoa($linha['nome_prof']);
                }
            }
            
            return $profissionalRet;
        }

        function insereProfissional(Profissionais $profissional)
        {
            $msg = 0;

            $cnsProf = $profissional->getCnsPessoa();
            $nomeProf = $profissional->getNomePessoa();
            $senhaProf = $profissional->getSenha();
            $perfilProf = $profissional->getPerfilProf();
            $statusProf = $profissional->getStatusProf();

            $sql = "INSERT INTO tbl_profissionais ( cns_prof, 
                                                    nome_prof, 
                                                    senha_prof, 
                                                    perfil_prof, 
                                                    status_prof)
                    VALUES ('$cnsProf', 
                            '$nomeProf', 
                            '$senhaProf', 
                            '$perfilProf', 
                            '$statusProf');";
            
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

        function alteraProfissional(Profissionais $profissional)
        {
            $msg = 0;
            
            $cnsProf = $profissional->getCnsPessoa();
            $nomeProf = $profissional->getNomePessoa();
            $senhaProf = $profissional->getSenha();
            $statusProf = $profissional->getStatusProf();

            if (empty($senhaProf))
            {
                $sql = "UPDATE tbl_profissionais 
                        SET status_prof='$statusProf'
                            nome_prof='$nomeProf'
                        WHERE cns_prof='$cnsProf'";
            }
            else
            {
                $sql = "UPDATE tbl_profissionais 
                        SET senha_prof='$senhaProf',
                            status_prof='$statusProf',
                            nome_prof='$nomeProf'
                        WHERE cns_prof='$cnsProf';";
            }
            
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
                else
                {
                    $msg = mysqli_error($this->conexao->getConn());
                }
            }

            return $msg;
        }
    }
?>