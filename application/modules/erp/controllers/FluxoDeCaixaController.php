<?php

class Erp_FluxoDeCaixaController extends Erp_Controller_Action {

    public function init() {
        parent::init();
        $this->model->_title = 'Relatório de Fluxo de Caixa';
    }

    public function indexAction() {
        $ContasPagarModel = new ContasPagar_Model();
        $ContasReceberModel = new ContasReceber_Model();

        $data = $this->_request->getParams();

        if (empty($data['dataInicial']['mes'])):
            $seisMeses = mktime(0, 0, 0, date('m') - 6, date('d'), date('Y'));
            $data['dataInicial']['mes'] = date('m', $seisMeses);
            $data['dataInicial']['ano'] = date('Y', $seisMeses);
        endif;
        if (empty($data['dataFinal']['mes'])):
            $data['dataFinal']['mes'] = date('m');
            $data['dataFinal']['ano'] = date('Y');
        endif;


        if ($this->_request->isPost()):
            $dataInicial = $data['dataInicial'];
            $dataFinal = $data['dataFinal'];

            if (!empty($dataInicial) || !empty($dataFinal)):
                $dataInicial['dia'] = '01';
                $dataFinal['dia'] = '31';

                $dataInicial = $dataInicial['ano'] . '-' . $dataInicial['mes'] . '-' . $dataInicial['dia'];
                $dataFinal = $dataFinal['ano'] . '-' . $dataFinal['mes'] . '-' . $dataFinal['dia'];
            endif;

            $contasReceber = $ContasReceberModel->buscarPorPeriodo($dataInicial, $dataFinal);
            $contasPagar = $ContasPagarModel->buscarPorPeriodo($dataInicial, $dataFinal);

            if (!empty($contaReceber) || !empty($contasPagar)):
                $contasCategorizadas = $ContasPagarModel->buscarPorCategoria($dataInicial, $dataFinal);


                $contasReceber = $ContasReceberModel->ajusteFluxo($contasReceber);
                $contasPagar = $ContasPagarModel->ajusteFluxo($contasPagar);

                $contas = array_merge_recursive($contasReceber, $contasPagar);
                $acumulado = array('pagar' => 0, 'receber' => 0, 'total' => 0);

                $acumulado['receber'] = $ContasReceberModel->buscarAcumulado($dataInicial);
                $acumulado['pagar'] = $ContasPagarModel->buscarAcumulado($dataInicial);
                $acumulado['total'] = $acumulado['receber']['soma'] - $acumulado['pagar']['soma'];
                $saldoAcumulado = $acumulado['total'];

                ksort($contas);
                foreach ($contas AS $key => $conta) {
                    $ZD = new Zend_Date($key . '-01');
                    if (!isset($conta['contas_pagar'])):
                        $conta['contas_pagar'] = 0;
                    endif;
                    if (!isset($conta['contas_receber'])):
                        $conta['contas_receber'] = 0;
                    endif;
                    $conta['saldo'] = $conta['contas_receber'] - $conta['contas_pagar'];
                    $conta['legenda'] = $ZD->toString('MM/yyyy');
                    $acumulado['total'] += $conta['saldo'];
                    $conta['acumulado'] = $acumulado['total'];
                    $contasAjustadas[$key] = $conta;
                }

                $this->view->contas = $contasAjustadas;
                $this->view->contasCategorizadas = $contasCategorizadas;
                $this->view->saldoAcumulado = $saldoAcumulado;
            endif;
        endif;
        $this->view->data = $data;
    }

}