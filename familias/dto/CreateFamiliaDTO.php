<?php
class CreateFamiliaDTO
{
    public $representante;
    public $telefono;
    public $direccion;
    public $cantidad_miembros;
    public $prioridad;
    public $id_refugio;

    public function __construct($data)
    {
        $this->representante = $data['representante'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->cantidad_miembros = $data['cantidad_miembros'] ?? null;
        $this->prioridad = $data['prioridad'] ?? null;
        $this->id_refugio = $data['id_refugio'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->representante) &&
            !empty($this->direccion) &&
            !empty($this->cantidad_miembros) &&
            !empty($this->prioridad) &&
            !empty($this->id_refugio);
    }
}