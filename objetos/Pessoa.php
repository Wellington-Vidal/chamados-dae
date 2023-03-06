<?php
    class Pessoa
    {
        private $cnsPessoa;
        private $nomePessoa;

        function __construct($cnsPessoa, $nomePessoa)
        {
            $this->cnsPessoa = $cnsPessoa;
            $this->nomePessoa = $nomePessoa;
        }

        function __destruct()
        {
            $this->cnsPessoa = null;
            $this->nomePessoa = null;
        }

        function getCnsPessoa()
        {
            return $this->cnsPessoa;
        }

        function setCnsPessoa($cnsPessoa)
        {
            $this->cnsPessoa = $cnsPessoa;
        }

        function getNomePessoa()
        {
            return $this->nomePessoa;
        }

        function setNomePessoa($nomePessoa)
        {
            $this->nomePessoa = $nomePessoa;
        }

        public static function validaCNS($cns)
        {
            $cnsValido = false;

            if ((strlen(trim($cns))) == 15)
            {
                switch (substr($cns,0,1))
                {
                    case 1:
                    case 2:
                        $pis = substr($cns, 0, 11);

                        $soma = (((substr($pis, 0,1)) * 15) +
                                ((substr($pis, 1,1)) * 14) +
                                ((substr($pis, 2,1)) * 13) +
                                ((substr($pis, 3,1)) * 12) +
                                ((substr($pis, 4,1)) * 11) +
                                ((substr($pis, 5,1)) * 10) +
                                ((substr($pis, 6,1)) * 9) +
                                ((substr($pis, 7,1)) * 8) +
                                ((substr($pis, 8,1)) * 7) +
                                ((substr($pis, 9,1)) * 6) +
                                ((substr($pis, 10,1)) * 5));
                                
                        $resto = fmod($soma, 11);
                        $dv = 11 - $resto;

                        if ($dv == 11) 
                        { 
                            $dv = 0;
                        }

                        if ($dv == 10) 
                        { 
                            $soma = ((((substr($pis, 0,1)) * 15) +
                                    ((substr($pis, 1,1)) * 14) +
                                    ((substr($pis, 2,1)) * 13) +
                                    ((substr($pis, 3,1)) * 12) +
                                    ((substr($pis, 4,1)) * 11) +
                                    ((substr($pis, 5,1)) * 10) +
                                    ((substr($pis, 6,1)) * 9) +
                                    ((substr($pis, 7,1)) * 8) +
                                    ((substr($pis, 8,1)) * 7) +
                                    ((substr($pis, 9,1)) * 6) +
                                    ((substr($pis, 10,1)) * 5)) + 2);
                                    
                            $resto = fmod($soma, 11);
                            $dv = 11 - $resto;
                            $resultado = $pis . "001" . $dv;
                        }
                        else 
                        { 
                            $resultado = $pis . "000" . $dv;
                        }

                        if ($cns == $resultado)
                        {
                            $cnsValido = true;
                        }
                        break;
                    case 7:
                    case 8:
                    case 9:
                        $soma = (((substr($cns,0,1)) * 15) +
                                ((substr($cns,1,1)) * 14) +
                                ((substr($cns,2,1)) * 13) +
                                ((substr($cns,3,1)) * 12) +
                                ((substr($cns,4,1)) * 11) +
                                ((substr($cns,5,1)) * 10) +
                                ((substr($cns,6,1)) * 9) +
                                ((substr($cns,7,1)) * 8) +
                                ((substr($cns,8,1)) * 7) +
                                ((substr($cns,9,1)) * 6) +
                                ((substr($cns,10,1)) * 5) +
                                ((substr($cns,11,1)) * 4) +
                                ((substr($cns,12,1)) * 3) +
                                ((substr($cns,13,1)) * 2) +
                                ((substr($cns,14,1)) * 1));
                            
                        $resto = fmod($soma, 11);
                        
                        if ($resto == 0)
                        {
                            $cnsValido = true;
                        }
                        break;
                    default:
                        break;
                }
            }

            return $cnsValido;
        }
    }
?>