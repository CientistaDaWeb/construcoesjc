<?php

class UsuariosCompromissos_Model extends WS_Model {

    protected $_status;

    public function __construct() {
        $this->_db = new UsuariosCompromissos_Db();
        $this->_title = 'Gerenciador de Compromissos';
        $this->_singular = 'Compromisso';
        $this->_plural = 'Compromissos';

        $this->_status = array(
            'A' => 'Aguardando',
            'C' => 'Concluído'
        );

        parent::__construct();
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'data' => 'date',
            'status' => 'getOption',
            'cliente_id' => 'int',
        );
    }


    public function compromissos() {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'usuarios_compromissos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'u.id = c.usuario_id', array('nome'))
                ->where('data <= ?', date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 5, date('Y'))))
                ->where('data >= ?', date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))))
                ->where('status = "A"')
                ->order('data ASC')
                ->order('hora ASC');
        return $consulta->query()->fetchAll();
    }

    public function agenda($inicio, $fim) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'usuarios_compromissos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'u.id = c.usuario_id', array('nome'))
                ->where('c.data >= ?', $inicio)
                ->where('c.data <= ?', $fim);
        return $consulta->query()->fetchAll();
    }

    public function relatorio($data) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('uc' => 'usuarios_compromissos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'u.id = uc.usuario_id', array('nome'))
                ->where('uc.data >= ?', WS_Date::adjustToDb($data['data_inicial']))
                ->where('uc.data <= ?', WS_Date::adjustToDb($data['data_final']))
                ;

        if (!empty($data['status'])):
            $consulta->where('uc.status = ?', $data['status']);
        endif;

        if (!empty($data['usuario_id'])):
            $consulta->where('u.id = ?', $data['usuario_id']);
        endif;

        return $consulta->query()->fetchAll();
    }

}
