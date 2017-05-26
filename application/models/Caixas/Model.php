<?php

class Caixas_Model extends WS_Model {

    public function __construct() {
        $this->_db = new Caixas_Db();
        $this->_pair = 'caixa';
        $this->_title = 'Gerenciador de Caixas';
        $this->_singular = 'Caixa';
        $this->_plural = 'Caixas';
        $this->_primary = 'c.id';
        $this->_layoutList = 'basic';

        parent::__construct();
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $sql = $db->select()
                ->from('caixas', array('id', 'caixa'))
                ->order('caixa');
        return $db->fetchPairs($sql);
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'caixas'), array('*'))
                ->joinInner(array('e' => 'empresas'), 'e.id = c.empresa_id', array('empresa' => 'razao_social'));
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'c.caixa' => 'text',
            'c.agencia' => 'text',
            'e.empresa' => 'text',
            'c.conta_corrente' => 'text'
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'empresa_id' => array(
                'label' => 'Empresa',
                'type' => 'Hidden',
                'required' => true,
                'value' => '1',
            ),
            'caixa' => array(
                'label' => 'Nome do Caixa',
                'type' => 'Text',
                'required' => true
            ),
            'agencia' => array(
                'label' => 'Agência',
                'type' => 'Text',
            ),
            'conta_corrente' => array(
                'label' => 'Conta Corrente',
                'type' => 'Text',
            ),
            'carteira' => array(
                'label' => 'Carteira',
                'type' => 'Text',
            )
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'caixa' => 'Caixa',
            'agencia' => 'Agência',
            'conta_corrente' => 'Conta Corrente'
        );
    }

    public function buscarPorConta($agencia, $conta_corrente) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'caixas'), array('*'))
                ->joinInner(array('e' => 'empresas'), 'e.id = c.empresa_id', array('empresa_id' => 'id'))
                ->where('c.agencia = ?', $agencia)
                ->where('c.conta_corrente = ?', $conta_corrente);
        $item = $consulta->query()->fetch();
        return $item;
    }

}
