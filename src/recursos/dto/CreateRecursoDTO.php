<?php
class CreateRecursoDTO
{
    public $nombre;
    public $tipo;
    public $unidad;
    public $cantidad_disponible;
    public $categoria;
    public $stock;
    public $peso; // Needed to compute capacity

    public function __construct($data)
    {
        $this->nombre = $data['nombre'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->unidad = $data['unidad'] ?? null;
        $this->cantidad_disponible = isset($data['cantidad_disponible']) ? (float) $data['cantidad_disponible'] : 0;
        $this->categoria = $data['categoria'] ?? 'Sin Categoría';
        $this->stock = isset($data['stock']) ? (float) $data['stock'] : 0.00;
        $this->peso = isset($data['peso']) ? (float) $data['peso'] : 0.00;
    }

    public function isValid()
    {
        return !empty($this->nombre) && !empty($this->tipo) && !empty($this->unidad);
    }
}