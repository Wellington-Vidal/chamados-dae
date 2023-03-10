<?php
    require_once 'objetos/Usuarios.php';
    require_once 'objetos/Profissionais.php';

    class Entradas
    {
        private $idEnt;
        private $dataEntrada;
        private $horaEntrada;
        private Usuarios $usuario;
        private Profissionais $profissionalExec;
        private $dataSaida;
        private $horaSaida;

        function __construct()
        {
            $this->idEnt = null;
            $this->dataEntrada = null;
            $this->horaEntrada = null;
            $this->usuario = new Usuarios();
            $this->profissionalExec = new Profissionais(null, null);
            $this->dataSaida = null;
            $this->horaSaida = null;
        }

        function __destruct()
        {
            $this->idEnt = null;
            $this->dataEntrada = null;
            $this->horaEntrada = null;

            if (isset($this->usuario))
            {
                extract(get_object_vars($this->usuario));
            }

            if (isset($this->profissionalExec))
            {
                extract(get_object_vars($this->profissionalExec));
            }

            $this->dataSaida = null;
            $this->horaSaida = null;
        }

        function getIdEnt()
        {
            return $this->idEnt;
        }

        function setIdEnt($idEnt)
        {
            $this->idEnt = $idEnt;
        }

        function getDataEntrada()
        {
            return $this->dataEntrada;
        }

        function setDataEntrada($dataEntrada)
        {
            $this->dataEntrada = $dataEntrada;
        }

        function getHoraEntrada()
        {
            return $this->horaEntrada;
        }

        function setHoraEntrada($horaEntrada)
        {
            $this->horaEntrada = $horaEntrada;
        }

        function getUsuario()
        {
            return $this->usuario;
        }

        function setUsuario(Usuarios $usuario)
        {
            $this->usuario = $usuario;
        }

        function getProfissionalExec()
        {
            return $this->profissionalExec;
        }

        function setProfissionalExec(Profissionais $profissionalExec)
        {
            $this->profissionalExec = $profissionalExec;
        }

        function getDataSaida()
        {
            return $this->dataSaida;
        }

        function setDataSaida($dataSaida)
        {
            $this->dataSaida = $dataSaida;
        }

        function getHoraSaida()
        {
            return $this->horaSaida;
        }

        function setHoraSaida($horaSaida)
        {
            $this->horaSaida = $horaSaida;
        }

        function geraArrayAtributos($arrayColunasAdicionais)
        {
            $arrayObjetoChaveValor = array('idEnt' => $this->getIdEnt(),
                                           'dataEntrada' => $this->getDataEntrada(),
                                           'horaEntrada' => $this->getHoraEntrada(),
                                           'cnsUsuario' => $this->getUsuario()->getCnsPessoa(),
                                           'nomeUsuario' => $this->getUsuario()->getNomePessoa(),
                                           'cnsProfissionalExec' => $this->getProfissionalExec()->getCnsPessoa(),
                                           'nomeProfissionalExec' => $this->getProfissionalExec()->getNomePessoa(),
                                           'dataSaida' => $this->getDataSaida(),
                                           'horaSaida' => $this->getHoraSaida());

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