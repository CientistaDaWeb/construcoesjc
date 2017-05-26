<?php

class Erp_OrdensServicoController extends Erp_Controller_Action {

    public function init() {
        $this->model = new OrdensServico_Model();
        $this->form = WS_Form_Generator::generateForm('OrdensServico', $this->model->getFormFields());
        unset($this->buttons['add']);
        parent::init();
    }

    public function formularioAction() {
        $orcamento_id = $this->_request->getParam('parent_id');
        if (!empty($orcamento_id)):
            $this->model->_params['orcamento_id'] = $orcamento_id;
            $id = $this->_request->getParam('id');
            if (empty($id)):
                $sequencial = $this->model->getSequencial($orcamento_id);
                if (!empty($sequencial)):
                    $this->model->_params['sequencial'] = $sequencial['sequencial'] + 1;
                else:
                    $this->model->_params['sequencial'] = 1;
                endif;
            endif;
        endif;
        parent::formularioAction();
    }

    public function orcamentoAction() {
        $orcamento_id = $this->_request->getParam('parent_id');
        $items = $this->model->buscarPorOrcamento($orcamento_id);
        $this->view->items = $items;
    }

    public function visualizarAction() {
        $id = $this->_request->getParam('id');
        $item = $this->model->find($id);

        $ServicosModel = new Servicos_Model();
        $servicos = $ServicosModel->buscarPorOrdemServico($id);

        $ClientesEnderecosModel = new ClientesEnderecos_Model();
        $enderecos = $ClientesEnderecosModel->buscarPorCliente($item['cliente_id']);

        $ClientesTelefonesModel = new ClientesTelefones_Model();
        $telefones = $ClientesTelefonesModel->buscarPorCliente($item['cliente_id']);

        $this->view->ordemServico = $item;
        $this->view->enderecos = $enderecos;
        $this->view->telefones = $telefones;
        $this->view->servicos = $servicos;
    }

    public function clienteAction() {
        $cliente_id = $this->_request->getParam('parent_id');
        $items = $this->model->buscarPorCliente($cliente_id);
        $this->view->items = $items;
    }

}