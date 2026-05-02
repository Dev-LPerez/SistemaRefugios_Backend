<?php
class UpdateDonanteDTO
{
    public $id_donante;
    public $identificacion;
    public $nombre;
    public $tipo;
    public $telefono;

    public function __construct($id, $data)
    {
        $this->id_donante = $id;
        $this->identificacion = $data['identificacion'] ?? null;
        $this->nombre = $data['nombre'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_donante) &&
            (!empty($this->identificacion) || !empty($this->nombre) || !empty($this->tipo) || !empty($this->telefono));
    }
}