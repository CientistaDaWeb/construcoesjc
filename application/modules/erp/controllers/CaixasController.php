<?php

class Erp_CaixasController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Caixas_Model();
        $this->form = WS_Form_Generator::generateForm('Caixas', $this->model->getFormFields());
        parent::init();
    }

    public function indexAction() {
        parent::indexAction();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

}