<?php
    require_once 'objetosDAO/Conexao.php';
    require_once 'objetos/Usuarios.php';
    require_once 'objetos/Endereco.php';

    class UsuariosDAO
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

        function selecionaUsuario($cnsUsuario)
        {
            $usuario = new Usuarios();

            $sql = "SELECT u.*
                    FROM tbl_usuarios u
                    WHERE u.cns_usuario='$cnsUsuario';";

            $res = $this->conexao->getConn()->query($sql);

            if ($res->num_rows > 0)
            {
                while($linha = $res->fetch_assoc())
                {
                    $usuario->setCnsPessoa($linha['cns_usuario']);
                    $usuario->setNomePessoa($linha['nome_usuario']);
                }
            }
            
            return $usuario;
        }

        function insereUsuario(Usuarios $usuario)
        {
            $msg = 0;

            $dadosUsuario = array(
                                array('cns_usuario', 'string', $usuario->getCnsPessoa()),
                                array('nome_usuario', 'string', $usuario->getNomePessoa())
                                );
            
            $sql = 'INSERT INTO tbl_usuarios ';
            $sqlCampos = '(';
		    $sqlValores = '(';

            for ($i = 0 ; $i < count($dadosUsuario) ; $i++)
            {
                $aspa = "";

                if ($dadosUsuario[$i][2] != null)
                {
                    if ($dadosUsuario[$i][1] == 'string')
                    {
                        $aspa = "'";
                    }

                    $nomeCampo = $dadosUsuario[$i][0];
                    $valorCampo = $aspa . $dadosUsuario[$i][2] . $aspa;

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

        function alteraUsuario(Usuarios $usuario)
        {
            $msg = 0;
            
            $dadosUsuario = array(
                                array('nome_usuario', 'string', $usuario->getNomePessoa())
                            );
            
            $sql = "UPDATE tbl_usuarios SET ";
            $sqlCamposValores = "";

            for ($i = 0 ; $i < count($dadosUsuario) ; $i++)
            {
                $aspa = "";

                if ($dadosUsuario[$i][2] != null)
                {
                    if ($dadosUsuario[$i][1] == 'string')
                    {
                        $aspa = "'";
                    }

                    $nomeCampo = $dadosUsuario[$i][0];
                    $valorCampo = $aspa . $dadosUsuario[$i][2] . $aspa;

                    $sqlCamposValores = $sqlCamposValores . ", $nomeCampo=$valorCampo";
                }
            }
            
            $sql = $sql . $sqlCamposValores . " WHERE cns_usuario='" . $usuario->getCnsPessoa() . "';";
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
    }
?>