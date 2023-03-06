<?php
    require_once 'objetos/Endereco.php';

    class Unidades
    {
        private $cnesUnidade;
        private $nomeUnidade;
        private Endereco $endereco;

        function __construct($cnesUnidade, $nomeUnidade, Endereco $endereco)
        {
            $this->cnesUnidade = $cnesUnidade;
            $this->nomeUnidade = $nomeUnidade;
            $this->endereco = $endereco;
        }

        function __destruct()
        {
            $this->cnesUnidade = null;
            $this->nomeUnidade = null;

            if (isset($this->endereco))
            {
                extract(get_object_vars($this->endereco));
            }
        }

        function getCnesUnidade()
        {
            return $this->cnesUnidade;
        }

        function setCnesUnidade($cnesUnidade)
        {
            $this->cnesUnidade = $cnesUnidade;
        }

        function getNomeUnidade()
        {
            return $this->nomeUnidade;
        }

        function setNomeUnidade($nomeUnidade)
        {
            $this->nomeUnidade = $nomeUnidade;
        }

        function getEndereco()
        {
            return $this->endereco;
        }

        function setEndereco(Endereco $endereco)
        {
            $this->endereco = $endereco;
        }

        function geraArrayAtributos($arrayColunasAdicionais)
        {
            $arrayObjetoChaveValor = array('cnesUnidade' => $this->getCnesUnidade(),
                                           'nomeUnidade' => $this->getNomeUnidade(),
                                           'cepUnidade' => $this->getEndereco()->getCep(),
                                           'logrUnidade' => $this->getEndereco()->getLogradouro(),
                                           'numUnidade' => $this->getEndereco()->getNumero(),
                                           'bairroUnidade' => $this->getEndereco()->getBairro(),
                                           'municipioUnidade' => $this->getEndereco()->getMunicipio(),
                                           'ufUnidade' => $this->getEndereco()->getUf());

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