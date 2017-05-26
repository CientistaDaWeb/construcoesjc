<?php

class FormasPagamento_Db extends Erp_Db_Table {

    protected $_name = 'formas_pagamento';
    protected $_referenceMap = array(
        'Banco' => array(
            'refTableClass' => 'Bancos',
            'refColumns' => array('id'),
            'columns' => array('banco_id')
        )
    );

}
