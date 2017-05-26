<?php

class Bens_Model extends WS_Model {

    public function __construct() {
        $this->_db = new Bens_Db();
        $this->_pair = 'nome';
        $this->_title = 'Gerenciador de Investimentos';
        $this->_singular = 'Investimento';
        $this->_plural = 'Investimentos';

        parent::__construct();
    }

    public function adjustToView($data, $comparacao = NULL, $inicial = NULL) {
        $depreciado = $this->recalculaValor($data, $comparacao, $inicial);
        $data['valor_atual'] = $depreciado['total'];
        $data['meses'] = $depreciado['meses'];
        $data['valor_depreciado'] = $data['valor_compra'] - $data['valor_atual'];
        $data['depreciacao'] = $data['valor_compra'] / $data['meses_quitacao'];

        return parent::adjustToView($data);
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'nome' => array(
                'label' => 'Nome do Bem',
                'type' => 'Text',
                'required' => true
            ),
            'valor_compra' => array(
                'label' => 'Valor de Compra',
                'type' => 'Money',
                'required' => true
            ),
            'data_aquisicao' => array(
                'label' => 'Data de Aquisição',
                'type' => 'Date',
                'required' => true
            ),
            'meses_quitacao' => array(
                'label' => 'Meses para retorno do investimento',
                'type' => 'Number',
                'required' => true
            ),
        );
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'data_aquisicao' => 'date',
        );
    }

    public function recalculaValor($data, $final = NULL, $inicial = NULL) {
        if (empty($final)):
            $inicioMes = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $comparacao = date('Y-m-d', $inicioMes);
        endif;
        $hoje = new Zend_Date($final);
        $compra = new Zend_Date($data['data_aquisicao']);
        if (!empty($inicial)):
            $dtini = new Zend_Date($inicial);
            $dtini->sub(3, Zend_Date::DAY);
            if ($dtini->compare($compra) > 0):
                $compra = $dtini;
            endif;
        endif;

        $diferenca = $hoje->sub($compra)->toString(Zend_Date::TIMESTAMP);
        $fator = floor(((($diferenca / 60) / 60) / 24) / 30); // meses

        if ($fator <= $data['meses_quitacao']):
            $depreciacao = $data['valor_compra'] / $data['meses_quitacao'];
            $retorno['total'] = $data['valor_compra'] - ($fator * $depreciacao);
            $retorno['meses'] = $fator;
        else:
            $retorno['total'] = 0;
            $retorno['meses'] = $data['meses_quitacao'];
        endif;
        return $retorno;
    }

    public function getByPeriod($data) {
        $itens = $this->listagem();
        $return = 0;
        foreach ($itens AS $item):
            $investimento = $this->recalculaValor($item, $data);
            $return += $investimento['total'];
        endforeach;
        return $return;
    }

}
