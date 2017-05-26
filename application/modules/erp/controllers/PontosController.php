<?php

class Erp_PontosController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Pontos_Model();
        $this->form = WS_Form_Generator::generateForm('Pontos', $this->model->getFormFields());
        parent::init();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

    public function funcionarioAction() {
        $id = $this->_request->getParam('id');
        $item = $this->model->find($id);
    }

}