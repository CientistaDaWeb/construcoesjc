<?php

class Erp_ConsultasController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Consultas_Model();
        unset($this->buttons['del']);
        parent::init();
    }

    public function indexAction() {
        parent::indexAction();
    }

    public function formularioAction() {
        parent::formularioAction();
    }

    public function funcionarioAction(){

    }

}