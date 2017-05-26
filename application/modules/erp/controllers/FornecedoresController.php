<?php

class Erp_FornecedoresController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Fornecedores_Model();
        $this->form = new Fornecedores_Form();
        parent::init();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

}