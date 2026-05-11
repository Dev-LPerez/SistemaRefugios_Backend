<?php
class UpdateRecursoDTO
{
    public $id_recurso;
    public $nombre;
    public $tipo;
    public $unidad;
    public $cantidad_disponible;
    public $categoria;
    public $stock;
    public $peso;

    public function __construct($id, $data)
    {
        $this->id_recurso = $id;
        $this->nombre = $data['nombre'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->unidad = $data['unidad'] ?? null;
        $this->cantidad_disponible = isset($data['cantidad_disponible']) ? (float) $data['cantidad_disponible'] : null;
        $this->categoria = $data['categoria'] ?? null;
        $this->stock = isset($data['stock']) ? (float) $data['stock'] : null;
        $this->peso = isset($data['peso']) ? (float) $data['peso'] : null;
    }

    public function isValid()
    {
        return !empty($this->id_recurso) &&
            (!empty($this->nombre) || !empty($this->tipo) || !empty($this->unidad) || $this->cantidad_disponible !== null || !empty($this->categoria) || $this->stock !== null || $this->peso !== null);
    }
}