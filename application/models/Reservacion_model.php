<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Reservacion_model extends CI_Model {

    public $fecha;
    public $id_local;
    public $id_cliente;


    public function set_datos( $data_cruda ){

        foreach( $data_cruda as $nombre_campo => $valor_campo ){
            if( property_exists( 'Reservacion_model', $nombre_campo  ) ){
                $this->$nombre_campo = $valor_campo;
            }
        }
        return $this;
    }

}
