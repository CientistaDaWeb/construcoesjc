<?php

class FormasPagamento_Model extends WS_Model {

    public function __construct() {
        $this->_db = new FormasPagamento_Db();
        $this->_title = 'Gerenciador de Formas de Pagamento';
        $this->_singular = 'Forma de Pagamento';
        $this->_plural = 'Formas de Pagamento';
        $this->_primary = 'fp.id';
        $this->_layoutList = 'basic';

        parent::__construct();
        parent::turningFemale();
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'fp.forma_pagamento' => 'text',
            'c.caixa' => 'text'
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'forma_pagamento' => 'Forma de Pagamento',
            'caixa' => 'Caixa'
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'caixa_id'=>array(
                'label' => 'Caixa',
                'type' => 'Select',
                'options' => Caixas_Model::fetchPair(),
                'required' => true
            ),
            'forma_pagamento' => array(
                'label' => 'Nome da Forma de Pagamento',
                'type' => 'Text',
                'required' => true
            ),
        );
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $consulta = $db->select()
                ->from('formas_pagamento', array('id', 'forma_pagamento'))
                ->order('forma_pagamento');
        return $db->fetchPairs($consulta);
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('fp' => 'formas_pagamento'), array('*'))
                ->joinInner(array('c' => 'caixas'), 'c.id = fp.caixa_id', array('caixa'));
    }

    public function buscaGeraRemessa() {
        $db = $this->getDefaultAdapter();
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('fp' => 'formas_pagamento'), array('id', 'forma_pagamento'))
                ->joinInner(array('c' => 'caixas'), 'c.id = fp.caixa_id', array('caixa'))
                ->joinInner(array('e' => 'empresas'), 'e.id = b.empresa_id', array('empresa' => 'razao_social'))
                ->where('fp.gera_remessa = "S"')
                ->order('fp.forma_pagamento');
        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

}
