<?php

class Erp_ClientesController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Clientes_Model();
        $this->form = WS_Form_Generator::generateForm('Clientes', $this->model->getFormFields());
        parent::init();
    }

    public function indexAction() {
        parent::indexAction();
    }

    public function formularioAction() {
        $this->options['noList'] = true;

        parent::formularioAction();

        if (isset($this->view->data['documento_tipo'])):
            if ($this->view->data['documento_tipo'] == '2'):
                $this->model->_formFields['documento']['type'] = 'Cpf';
                $this->form = WS_Form_Generator::generateForm('Clientes', $this->model->getFormFields());
                $this->form->populate($this->view->data);
                $this->view->form = $this->form;
            endif;
        endif;

        if (!isset($this->view->data['usuario'])):
            $this->view->data['usuario'] = $this->model->gerarandomico();
            $this->view->data['senha'] = $this->model->gerarandomico();
            $this->form->populate($this->view->data);
            $this->view->form = $this->form;
        endif;

        if ($this->_hasParam('id')):
            $ClientesEnderecos = new ClientesEnderecos_Model();
            $this->view->enderecos = $ClientesEnderecos->fetchPair($this->_getParam('id'));
        endif;
    }

    public function arAction() {
        if ($this->_hasParam('id')):
            $EmpresaModel = new Empresas_Model();
            $empresa = $EmpresaModel->find(1);
            $EstadosModel = new Estados_Model();
            $estado = $EstadosModel->find($empresa['estado_id']);
            $empresa['uf'] = $estado['uf'];
            $this->view->empresa = $empresa;
            $id = $this->_getParam('id');
            $cliente = $this->model->find($id);
            $endereco_id = $this->_getParam('parent_id');
            $ClientesEnderecos = new ClientesEnderecos_Model();
            $endereco = $ClientesEnderecos->find($endereco_id);
            $estado = $EstadosModel->find($endereco['estado_id']);
            $endereco['uf'] = $estado['uf'];
            $cliente['endereco'] = $endereco;
            $cliente['endereco']['pais'] = 'BRASIL';
            $this->view->cliente = $cliente;

        endif;
    }

}