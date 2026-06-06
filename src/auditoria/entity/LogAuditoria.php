<?php
class LogAuditoria
{
    public $id;
    public $usuario_id;
    public $accion;
    public $entidad;
    public $fecha;
    public $ip;
    public $detalle;

    public function __construct($usuario_id, $accion, $entidad, $ip, $detalle = null)
    {
        $this->usuario_id = $usuario_id;
        $this->accion = $accion;
        $this->entidad = $entidad;
        $this->ip = $ip;
        $this->detalle = $detalle;
    }
}