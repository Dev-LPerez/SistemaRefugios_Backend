<?php
class UpdateDonacionDTO
{
    public $id_donacion;
    public $fecha;
    public $descripcion;
    public $id_donante;

    public function __construct($id, $data)
    {
        $this->id_donacion = $id;
        $this->fecha = $data['fecha'] ?? null;
        $this->descripcion = $data['descripcion'] ?? null;
        $this->id_donante = $data['id_donante'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_donacion) &&
            (!empty($this->fecha) || !empty($this->descripcion) || !empty($this->id_donante));
    }
}