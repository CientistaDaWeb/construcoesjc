<?php

class Erp_RelatorioNotasFiscaisController extends Erp_Controller_Action {

    public function init() {
        $this->model = new NotasFiscais_Model();
        parent::init();
        $this->model->_title = 'Relatório de Notas Fiscais';
    }

    public function indexAction() {
        $data['data_inicial'] = date('d/m/Y', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
        $data['data_final'] = date('d/m/Y');
        $data['empresa_id'] = '';
        $data['tipo'] = '';

        if ($this->_request->isPost()):
            $data = $this->_request->getParams();

            /* Limita a data final da pesquisa quando não for administrador */
            $auth = new WS_Auth('erp');
            $user = $auth->getIdentity();
            if ($user->papel != 'A'):
                /* Limita a data inicial */
                $data = Erp_Date::dateLimit($data, 35, 'data_inicial');
            endif;

            $items = $this->model->relatorio($data);
            $this->view->items = $items;
        endif;
        $this->view->data = $data;
    }

}