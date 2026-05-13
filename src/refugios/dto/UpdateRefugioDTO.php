<?php
class UpdateRefugioDTO
{
    public $id_refugio;
    public $nombre;
    public $direccion;
    public $capacidad_maxima;
    public $ocupacion_actual;
    public $estado;

    public function __construct($id, $data)
    {
        $this->id_refugio = $id;
        $this->nombre = $data['nombre'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->capacidad_maxima = $data['capacidad_maxima'] ?? null;
        // usar isset en vez de empty porque ocupación podría ser 0
        $this->ocupacion_actual = isset($data['ocupacion_actual']) ? $data['ocupacion_actual'] : null; 
        $this->estado = $data['estado'] ?? null;
    }

    public function isValid()
    {
        // Se valida al menos un campo con valor a actualizar
        return !empty($this->id_refugio) && (
            !empty($this->nombre) || 
            !empty($this->direccion) || 
            !empty($this->capacidad_maxima) || 
            isset($this->ocupacion_actual) || 
            !empty($this->estado)
        );
    }
}