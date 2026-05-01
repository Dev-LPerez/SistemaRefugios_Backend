<?php
class DetalleGestion
{
    public $id_detalle;
    public $id_usuario;
    public $id_recurso;
    public $accion; // Ej: 'Creación', 'Actualización', 'Eliminación', 'Ajuste manual'

    public function __construct($id_detalle, $id_usuario, $id_recurso, $accion)
    {
        $this->id_detalle = $id_detalle;
        $this->id_usuario = $id_usuario;
        $this->id_recurso = $id_recurso;
        $this->accion = $accion;
    }
}