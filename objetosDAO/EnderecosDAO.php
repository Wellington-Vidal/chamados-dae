<?php
    require_once 'objetosDAO/Conexao.php';
    require_once 'objetos/Endereco.php';

    class EnderecosDAO
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

        function listaLogradouros()
        {
            $logradouros = array();

            $sql = "SELECT l.* FROM tbl_logradouros l;";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $cepLog = $linha['cep_log'];
                    $nomeLog = $linha['nome_log'];
                    $bairroLog = $linha['bairro_log'];
                    $localidade = explode("/", $linha['localidade']) ;
                    $municipio = $localidade[0];
                    $estado = $localidade[0];
                    
                    $endereco = new Endereco($cepLog, $nomeLog, null, null, $bairroLog, $municipio, $estado);

                    array_push($logradouros, $endereco);
                }
            }
            
            return $logradouros;
        }

        function selecionaLogradouro($cep)
        {
            $endereco = new Endereco(null, null, null, null, null, null, null);

            $sql = "SELECT l.* 
                    FROM tbl_logradouros l
                    WHERE l.cep_log='$cep';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $cepLog = $linha['cep_log'];
                    $nomeLog = $linha['nome_log'];
                    $bairroLog = $linha['bairro_log'];
                    $localidade = explode("/", $linha['localidade']) ;
                    $municipio = $localidade[0];
                    $estado = $localidade[1];
                    
                    $endereco->setCep($cepLog);
                    $endereco->setLogradouro($nomeLog);
                    $endereco->setBairro($bairroLog);
                    $endereco->setMunicipio($municipio);
                    $endereco->setUf($estado);
                }
            }
            
            return $endereco;
        }
    }
?>