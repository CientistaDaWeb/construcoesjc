<?php

class Erp_RelatorioOrdemServicoController extends Erp_Controller_Action {

    public function init() {
        $this->model = new OrdensServico_Model();
        parent::init();
        $this->model->_title = 'Relatório de ' . $this->model->getOption('plural');
    }

    public function indexAction() {
        $data['data_inicial'] = date('d/m/Y', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
        $data['data_final'] = date('d/m/Y');
        $data['status'] = '';
        $data['cliente_id'] = '';
        $data['empresa_id'] = '';

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

    public function pdfAction() {
        if ($this->_request->isPost()):
            $data = $this->_request->getParams();

            $document = $this->model->montaRelatorio($data, 'pdf');

            if ($document) :
                $this->getResponse()
                        ->setHeader('Content-Disposition', 'attachment; filename=Relatorio-de-Ordens-de-Servico.pdf')
                        ->setHeader('Content-type', 'application/x-pdf')
                        ->setBody($document);
            endif;
        endif;
    }

    public function xlsAction() {
        $data = $this->_request->getParams();
        $arquivo = $this->model->montaRelatorio($data, 'xls');
        $document = fopen($arquivo, 'r');

        $this->getResponse()
                ->setHeader('Content-Disposition', 'attachment; filename=Relatório-Fepan.csv')
                ->setHeader('Content-type', 'application/excel');
        readfile($arquivo);
    }

    public function impressaoAction() {
        $ids = $this->_getParam('os_id');

        foreach ($ids AS $id):
            $item = $this->model->find($id);
            $ServicosModel = new Servicos_Model();
            $item['servicos'] = $ServicosModel->buscarPorOrdemServico($id);
            $ClientesEnderecosModel = new ClientesEnderecos_Model();
            $item['enderecos'] = $ClientesEnderecosModel->buscarPorCliente($item['cliente_id']);
            $ClientesTelefonesModel = new ClientesTelefones_Model();
            $item['telefones'] = $ClientesTelefonesModel->buscarPorCliente($item['cliente_id']);

            $items[] = $item;
        endforeach;
        $this->view->noLogo = true;
        $this->view->items = $items;
    }

}