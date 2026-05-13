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
    
    // Nuevos campos
    public $es_embarazada;
    public $tiene_discapacidad;
    public $enfermedad_cronica;

    public function __construct(
        $id_persona, $nombre, $edad, $parentezco, $tipo_documento, 
        $numero_documento, $vulnerable, $tipo_vulnerabilidad, $id_familia,
        $es_embarazada = 0, $tiene_discapacidad = 0, $enfermedad_cronica = 0
    ) {
        $this->id_persona = $id_persona;
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->parentezco = $parentezco;
        $this->tipo_documento = $tipo_documento;
        $this->numero_documento = $numero_documento;
        $this->vulnerable = $vulnerable;
        $this->tipo_vulnerabilidad = $tipo_vulnerabilidad;
        $this->id_familia = $id_familia;
        
        $this->es_embarazada = $es_embarazada;
        $this->tiene_discapacidad = $tiene_discapacidad;
        $this->enfermedad_cronica = $enfermedad_cronica;
    }
}