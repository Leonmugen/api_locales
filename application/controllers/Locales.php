<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require( APPPATH.'/libraries/REST_Controller.php');

/*
* Es importante requerir el REST_Controller
* Es importante extender del REST_Controller
*/

class Locales extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->model('Local_model');
        $this->load->model('Reservacion_model');
    }

    // ==================================
    // Paginar clientes mediante helper
    // METHOD: GET
    // ==================================
    public function paginar_get(){

        $this->load->helper('paginacion');

        $pagina     = $cliente_id = $this->uri->segment(3); // parametro #3
        $por_pagina = $cliente_id = $this->uri->segment(4); // parametro #4

        $campos = array('id','nombre','capacidad', 'direccion', 'costo', 'descripcion'); // campos de la tabla

        $respuesta = paginar_todo( 'locales', $pagina, $por_pagina, $campos ); // helper
        $this->response( $respuesta );  // imprime el resultado de lo que se obtuvo
    }


    public function disponibles_get(){

            $fecha = $this->uri->segment(3);

            $respuesta = $this->Local_model->get_locales_disponibes($fecha);
            $this->response($respuesta->result());
    }

    public function disponibilidad_get(){
        $id = $this->uri->segment(3);
        $fecha = $this->uri->segment(4);
        
        $data = array(
            'id_local' => $id,
            'fecha' => $fecha
        );         
        
        $respuesta = $this->Local_model->get_disponibilidad($data);

        if (empty($respuesta)) {
            $msg = array(
                'mensaje' => 'El local esta disponible en la fecha elegida.',
            );
            $this->response($msg);
        }else{
            $msg = array(
                'mensaje' => 'El local no esta disponible en la fecha elegida.',
            );
            $this->response($msg);
        }
    }


    public function local_put(){
        $data = $this->put();   // guarda los campos posteados
        $this->load->library('form_validation');    // ayuda para validar los campos
        $this->form_validation->set_data( $data );

        $this->form_validation->set_rules('fecha','fecha','trim|required');
        $this->form_validation->set_rules('id_local','id_local','trim|required');
        $this->form_validation->set_rules('id_cliente','id_cliente','trim|required');



        if( $this->form_validation->run() ){ // TRUE = TODO BIEN

            //SOLO PARA QUE NO ESTE DUPLICADO
            $query = $this->db->get_where( 'ventas', array( 'fecha' => $data['fecha'] ) );
            $local_fecha = $query->row();
            $query = $this->db->get_where( 'ventas', array( 'id_local' => $data['id_local'] ) );
            $local_id = $query->row();
            $query = $this->db->get_where( 'ventas', array( 'id_cliente' => $data['id_cliente'] ) );
            $cliente_id = $query->row();

            if(isset( $local_fecha ) && isset( $local_id )){
                $respuesta = array(
                    'error' => TRUE,
                    'mensaje' => 'La reservacion ya está registrada'
                );

                $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST );
                return; // finaliza la ejecución del metodo
            }

            $local = $this->Reservacion_model->set_datos( $data ); // objeto de cliente model

            if( $this->db->insert( 'ventas', $local ) ){    // INSERTADO

                $respuesta = array(
                    'error' => FALSE,
                    'mensaje' => 'Se ha insertado el registro correctamente',
                    //'local_id' => $this->db->insert_id()  // CI Query builder function
                );

                $this->response( $respuesta );

            }else{  // NO INSERTADO

                $respuesta = array(
                    'error' => TRUE,
                    'mensaje' => 'Error en la base de datos',
                    'error_mensaje' => $this->db->_error_message(), // error en la db
                    'error_number' => $this->db->_error_number()    // num error db
                );

                $this->response( $respuesta, REST_Controller::HTTP_INTERNAL_SERVER_ERROR );  // error 500

            }


        }else{  // form validation FALSE

            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Hay errores en la información',
                'errores' => $this->form_validation->get_errores_arreglo()  // MY_Form_validation.php
            );

            $this->response( $respuesta, REST_Controller::HTTP_BAD_REQUEST ); // error

        }
    }


    public function local_get(){
    	
        $id = $this->uri->segment(3);

    	if (!isset($id)) {
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'Es necesario un ID del local.'
            );
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        $local = $this->Local_model->get_local($id);

        if (isset($id)) {
            $respuesta = array(
                'error' => FALSE,
                'mensaje' => 'Registro mostrado correctamente.',
                'Local' => $local
            );
            $this->response($respuesta);
        }else{
            $respuesta = array(
                'error' => TRUE,
                'mensaje' => 'El registro con el ID '.$id.' no existe.',
                'Local' => null
            );
            $this->response($respuesta, REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
