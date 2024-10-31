<?php
class Historia_estado
{
    private $HistoriaId;
    private $EstadoId;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
