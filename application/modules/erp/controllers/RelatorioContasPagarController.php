<?php

class Erp_RelatorioContasPagarController extends Erp_Controller_Action {

    public function init() {
        $this->model = new ContasPagar_Model();
        parent::init();
        $this->model->_title = 'Relatório de ' . $this->model->getOption('plural');
    }

    public function indexAction() {
        $data['data_inicial'] = date('d/m/Y', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
        $data['data_final'] = date('d/m/Y');
        $data['data_inicial_pago'] = '';
        $data['data_final_pago'] = '';
        $data['data_inicial_lancamento'] = '';
        $data['data_final_lancamento'] = '';
        $data['status'] = '';
        $data['categoria_id'] = '';
        $data['fornecedor_categoria_id'] = '';
        $data['fornecedor_id'] = '';
        $data['forma_pagamento_id'] = '';
        $data['conta_fixa'] = '';
        $data['empresa_id'] = '';
        $data['ordem'] = '';
        $data['ordem_tipo'] = '';

        if ($this->_request->isPost()):
            $data = $this->_request->getParams();

            /* Limita a data final da pesquisa quando não for administrador */
            $auth = new WS_Auth('erp');
            $user = $auth->getIdentity();
            if ($user->papel != 'A'):
                /* Limita a data inicial */
                $data = Erp_Date::dateLimit($data, 35, 'data_inicial');
                /* Limita a data de Pagamento */
                $data = Erp_Date::dateLimit($data, 35, 'data_inicial_pago');
                /* Limita a data de Lancamento */
                $data = Erp_Date::dateLimit($data, 35, 'data_inicial_lancamento');
            endif;

            $items = $this->model->Relatorio($data);
            $this->view->items = $items;
        endif;
        $this->view->data = $data;
    }

}