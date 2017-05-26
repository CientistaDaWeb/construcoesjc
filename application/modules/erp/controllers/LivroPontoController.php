<?php

class Erp_LivroPontoController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Pontos_Model();
        $this->form = WS_Form_Generator::generateForm('Pontos', $this->model->getFormFields());
        parent::init();
    }

    public function indexAction() {
        parent::indexAction();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

}