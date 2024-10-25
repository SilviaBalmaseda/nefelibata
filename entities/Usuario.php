<?php
class Usuario
{
    private $IdUsuario;
    private $Nombre;
    private $Clave;
    private $Email;

    public function __get($propiedad)
    {
        return $this->$propiedad;
    }

    public function __set($propiedad, $valor)
    {
        $this->$propiedad = $valor;
    }
}
