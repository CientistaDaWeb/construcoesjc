<?php

class Empresas_Model extends WS_Model {

    public function __construct() {
        $this->_db = new Empresas_Db();
        $this->_title = 'Gerenciador de Empresas';
        $this->_singular = 'Empresa';
        $this->_plural = 'Empresas';
        $this->_layoutList = 'basic';
        $this->_layoutForm = false;

        parent::__construct();
        parent::turningFemale();
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'razao_social' => 'Empresa',
            'documento' => 'CNPJ'
        );
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'razao_social' => 'text',
            'documento' => 'text'
        );
    }

    public function adjustToDb($data) {
        if(empty($data['logomarca'])):
            unset($data['logomarca']);
        endif;
        return parent::adjustToDb($data);
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $consulta = $db->select()
                ->from('empresas', array('id', 'razao_social'))
                ->order('razao_social');
        return $db->fetchPairs($consulta);
    }

    public function buscarDadosPorFormaPagamento($forma_pagamento_id){
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('fp'=>'formas_pagamento'), array('*'))
                ->joinInner(array('b'=>'bancos'), 'b.id = fp.banco_id', array('*'))
                ->joinInner(array('e'=>'empresas'),'e.id = b.empresa_id', array('*'))
                ->where('fp.id = ?', $forma_pagamento_id);
        $item = $consulta->query()->fetch();
        return $item;
    }

}