<?php
    require_once 'objetos/Pessoa.php';

    class Usuarios extends Pessoa
    {
        function __construct()
        {
            //CONSTRUTOR DE PESSOA
        }

        function __destruct()
        {
            $this->cnsPessoa = null;
            $this->nomePessoa = null;
        }

        function geraArrayAtributos($arrayColunasAdicionais)
        {
            $arrayObjetoChaveValor = array('cnsUsuario' => $this->getCnsPessoa(),
                                           'nomeUsuario' => $this->getNomePessoa());

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
