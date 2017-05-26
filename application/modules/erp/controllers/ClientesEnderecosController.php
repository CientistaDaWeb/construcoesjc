<?php

class Erp_ClientesEnderecosController extends Erp_Controller_Action {

    public function init() {
        $this->model = new ClientesEnderecos_Model();
        $this->form = WS_Form_Generator::generateForm('ClientesEnderecos', $this->model->getFormFields());
        parent::init();
    }

    public function clienteAction() {
        $cliente_id = $this->_request->getParam('parent_id');
        $items = $this->model->buscarPorCliente($cliente_id);
        $this->view->items = $items;
    }

    public function formularioAction() {
        $cliente_id = $this->_request->getParam('parent_id');
        if (!empty($cliente_id)):
            $this->model->_params['cliente_id'] = $cliente_id;
        endif;

        parent::formularioAction();
    }

}