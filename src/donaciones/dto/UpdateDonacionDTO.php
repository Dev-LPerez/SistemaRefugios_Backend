<?php
class UpdateDonacionDTO
{
    public $id_donacion;
    public $fecha;
    public $descripcion;
    public $id_donante;
    public $origen;
    public $categoria;

    public function __construct($id, $data)
    {
        $this->id_donacion = $id;
        $this->fecha = $data['fecha'] ?? null;
        $this->descripcion = $data['descripcion'] ?? null;
        $this->id_donante = $data['id_donante'] ?? null;
        $this->origen = $data['origen'] ?? null;
        $this->categoria = $data['categoria'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_donacion) &&
            (!empty($this->fecha) || !empty($this->descripcion) || !empty($this->id_donante) || !empty($this->origen) || !empty($this->categoria));
    }
}