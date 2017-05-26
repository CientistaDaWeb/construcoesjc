<?php

class Usuarios_Model extends WS_Model {

    protected $_papel, $_ponto;

    public function __construct() {
        $this->_db = new Usuarios_Db();
        $this->_pair = 'nome';
        $this->_title = 'Gerenciador de Usuários';
        $this->_singular = 'Usuário';
        $this->_plural = 'Usuários';
        $this->_layoutList = 'basic';
        $this->_layoutForm = false;
        $this->_papel = array(
            'U' => 'Não',
            'A' => 'Sim'
        );

        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('u' => 'usuarios'), array('*'));
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'usuario' => 'text',
            'nome' => 'text',
            'papel' => 'getKey'
        );
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'papel' => 'getOption',
            'usuario' => 'slug'
        );
    }

    public function setOrderFields() {
        $this->_orderFields = array(
            'nome' => 'asc',
            'usuario' => 'asc'
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'nome' => 'Nome',
            'usuario' => 'Usuario',
            'papel' => 'Administrador'
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'nome' => array(
                'type' => 'Text',
                'label' => 'Nome',
                'required' => true
            ),
            'usuario' => array(
                'type' => 'Text',
                'label' => 'Usuário',
                'required' => true
            ),
            'senha' => array(
                'type' => 'Password',
                'label' => 'Senha'
            ),
            'cargo' => array(
                'type' => 'Text',
                'label' => 'Cargo'
            ),
            'telefone' => array(
                'type' => 'Phone',
                'label' => 'Telefone',
                'required' => true
            ),
            'email' => array(
                'type' => 'Mail',
                'label' => 'E-mail',
                'required' => true
            ),
            'papel' => array(
                'type' => 'Select',
                'label' => 'Administrador',
                'option' => array('' => 'Selecione'),
                'options' => $this->listOptions('papel')
            )
        );
    }

    public function adjustToDb($data) {
        if ($data['senha']):
            $data['senha'] = sha1($data['senha']);
        else:
            unset($data['senha']);
        endif;
        return parent::adjustToDb($data);
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $consulta = $db->select()
                ->from('usuarios', array('id', 'nome'))
                ->order('nome');
        return $db->fetchPairs($consulta);
    }

    public static function fetchPairPonto() {
        $db = WS_Model::getDefaultAdapter();
        $consulta = $db->select()
                ->from('usuarios', array('id', 'nome'))
                ->where('ponto = "S"')
                ->order('nome');
        return $db->fetchPairs($consulta);
    }

    public function findByToken($token) {
        $sql = $this->_db->select()
                ->from(array('u' => 'usuarios'), array('id', 'nome'))
                ->where('u.token_ponto = ?', $token);

        return $sql->query()->fetch();
    }

}
