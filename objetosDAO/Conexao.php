<?php
    class Conexao
    {
        private const NOME_SERVIDOR = "localhost";
        private const NOME_USUARIO = "root";
        private const NOME_BANCO_DADOS = "dae_chamado_db";
        private const SENHA_BANCO_DADOS = "";
        
        private $conn;

        function __construct()
        {
            $this->criaBancoDados();

            $this->conn = new mysqli(self::NOME_SERVIDOR, self::NOME_USUARIO, self::SENHA_BANCO_DADOS, self::NOME_BANCO_DADOS);
            
            mysqli_set_charset($this->conn,"utf8");
            
            if ($this->conn->connect_error) 
            {
                die("Falha ao Conectar: " . $this->conn->connect_error);
            }
        }

        function __destruct()
        {
            $this->conn->close();
        }

        function getConn()
        {
            return $this->conn;
        }

        function criaBancoDados()
        {
            $this->conn = new mysqli(self::NOME_SERVIDOR, self::NOME_USUARIO, self::SENHA_BANCO_DADOS);
            
            mysqli_set_charset($this->conn,"utf8");
            
            if ($this->conn->connect_error) 
            {
                die("Falha ao Conectar: " . $this->conn->connect_error);
            }

            try
            {
                $sql = "CREATE DATABASE " . self::NOME_BANCO_DADOS . ";";

                if ($this->conn->query($sql) == TRUE)
                {
                    echo "Banco de Dados criado com sucesso!";
                }
            }
            catch(Exception $ex)
            {
                //echo "<br>Falha ao criar Banco de Dados";
            }

            $this->conn->close();

            $this->criaTabelas();
        }

        function criaTabelas()
        {
            $sql = "CREATE TABLE IF NOT EXISTS tbl_usuarios (cns_usuario VARCHAR(15) PRIMARY KEY, 
                                               nome_usuario VARCHAR(100) NOT NULL);";
            $this->criaTabela($sql, "tbl_usuarios");

            $sql = "CREATE TABLE IF NOT EXISTS tbl_profissionais (cns_prof VARCHAR(15) PRIMARY KEY, 
                                                                  nome_prof VARCHAR(100) NOT NULL, 
                                                                  senha_prof VARCHAR(60) NOT NULL, 
                                                                  perfil_prof VARCHAR(30) NOT NULL,
                                                                  status_prof VARCHAR(7) NOT NULL);";
            $this->criaTabela($sql, "tbl_profissionais");

            $sql = "CREATE TABLE IF NOT EXISTS tbl_procedimentos (id_procd INT(6) AUTO_INCREMENT PRIMARY KEY, 
                                                                  nome_procd VARCHAR(100) NOT NULL);";
            $this->criaTabela($sql, "tbl_procedimentos");

            $sql = "CREATE TABLE IF NOT EXISTS tbl_entradas (id_entrada INT(6) AUTO_INCREMENT PRIMARY KEY, 
                                                             cns_usuario VARCHAR(15) NOT NULL, 
                                                             cns_prof VARCHAR(15) NOT NULL, 
                                                             data_entrada VARCHAR(10) NOT NULL,
                                                             hora_entrada VARCHAR(8) NOT NULL,
                                                             data_saida VARCHAR(10) NULL,
                                                             hora_saida VARCHAR(8) NULL);";
            $this->criaTabela($sql, "tbl_entradas");

            $sql = "CREATE TABLE IF NOT EXISTS tbl_unidade_exec (cnes_unidade VARCHAR(7) PRIMARY KEY, 
                                                                 nome_unidade VARCHAR(100) NOT NULL, 
                                                                 cep_unidade VARCHAR(8) NOT NULL, 
                                                                 logr_unidade VARCHAR(100) NOT NULL,
                                                                 num_unidade VARCHAR(5) NOT NULL,
                                                                 bairro_unidade VARCHAR(50) NOT NULL,
                                                                 municipio_unidade VARCHAR(50) NOT NULL,
                                                                 uf_unidade VARCHAR(2) NOT NULL);";
            $this->criaTabela($sql, "tbl_unidade_exec");

            $sql = "CREATE TABLE IF NOT EXISTS tbl_fichas (id_ficha INT(6) AUTO_INCREMENT PRIMARY KEY, 
                                                           nome_usuario VARCHAR(100) NOT NULL,
                                                           num_ficha INT(6) NOT NULL, 
                                                           data_ficha VARCHAR(10) NOT NULL);";
            $this->criaTabela($sql, "tbl_fichas");
        }

        function criaTabela($sql, $nomeTabela)
        {
            try
            {
                $this->conn = new mysqli(self::NOME_SERVIDOR, self::NOME_USUARIO, self::SENHA_BANCO_DADOS, self::NOME_BANCO_DADOS);
            
                mysqli_set_charset($this->conn,"utf8");
                
                if ($this->conn->connect_error) 
                {
                    die("Falha ao Conectar: " . $this->conn->connect_error);
                }
    
                if ($this->conn->query($sql) == TRUE)
                {
                    //echo "Tabela $nomeTabela criada com sucesso!";
                }
            }
            catch(Exception $ex)
            {
                //echo "<br>Falha ao criar Tabela $nomeTabela";
            }

            $this->conn->close();
        }
    }
?>