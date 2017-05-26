<?php

class Erp_FuncionariosController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Funcionarios_Model();
        $this->form = WS_Form_Generator::generateForm('Funcionários', $this->model->getFormFields());
        parent::init();
    }

    public function indexAction() {
        parent::indexAction();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

}