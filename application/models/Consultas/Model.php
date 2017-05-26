<?php

class Consultas_Model extends WS_Model {

    public function __construct() {
        $this->_db = new Consultas_Db();
        $this->_pair = 'caixa';
        $this->_title = 'Gerenciador de Consultas';
        $this->_singular = 'Consulta';
        $this->_plural = 'Consultas';
        $this->_primary = 'c.id';
        $this->_layoutList = 'basic';

        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'consultas'), array('*'));
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'c.data' => 'date'
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'data' => array(
                'label' => 'Data',
                'type' => 'Date',
                'required' => true,
            ),
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'data' => 'Data',
        );
    }

}
