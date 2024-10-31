<?php
class Historia
{
    private $IdHistoria;
    private $Titulo;
    private $UsuarioId;
    private $Sinopsis;
    private $Imagen;
    private $NumFavorito;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
