<?php
class CreateRecursoDTO
{
    public $nombre;
    public $tipo;
    public $unidad;
    public $cantidad_disponible;

    public function __construct($data)
    {
        $this->nombre = $data['nombre'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->unidad = $data['unidad'] ?? null;
        $this->cantidad_disponible = isset($data['cantidad_disponible']) ? (float) $data['cantidad_disponible'] : 0;
    }

    public function isValid()
    {
        return !empty($this->nombre) && !empty($this->tipo) && !empty($this->unidad);
    }
}