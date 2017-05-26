<?php

class Textos_Db extends Erp_Db_Table {

    protected $_name = 'textos';
    protected $_referenceMap = array(
        'Categorias' => array(
            'refTableClass' => 'TextosCategorias',
            'refColumns' => array('id'),
            'columns' => array('categoria_id')
        )
    );
}
