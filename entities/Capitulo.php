<?php
class Capitulo
{
    private $IdCapitulo;
    private $HistoriaId;
    private $NumCapitulo;
    private $TituloCap;
    private $Historia;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
