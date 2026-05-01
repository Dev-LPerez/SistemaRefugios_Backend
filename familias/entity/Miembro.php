<?php
class Miembro
{
    public $id_persona;
    public $nombre;
    public $edad;
    public $parentezco;
    public $tipo_documento;
    public $numero_documento;
    public $vulnerable;
    public $tipo_vulnerabilidad;
    public $id_familia;

    public function __construct($id_persona, $nombre, $edad, $parentezco, $tipo_documento, $numero_documento, $vulnerable, $tipo_vulnerabilidad, $id_familia)
    {
        $this->id_persona = $id_persona;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->parentezco = $parentezco;
        $this->tipo_documento = $tipo_documento;
        $this->numero_documento = $numero_documento;
        $this->vulnerable = $vulnerable;
        $this->tipo_vulnerabilidad = $tipo_vulnerabilidad;
        $this->id_familia = $id_familia;
    }
}