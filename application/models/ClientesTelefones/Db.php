<?php

class ClientesTelefones_Db extends Erp_Db_Table {

    protected $_name = 'clientes_telefones';
    protected $_referenceMap = array(
        'Cliente' => array(
            'refTableClass' => 'Clientes_Db',
            'refColumns' => array('id'),
            'columns' => array('cliente_id')
        ),
        'Categoria' => array(
            'refTableClass' => 'TelefonesCategorias_Db',
            'refColumns' => array('id'),
            'columns' => array('categoria_id')
        )
    );

}
