<?php

class Erp_IndexController extends Erp_Controller_Action {

    public function indexAction() {

        $CRModel = new ContasReceber_Model();
        $CPModel = new ContasPagar_Model();
        $OSModel = new OrdensServico_Model();
        $OrcamentosModel = new Orcamentos_Model();

        /* Contas a Receber */
        $this->view->tituloContasReceber = $CRModel->getOption('plural');

        $contasReceberAtrasadas = $CRModel->contasAtrasadas();
        $this->view->contasReceberAtrasadas = $contasReceberAtrasadas;

        $contasReceberVencendo = $CRModel->contasVencendo();
        $this->view->contasReceberVencendo = $contasReceberVencendo;

        /* Contas a Pagar */
        $this->view->tituloContasPagar = $CPModel->getOption('plural');

        $contasPagarAtrasadas = $CPModel->contasAtrasadas();
        $this->view->contasPagarAtrasadas = $contasPagarAtrasadas;

        $contasPagarVencendo = $CPModel->contasVencendo();
        $this->view->contasPagarVencendo = $contasPagarVencendo;

        /* Ordens de Serviço */
        $this->view->tituloOrdensServico = $OSModel->getOption('plural');

        $OSAtrasadas = $OSModel->osAtrasadas();
        $this->view->OSAtrasadas = $OSAtrasadas;

        $OSVencendo = $OSModel->osVencendo();
        $this->view->OSVencendo = $OSVencendo;


        /* Orçamentos */
        $this->view->tituloOrcamentos = $OrcamentosModel->getOption('plural');

        $orcamentos = $OrcamentosModel->orcamentosAbertos();
        $this->view->orcamentos = $orcamentos;

    }

}

