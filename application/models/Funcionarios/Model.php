<?php

class Funcionarios_Model extends WS_Model {

    protected $_ativo;

    public function __construct() {
        $this->_db = new Funcionarios_Db();
        $this->_pair = 'nome';
        $this->_title = 'Gerenciador de Funcionários';
        $this->_singular = 'Funcionário';
        $this->_plural = 'Funcionários';
        $this->_layoutForm = false;
        $this->_layoutList = 'basic';
        $this->_primary = 'f.id';

        $this->_ativo = array(
            'S' => 'Sim',
            'N' => 'Não',
        );
        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('f' => 'funcionarios'), array('*'));
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $sql = $db->select()
                ->from('funcionarios', array('id', 'nome'))
                ->order('nome');
        return $db->fetchPairs($sql);
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'data_nascimento' => 'date',
            'data_admissao' => 'date',
            'data_demissao' => 'date',
            'ativo' => 'getOption',
        );
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'f.nome' => 'text',
            'f.telefone' => 'text',
            'f.cargo' => 'text',
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'nome' => array(
                'label' => 'Nome',
                'type' => 'Text',
                'required' => true,
            ),
            'data_nascimento' => array(
                'label' => 'Data de Nascimento',
                'type' => 'Date',
                'required' => true,
            ),
            'endereco' => array(
                'label' => 'Endereço',
                'type' => 'Text',
                'required' => true,
            ),
            'telefone' => array(
                'label' => 'Telefone',
                'type' => 'Phone',
                'required' => true,
            ),
            'rg' => array(
                'label' => 'RG',
                'type' => 'Text',
                'required' => true,
            ),
            'cpf' => array(
                'label' => 'CPF',
                'type' => 'Cpf',
                'required' => true,
            ),
            'ctps' => array(
                'label' => 'CTPS',
                'type' => 'Text',
                'required' => true,
            ),
            'cbo' => array(
                'label' => 'CBO',
                'type' => 'Text',
                'required' => true,
            ),
            'pis' => array(
                'label' => 'PIS',
                'type' => 'Text',
                'required' => true,
            ),
            'numero_registro' => array(
                'label' => 'Número do Registro',
                'type' => 'Text',
                'required' => true,
            ),
            'cargo' => array(
                'label' => 'Cargo',
                'type' => 'Text',
                'required' => true,
            ),
            'horario_entrada' => array(
                'label' => 'Horário de Entrada',
                'type' => 'Hour',
                'required' => true,
                'value' => '07:00:00',
            ),
            'horario_saida' => array(
                'label' => 'Horário de Saída',
                'type' => 'Hour',
                'required' => true,
                'value' => '17:00:00',
            ),
            'intervalo_saida' => array(
                'label' => 'Horário de Saída para Intervalo',
                'type' => 'Hour',
                'required' => true,
                'value' => '11:45:00',
            ),
            'intervalo_entrada' => array(
                'label' => 'Horário de Entrada para Intervalo',
                'type' => 'Hour',
                'required' => true,
                'value' => '13:00:00',
            ),
            'salario' => array(
                'label' => 'Salário',
                'type' => 'Money',
                'required' => true,
            ),
            'carga_horaria' => array(
                'label' => 'Carga Horária',
                'type' => 'Number',
                'required' => true,
                'value' => '220',
            ),
            'data_admissao' => array(
                'label' => 'Data de Admissão',
                'type' => 'Date',
                'required' => true,
            ),
            'data_demissao' => array(
                'label' => 'Data de Demissão',
                'type' => 'Date',
            ),
            'ativo' => array(
                'label' => 'Ativo',
                'type' => 'Select',
                'options' => $this->_ativo,
                'required' => true,
            ),
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'nome' => 'Nome',
            'telefone' => 'Telefone',
            'cargo' => 'Cargo',
        );
    }

    public function Relatorio($data) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('f' => 'funcionarios'), array('*'))
                ->order('f.nome');

        if (!empty($data['admissao_data_inicial'])):
            $consulta->where('f.data_admissao >= ?', WS_Date::adjustToDb($data['admissao_data_inicial']));
        endif;

        if (!empty($data['admissao_data_final'])):
            $consulta->where('f.data_admissao <= ?', WS_Date::adjustToDb($data['admissao_data_final']));
        endif;

        if (!empty($data['demissao_data_inicial'])):
            $consulta->where('f.data_demissao >= ?', WS_Date::adjustToDb($data['demissao_data_inicial']));
        endif;

        if (!empty($data['demissao_data_final'])):
            $consulta->where('f.data_demissao <= ?', WS_Date::adjustToDb($data['demissao_data_final']));
        endif;

        if (!empty($data['ativo'])):
            $consulta->where('f.ativo = ?', $data['ativo']);
        endif;

        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

}