<?php
    class Endereco
    {
        private $cep;
        private $logradouro;
        private $numero;
        private $bairro;
        private $municipio;
        private $uf;

        function __construct($cep, $logradouro, $compl, $numero, $bairro, $municipio, $uf)
        {
            $this->cep = $cep;
            $this->logradouro = $logradouro;
            $this->compl = $compl;
            $this->numero = $numero;
            $this->bairro = $bairro;
            $this->municipio = $municipio;
            $this->uf = $uf;
        }

        function __destruct()
        {
            $this->cep = null;
            $this->logradouro = null;
            $this->compl = null;
            $this->numero = null;
            $this->bairro = null;
            $this->municipio = null;
            $this->uf = null;
        }

        function getCep()
        {
            return $this->cep;
        }

        function setCep($cep)
        {
            $this->cep = $cep;   
        }

        function getLogradouro()
        {
            return $this->logradouro;
        }

        function setLogradouro($logradouro)
        {
            $this->logradouro = $logradouro;   
        }

        function getCompl()
        {
            return $this->compl;
        }

        function setCompl($compl)
        {
            $this->compl = $compl;   
        }

        function getNumero()
        {
            return $this->numero;
        }

        function setNumero($numero)
        {
            $this->numero = $numero;   
        }

        function getBairro()
        {
            return $this->bairro;
        }

        function setBairro($bairro)
        {
            $this->bairro = $bairro;   
        }

        function getMunicipio()
        {
            return $this->municipio;
        }

        function setMunicipio($municipio)
        {
            $this->municipio = $municipio;   
        }

        function getUf()
        {
            return $this->uf;
        }

        function setUf($uf)
        {
            $this->uf = $uf;   
        }

        function geraArrayAtributos($arrayColunasAdicionais)
        {
            $arrayObjetoChaveValor = array('cep' => $this->getCep(),
                                           'logradouro' => $this->getLogradouro(),
                                           'numero' => $this->getNumero(),
                                           'compl' => $this->getCompl(),
                                           'bairro' => $this->getBairro(),
                                           'municipio' => $this->getMunicipio(),
                                           'estado' => $this->getUf());

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