<?php

class Clientes_Db extends Erp_Db_Table {

    protected $_name = 'clientes';
    protected $_dependenceTables = array('Orcamentos_Db', 'ClientesEnderecos_Db', 'ClientesTelefones_Db');

    public function verifyToDel($id) {
        $query = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'clientes'), array(''))
                ->joinLeft(array('o' => 'orcamentos'), 'c.id = o.cliente_id', array('childs' => 'COUNT(o.id)'))
                ->group('c.id')
                ->where('c.id = ?', $id);
        $item = $query->query()->fetch();
        if ($item['childs'] > 0):
            return false;
        else:
            return true;
        endif;
    }

}
