<?php
class Historia_genero
{
    private $HistoriaId;
    private $GeneroId;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
