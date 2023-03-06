<?php
    require_once 'objetosDAO/Conexao.php';
    require_once 'objetos/Unidades.php';
    require_once 'objetos/Endereco.php';

    class UnidadesDAO
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

        function selecionaUnidade($cnesUnidade)
        {
            $unidade = new Unidades("", "", new Endereco("", "", "", "", "", "", ""));

            $sql = "SELECT u.*
                    FROM tbl_unidade_exec u
                    WHERE u.cnes_unidade='$cnesUnidade';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $unidade->setCnesUnidade($linha['cnes_unidade']);
                    $unidade->setNomeUnidade($linha['nome_unidade']);

                    $endereco = new Endereco($linha['cep_unidade'], 
                                             $linha['logr_unidade'], 
                                             "", 
                                             $linha['num_unidade'], 
                                             $linha['bairro_unidade'], 
                                             $linha['municipio_unidade'], 
                                             $linha['uf_unidade']);

                    $unidade->setEndereco($endereco);
                }
            }
            
            return $unidade;
        }

        function listaUnidades()
        {
            $unidades = array();

            $sql = "SELECT u.*
                    FROM tbl_unidade_exec u;";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $endereco = new Endereco($linha['cep_unidade'], 
                                             $linha['logr_unidade'], 
                                             "", 
                                             $linha['num_unidade'], 
                                             $linha['bairro_unidade'], 
                                             $linha['municipio_unidade'], 
                                             $linha['uf_unidade']);

                    $unidade = new Unidades($linha['cnes_unidade'], 
                                            $linha['nome_unidade'], 
                                            $endereco);

                    array_push($unidades, $unidade);
                }
            }
            
            return $unidades;
        }

        function insereUnidade(Unidades $unidade)
        {
            $msg = 0;

            $dadosUnidade = array(
                                array('cnes_unidade', 'string', $unidade->getCnesUnidade()),
                                array('nome_unidade', 'string', $unidade->getNomeUnidade()),
                                array('cep_unidade', 'string', $unidade->getEndereco()->getCep()),
                                array('logr_unidade', 'string', $unidade->getEndereco()->getLogradouro()),
                                array('num_unidade', 'string', $unidade->getEndereco()->getNumero()),
                                array('bairro_unidade', 'string', $unidade->getEndereco()->getBairro()),
                                array('municipio_unidade', 'string', $unidade->getEndereco()->getMunicipio()),
                                array('uf_unidade', 'string', $unidade->getEndereco()->getUf())
                                );
            
            $sql = 'INSERT INTO tbl_unidade_exec ';
            $sqlCampos = '(';
		    $sqlValores = '(';

            for ($i = 0 ; $i < count($dadosUnidade) ; $i++)
            {
                $aspa = "";

                if ($dadosUnidade[$i][2] != null)
                {
                    if ($dadosUnidade[$i][1] == 'string')
                    {
                        $aspa = "'";
                    }

                    $nomeCampo = $dadosUnidade[$i][0];
                    $valorCampo = $aspa . $dadosUnidade[$i][2] . $aspa;

                    $sqlCampos = $sqlCampos . ", $nomeCampo";
                    $sqlValores = $sqlValores . ", $valorCampo";
                }
            }
            
            $sqlCampos = str_replace("(, ", "(", $sqlCampos);
            $sqlValores = str_replace("(, ", "(", $sqlValores);

		    $sql = $sql . $sqlCampos . ') VALUES ' . $sqlValores . ');';

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

        function alteraUnidade(Unidades $unidade)
        {
            $msg = 0;
            
            $dadosUnidade = array(
                                array('cnes_unidade', 'string', $unidade->getCnesUnidade()),
                                array('nome_unidade', 'string', $unidade->getNomeUnidade()),
                                array('cep_unidade', 'string', $unidade->getEndereco()->getCep()),
                                array('logr_unidade', 'string', $unidade->getEndereco()->getLogradouro()),
                                array('num_unidade', 'string', $unidade->getEndereco()->getNumero()),
                                array('bairro_unidade', 'string', $unidade->getEndereco()->getBairro()),
                                array('municipio_unidade', 'string', $unidade->getEndereco()->getMunicipio()),
                                array('uf_unidade', 'string', $unidade->getEndereco()->getUf())
                                );
            
            $sql = "UPDATE tbl_unidade_exec SET ";
            $sqlCamposValores = "";

            for ($i = 0 ; $i < count($dadosUnidade) ; $i++)
            {
                $aspa = "";

                if ($dadosUnidade[$i][2] != null)
                {
                    if ($dadosUnidade[$i][1] == 'string')
                    {
                        $aspa = "'";
                    }

                    $nomeCampo = $dadosUnidade[$i][0];
                    $valorCampo = $aspa . $dadosUnidade[$i][2] . $aspa;

                    $sqlCamposValores = $sqlCamposValores . ", $nomeCampo=$valorCampo";
                }
            }
            
            $sql = $sql . $sqlCamposValores . " WHERE cnes_unidade='" . $unidade->getCnesUnidade() . "';";
            $sql = str_replace("SET ,", "SET ", $sql);

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

        function excluiUnidade($cnes)
        {
            $msg = 0;

            $sql = "DELETE FROM tbl_unidade_exec 
                    WHERE cnes_unidade = '$cnes'";

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