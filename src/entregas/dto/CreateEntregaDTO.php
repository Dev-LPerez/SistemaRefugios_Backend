<?php
class CreateEntregaDTO
{
    public $estado;
    public $fecha;
    public $cantidad;
    public $id_familia;
    public $id_recurso;

    public function __construct($data)
    {
        $this->estado = $data['estado'] ?? 'Entregado'; // Por defecto lo marcamos como entregado
        $this->fecha = $data['fecha'] ?? date('Y-m-d');
        $this->cantidad = isset($data['cantidad']) ? (float) $data['cantidad'] : 0;
        $this->id_familia = $data['id_familia'] ?? null;
        $this->id_recurso = $data['id_recurso'] ?? null;
    }

    public function isValid()
    {
        return $this->cantidad > 0 && !empty($this->id_familia) && !empty($this->id_recurso);
    }
}