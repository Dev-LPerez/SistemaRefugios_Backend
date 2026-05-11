<?php
class CreateDonacionDTO
{
    public $fecha;
    public $descripcion;
    public $id_donante;
    public $origen;
    public $categoria;

    public function __construct($data)
    {
        $this->fecha = $data['fecha'] ?? date('Y-m-d'); // Fecha actual por defecto si no se envía
        $this->descripcion = $data['descripcion'] ?? null;
        $this->id_donante = $data['id_donante'] ?? null;
        $this->origen = $data['origen'] ?? 'Desconocido';
        $this->categoria = $data['categoria'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->fecha) && !empty($this->id_donante) && !empty($this->categoria);
    }
}