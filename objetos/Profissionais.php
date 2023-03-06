<?php
    require_once 'objetos/Pessoa.php';

    class Profissionais extends Pessoa
    {
        private $senhaProf;
        private $perfilProf;
        private $statusProf;

        function __construct($perfil, $status)
        {
            $this->perfilProf = $perfil;
            $this->statusProf = $status;
        }

        function __destruct()
        {
            $this->cnsPessoa = null;
            $this->nomePessoa = null;

            $this->senhaProf = null;
            $this->perfilProf = null;
            $this->statusProf = null;
        }

        function getPerfilProf()
        {
            return $this->perfilProf;
        }

        function setPerfilProf($perfil)
        {
            $this->perfilProf = $perfil;
        }

        function getStatusProf()
        {
            return $this->statusProf;
        }

        function setStatus($status)
        {
            $this->statusProf = $status;
        }

        function getSenha()
        {
            return $this->senhaProf;
        }

        function setSenha($senha)
        {
            $this->senhaProf = $senha;
        }

        function geraArrayAtributos($arrayColunasAdicionais)
        {
            $arrayObjetoChaveValor = array('cns' => $this->getCnsPessoa(),
                                           'nome' => $this->getNomePessoa(),
                                           'perfil' => $this->getPerfilProf(),
                                           'status' => $this->getStatusProf());

            if (!empty($arrayColunasAdicionais))
            {
                for ($i = 0 ; $i < count($arrayColunasAdicionais) ; $i++)
                {
                    $novaColuna = $arrayColunasAdicionais[$i];

                    foreach($novaColuna as $atributo => $valor)
                    {
                        $arrayObjetoChaveValor[$atributo] = $valor;
                    }
                }
            }

            return $arrayObjetoChaveValor;
        }
    }
?>