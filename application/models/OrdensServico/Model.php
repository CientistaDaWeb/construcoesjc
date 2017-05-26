<?php

class OrdensServico_Model extends WS_Model {

    protected $_status;

    public function __construct() {
        $this->_db = new OrdensServico_Db();
        $this->_title = 'Gerenciador de Ordens de Serviço';
        $this->_singular = 'Ordem de Serviço';
        $this->_plural = 'Ordens de Serviço';
        $this->_primary = 'os.id';
        $this->_layoutList = 'basic';
        $this->_layoutForm = false;

        $this->_status = array(
            1 => 'Aguardando',
            2 => 'Executando',
            3 => 'Concluido'
        );

        parent::__construct();
        parent::turningFemale();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'ordens_servico'), array('*', 'saldo' => '(os.valor - os.desconto)'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array(''))
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('cliente' => 'razao_social', 'cliente_id' => 'id'))
                ->group('os.id')
                ->order('os.data DESC');
    }

    public function adjustToDb($data) {
        if (empty($data['valor'])):
            $data['valor'] = 0;
        endif;
        if (empty($data['desconto'])):
            $data['desconto'] = 0;
        endif;
        return parent::adjustToDb($data);
    }

    public function setAdjustFields() {
        $this->_adjustFields = array(
            'data' => 'date',
            'status' => 'getOption',
            'valor' => 'money',
            'desconto' => 'money',
            'saldo' => 'money',
        );
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'os.data' => 'date',
            'os.orcamento_id' => 'text',
            'c.razao_social' => 'text',
            'os.valor' => 'money'
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'codigo' => 'Código',
            'data' => 'Data',
            'cliente' => 'Cliente',
            'saldo' => 'Valor'
        );
    }

    public function setOrderFields() {
        $this->_orderFields = array(
            'data' => 'desc',
            'status' => 'asc',
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'sequencial' => array(
                'type' => 'Hidden'
            ),
            'orcamento_id' => array(
                'type' => 'Hidden',
                'required' => true
            ),
            'empresa_id' => array(
                'type' => 'Hidden',
                'label' => 'Empresa',
                'required' => true,
                'value' => '1',
            ),
            'data' => array(
                'type' => 'Date',
                'label' => 'Data do Serviço',
                'required' => true
            ),
            'valor' => array(
                'type' => 'Money',
                'label' => 'Valor'
            ),
            'desconto' => array(
                'type' => 'Money',
                'label' => 'Desconto'
            ),
            'status' => array(
                'type' => 'Select',
                'label' => 'Status',
                'options' => $this->listOptions('status'),
                'required' => true
            ),
            'observacoes' => array(
                'label' => 'Observações',
                'type' => 'Textarea',
                'required' => false,
            ),
        );
    }

    public function adjustToView(array $data) {
        $data['codigo'] = $data['orcamento_id'] . '.' . $data['sequencial'];
        switch ($data['status']):
            case 1:
                $WD = new WS_Date();
                $Vencimento = new WS_Date($data['data']);
                if ($Vencimento->compare($WD->today()) !== 1):
                    $data['class'] = 'atrasada';
                endif;
                break;
            case 2:
                $data['class'] = 'aberto';
                break;
            case 3:
                $data['class'] = 'pago';
                break;
            default:
                break;
        endswitch;
        return parent::adjustToView($data);
    }

    public function ajusteRelatorio(array $itens) {
        $WD = new WS_Date();
        $total = array('domestico' => 0, 'industrial' => 0, 'residuos' => 0);
        foreach ($itens AS $item):
            $var['item_status'] = $this->getOption('status', $item['status']);
            $var['item_codigo'] = $item['orcamento_id'] . '.' . $item['id'];
            $var['item_data'] = WS_Date::adjustToView($item['data']);
            $var['item_cliente'] = $item['razao_social'];
            $total['domestico'] += $var['item_volume_domestico'] = $this->volumeDomestico($item['id']);
            $total['industrial'] += $var['item_volume_industrial'] = $this->volumeIndustrial($item['id']);
            $total['residuos'] += $var['item_volume_residuos'] = $this->volumeResiduos($item['id']);
            $retorno[] = $var;
        endforeach;

        $retorno['total_domestico'] = $total['domestico'];
        $retorno['total_industrial'] = $total['industrial'];
        $retorno['total_residuos'] = $total['residuos'];

        return $retorno;
    }

    public function somaValores($orcamento_id) {
        $sql = $this->_db->select()
                ->from(array('os' => 'ordens_servico'), array('soma' => '(SUM(os.valor) - SUM(os.desconto))'))
                ->where('os.orcamento_id = ?', $orcamento_id);
        $item = $sql->query()->fetch();
        return (!empty($item)) ? $item : false;
    }

    public function relatorio($data) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'ordens_servico'), array('data', 'status', 'id', 'orcamento_id', 'sequencial'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array())
                ->joinInner(array('e' => 'empresas'), 'e.id = o.empresa_id', array('empresa' => 'razao_social'))
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('cliente_id' => 'id', 'cliente' => 'razao_social'))
                ->order('os.data ASC')
                ->group('os.id')
                ->where('os.data >= ?', WS_Date::adjustToDb($data['data_inicial']))
                ->where('os.data <= ?', WS_Date::adjustToDb($data['data_final']));

        if (!empty($data['status'])):
            $consulta->where('os.status = ?', $data['status']);
        endif;
        if (!empty($data['cliente_id'])):
            $consulta->where('c.id = ?', $data['cliente_id']);
        endif;
        if (!empty($data['empresa_id'])):
            $consulta->where('e.id = ?', $data['empresa_id']);
        endif;
        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

    public function getSequencial($orcamento_id) {
        $consulta = $this->_db->select()
                ->from(array('os' => 'ordens_servico'), array('sequencial' => 'MAX(sequencial)'))
                ->where('os.orcamento_id = ?', $orcamento_id);
        $item = $consulta->query()->fetch();
        return $item;
    }

    public function osAtrasadas() {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->joinInner(array('os' => 'ordens_servico'), array('id', 'sequencial', 'data', 'hora_coleta'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array())
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('razao_social'))
                ->where('os.data < ?', date('Y-m-d'))
                ->where('os.status = 1')
                ->order('os.data ASC');
        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

    public function osVencendo() {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->joinInner(array('os' => 'ordens_servico'), array('id', 'sequencial', 'data', 'hora_coleta'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array())
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('razao_social'))
                ->where('os.data <= ?', date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 15, date('Y'))))
                ->where('os.data >= ?', date('Y-m-d'))
                ->where('os.status = 1')
                ->order('os.data ASC');
        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

    public function buscarPorOrcamento($orcamento_id) {
        $sql = clone($this->_basicSearch);
        $sql->where('os.orcamento_id = ?', $orcamento_id)
                ->order('os.data ASC');
        $itens = $sql->query()->fetchAll();
        return $itens;
    }

    public function buscarDadosPorOrcamento($orcamento_id) {
        $consulta = $this->_db->select()
                ->from(array('os' => 'ordens_servico'), array('total' => 'COUNT(os.id)', 'soma' => 'SUM(os.valor)-SUM(os.desconto)'))
                ->where('os.orcamento_id = ?', $orcamento_id)
        ;
        $item = $consulta->query()->fetch();
        return $item;
    }

    public function find($ordem_servico_id) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'ordens_servico'), array('*'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array('cliente_id'))
                ->joinInner(array('e' => 'empresas'), 'e.id = o.empresa_id', array('empresa' => 'razao_social', 'endereco', 'numero', 'cidade', 'complemento', 'bairro', 'cep', 'telefone', 'telefone2', 'telefone3', 'logomarca', 'website', 'email', 'email2'))
                ->joinInner(array('es' => 'estados'), 'es.id = e.estado_id', array('uf'))
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('cliente' => 'razao_social', 'contato', 'sindico', 'zelador'))
                ->where('os.id = ?', $ordem_servico_id);

        $item = $consulta->query()->fetch();
        return $item;
    }

    public function buscarPorCliente($cliente_id) {
        $sql = clone($this->_basicSearch);
        $sql->where('o.cliente_id = ?', $cliente_id);
        $itens = $sql->query()->fetchAll();
        return $itens;
    }

    public function eventos($inicio, $fim) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'ordens_servico', array('id', 'data', 'hora_coleta')))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array())
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array('razao_social'))
                ->where('os.data >= ?', $inicio)
                ->where('os.data <= ?', $fim);
        $items = $sql->query()->fetchAll();
        return $items;
    }

    public function getLastByClient($cliente_id) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('o' => 'orcamentos'), array('data_emissao'))
                ->joinLeft(array('os' => 'ordens_servico'), 'o.id = os.orcamento_id', array('data'))
                ->joinInner(array('c' => 'clientes'), 'c.id = o.cliente_id', array(''))
                ->where('c.id = ?', $cliente_id)
                ->order('os.data DESC')
                ->order('o.data_emissao DESC')
                ->where('(os.status = 3 AND data != "") OR (data_emissao != "")')
                ->limit(1)
        ;
        return $sql->query()->fetch();
    }

}