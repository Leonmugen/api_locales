<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Local_model extends CI_Model {

    // Campos de la tabla locales
    public $id;
    public $nombre;
    public $capacidad;
    public $direccion;
    public $costo;
    public $descripcion;

    public function get_locales($id){

        $this->db->where(array('id' => $id));
        $query = $this->db->get('locales');
        $row = $query->custom_row_object(0, 'Local_model');

        if (isset($row)) {
            $row->id        = intval($row->id);
            $row->capacidad = intval($row->capacidad);
            $row->costo     = intval($row->costo);

        }
        return $row;
	}

    public function get_local($id){

        $this->db->where(array('id' => $id));
        $query = $this->db->get('locales');
        $row = $query->custom_row_object(0, 'Local_model');

        if (isset($row)) {
            $row->id        = intval($row->id);
            $row->capacidad = intval($row->capacidad);
            $row->costo     = intval($row->costo);

        }
        return $row;
    }


    // SELECT * FROM locales 
    // WHERE locales.id NOT IN(select id_local FROM renta_locales WHERE fecha_renta = '2019-11-20'); 
    public function get_locales_disponibes($data)
    {
        $query = " SELECT * FROM locales WHERE locales.id NOT IN(select id_local FROM ventas WHERE fecha = '".$data."')";
        $disponibilidad = $this->db->query($query);
        return $disponibilidad;
    }


    public function get_disponibilidad($data)
    {
        $this->db->where('id_local',$data['id_local']);
        $this->db->where('fecha',$data['fecha']);

        $query = $this->db->get('ventas');
        return $query->result();
        
    }

}
