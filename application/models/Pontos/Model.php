<?php

class Pontos_Model extends WS_Model {

    //Marcele - bo7wa611lrstckxilwnjj25d4ntmcdst1390e1gbuzp3s
    //Camila  - p3650w29av7qt3x6ps9mtk12ywm7mff5p7p3rmrszb23l
    //Ana     - i5poaymlhbkr3fu4alqydtjxrfm2wj5dn9wv0x8doqehk

    public $faixas;

    public function __construct() {
        $this->_db = new Pontos_Db();
        $this->_title = 'Gerenciador de Pontos';
        $this->_singular = 'Ponto';
        $this->_plural = 'Pontos';
        $this->_layoutList = 'basic';
        $this->_primary = 'lp.id';
        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('lp' => 'livro_pontos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'lp.usuario_id = u.id', array('nome'));
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'nome' => 'text',
            'data' => 'date',
        );
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'data' => 'date',
            'hora_inicial' => 'hour',
            'hora_final' => 'hour',
        );
    }

    public function setOrderFields() {
        $this->_orderFields = array(
            'data' => 'desc',
            'hora_inicial' => 'desc',
            'hora_final' => 'desc',
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'nome' => 'Nome',
            'data' => 'Data',
            'hora_inicial' => 'Hora de Entrada',
            'hora_final' => 'Hora de Saida',
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'usuario_id' => array(
                'type' => 'Select',
                'label' => 'Usuário',
                'options' => Usuarios_Model::fetchPairPonto(),
                'required' => true
            ),
            'data' => array(
                'type' => 'Date',
                'label' => 'Data',
                'required' => true
            ),
            'hora_inicial' => array(
                'type' => 'Hour',
                'label' => 'Hora de Entrada',
                'required' => true
            ),
            'hora_final' => array(
                'type' => 'Hour',
                'label' => 'Hora de Saída'
            ),
        );
    }

    public function setFormBaterPonto() {
        $this->_formFields = array(
            'acao' => array(
                'type' => 'Hidden',
                'required' => 'true',
                'value' => 'confirmar'
            ),
            'id' => array(
                'type' => 'Hidden',
                'required' => true
            ),
        );
    }

    public function setFormIdentifyUser() {
        $this->_formFields = array(
            'token' => array(
                'type' => 'Password',
                'label' => 'Utilize o leitor Biométrico',
                'required' => true
            ),
        );
    }

    public function getLastPoint($usuario_id) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('lp' => 'livro_pontos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'lp.usuario_id = u.id', array('nome'))
                ->where('u.id = ?', $usuario_id)
                ->where('lp.hora_inicial IS NOT NULL')
                ->where('lp.hora_final IS NULL');
        return $sql->query()->fetch();
    }

    public function relatorio($data) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('lp' => 'livro_pontos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'lp.usuario_id = u.id', array('nome'))
                ->where('lp.hora_inicial IS NOT NULL')
                ->order('usuario_id asc')
                ->order('data asc')
                ->order('hora_inicial asc')
                ->order('hora_final asc');

        if (!empty($data['usuario_id'])):
            $sql->where('u.id = ?', $data['usuario_id']);
        endif;

        if (!empty($data['data_inicial'])):
            $sql->where('lp.data >= ?', WS_Date::adjustToDb($data['data_inicial']));
        endif;

        if (!empty($data['data_final'])):
            $sql->where('lp.data <= ?', WS_Date::adjustToDb($data['data_final']));
        endif;

        return $sql->query()->fetchAll();
    }

    public function adjustToDb($data) {
        if(empty($data['hora_final'])):
            unset($data['hora_final']);
        endif;
        return parent::adjustToDb($data);
    }

    public function adjustToRelatorio($data) {
        $data['horas'] = 0;
        $data['extra'] = 0;
        $data2 = $data;
        foreach ($this->_faixas AS $key => $horario):
            if ($data['hora_inicial'] <= $horario['saida']): //Define que horário usar
                if ($data['hora_final'] > $horario['saida']):
                    $data2['hora_final'] = $horario['saida'];
                endif;
                if ($data['hora_inicial'] < $horario['entrada']):
                    $data2['hora_inicial'] = $horario['entrada'];
                endif;
                $tempo = $this->getHours($data2);
                $data['horas'] = number_format($tempo['horas'], 2);
                $data['minutos'] = number_format($tempo['minutes_total'], 2);
                break;
            endif;
        endforeach;
        $data = parent::adjustToView($data);
        return $data;
    }

    public function getDays($data_inicial, $data_final) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('lp' => 'livro_pontos'), array('*'))
                ->where('lp.hora_final IS NOT NULL')
                ->where('lp.data >= ?', WS_Date::adjustToDb($data_inicial))
                ->where('lp.data <= ?', WS_Date::adjustToDb($data_final))
                ->group('lp.data');
        return $sql->query()->fetchAll();
    }

    public function getHoursWorked($data) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('lp' => 'livro_pontos'), array('*'))
                ->joinInner(array('u' => 'usuarios'), 'lp.usuario_id = u.id', array('nome'))
                ->where('lp.hora_inicial IS NOT NULL')
                ->where('lp.hora_final != "00:00:00"')
                ->where('u.id = ?', $data['usuario_id'])
                ->where('lp.data >= ?', WS_Date::adjustToDb($data['data_inicial']))
                ->where('lp.data <= ?', WS_Date::adjustToDb($data['data_final']));

        $horas = $sql->query()->fetchAll();
        $retorno['horas'] = 0;
        $retorno['minutos'] = 0;
        if (!empty($horas)):
            foreach ($horas AS $item):
                $item = $this->adjustToRelatorio($item);
                $retorno['horas'] += $item['horas'];
                $retorno['minutos'] += $item['minutos'];
            endforeach;
        endif;
        return $retorno;
    }

    public function adjustToRelatorioExtra($data) {
        $data['horas'] = 0;
        $data['extra'] = 0;
        $data2 = $data;
        foreach ($this->_faixas AS $key => $horario):
            if ($data['hora_inicial'] <= $horario['saida']): //Define que horário usar
                if ($data['hora_final'] >= $horario['saida']): // Caso ele fique mais tempo que o final do expediente
                    if ($data['hora_inicial'] >= $horario['entrada']): //caso ele chegue dentro do horário
                        $data2['hora_final'] = $horario['saida'];
                        $tempo = $this->getHours($data2);
                        $data['horas'] += number_format($tempo['horas'], 2);

                        $data2['hora_final'] = $data['hora_final'];
                        $data2['hora_inicial'] = $horario['saida'];
                        $tempo = $this->getHours($data2);
                        $data['extra'] += number_format($tempo['horas'], 2);
                    endif;
                else: //caso ele chegue antes do horário
                    echo 'chegou adiantado e ficou a mais ' . $data['hora_inicial'];
                endif;
            else: // Caso ele saia antes do expediente
                if ($data['hora_inicial'] >= $horario['entrada']): //caso ele chegue dentro do horário
                    $tempo = $this->getHours($data2);
                    $data['horas'] += number_format($tempo['horas'], 2);
                else: //caso ele chegue antes do horário
                    echo 'chegou adiantado e saiu antes ' . $data['hora_inicial'];
                endif;
            endif;
            break;
        endforeach;
        $data = parent::adjustToView($data);
        return $data;
    }

    public function getHours($data, $debug = false) {
        if ($debug):
            Zend_Debug::dump($data);
        endif;
        $return = WS_Date::dateDifference($data['data'] . ' ' . $data['hora_final'], $data['data'] . ' ' . $data['hora_inicial']);
        return $return;
    }

}
