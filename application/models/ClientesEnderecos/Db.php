<?php

class ClientesEnderecos_Db extends Erp_Db_Table {

    protected $_name = 'clientes_enderecos';
    protected $_referenceMap = array(
        'Cliente' => array(
            'refTableClass' => 'Clientes_Db',
            'refColumns' => array('id'),
            'columns' => array('cliente_id')
        ),
        'Categoria' => array(
            'refTableClass' => 'EnderecosCategorias_Db',
            'refColumns' => array('id'),
            'columns' => array('categoria_id')
        ),
        'Estado' => array(
            'refTableClass' => 'Estados_Db',
            'refColumns' => array('id'),
            'columns' => array('estado_id')
        )
    );

}
