<?php
class Favorito
{
    private $UsuarioId ;
    private $HistoriaId ;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
