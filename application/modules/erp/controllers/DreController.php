<?php

class Erp_DreController extends Erp_Controller_Action {

    public function init() {
        parent::init();
        $this->model->_title = 'Demonstrativo de Resultado';
    }

    public function indexAction() {
        $data['data_inicial'] = date('d/m/Y', mktime(0, 0, 0, date('m') - 1, 1, date('Y')));
        $data['data_final'] = date('d/m/Y', mktime(0, 0, 0, date('m') - 1, 31, date('Y')));

        if ($this->_request->isPost()):
            $data = $this->_request->getParams();
            $ContasPagarModel = new ContasPagar_Model();
            $ContasReceberModel = new ContasReceber_Model();
            $BensModel = new Bens_Model();
            $NotasFiscaisModel = new NotasFiscais_Model();

            $dataInicial = WS_Date::adjustToDb($data['data_inicial']);
            $dataFinal = WS_Date::adjustToDb($data['data_final']);


            if (!empty($dataInicial) || !empty($dataFinal)):
                //$faturamento = $ContasReceberModel->getBillingByPeriod($dataInicial, $dataFinal);
                $faturamento = $NotasFiscaisModel->getSumByPeriod($dataInicial, $dataFinal);
                $data['faturamento'] = $faturamento['faturamento'];

                $investimentosInicial = $BensModel->getByPeriod($dataInicial);
                $investimentosFinal = $BensModel->getByPeriod($dataFinal);

                $data['depreciacao'] = $investimentosFinal - $investimentosInicial;

                $custo_fixo = $ContasPagarModel->getSumByPeriod($dataInicial, $dataFinal, '1', true);
                $custo_variavel = $ContasPagarModel->getSumByPeriod($dataInicial, $dataFinal, '2', true);

                $despesasCategoria = $ContasPagarModel->getSumByPeriodCategory($dataInicial, $dataFinal);

                $valor_retido = $ContasReceberModel->getRetainedByPeriod($dataInicial, $dataFinal);

                $data['custo_fixo'] = $custo_fixo['custo'];
                $data['custo_variavel'] = $custo_variavel['custo'];
                $data['valor_retido'] = $valor_retido['retido'];
                $data['investimentos'] = $investimentosFinal;
                $data['despesasCategoria'] = $despesasCategoria;

                $data['bens'] = $BensModel->listagem();

                $data['contasReceber'] = $ContasReceberModel->getExtractBillingByPeriod($dataInicial, $dataFinal);

                $dados = array(
                    'data_inicial' => $data['data_inicial'],
                    'data_final' => $data['data_final']
                );
                $dados2 = array(
                    'data_inicial_lancamento' => $data['data_inicial'],
                    'data_final_lancamento' => $data['data_final']
                );

                $data['notasFiscais'] = $NotasFiscaisModel->relatorio($dados);
                $data['contasPagar'] = $ContasPagarModel->Relatorio($dados2);
            endif;
        endif;
        $this->view->data = $data;
    }

}