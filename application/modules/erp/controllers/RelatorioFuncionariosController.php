<?php

class Erp_RelatorioFuncionariosController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Funcionarios_Model();
        parent::init();
        $this->model->_title = 'Relatório de Funcionários';
    }

    public function indexAction() {
        $data['admissao_data_inicial'] = '';
        $data['admissao_data_final'] = '';
        $data['demissao_data_inicial'] = '';
        $data['demissao_data_final'] = '';
        $data['ativo'] = '';

        if ($this->_request->isPost()):
            $data = $this->_request->getParams();
            $items = $this->model->Relatorio($data);
            $this->view->items = $items;
        endif;
        $this->view->data = $data;
    }

}