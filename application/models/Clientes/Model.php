<?php

class Clientes_Model extends WS_Model {

    protected $_tipo;

    public function __construct() {
        $this->_db = new Clientes_Db();
        $this->_title = 'Gerenciador de Clientes';
        $this->_singular = 'Cliente';
        $this->_plural = 'Clientes';
        $this->_layoutForm = false;
        $this->_layoutList = 'basic';
        $this->_primary = 'c.id';

        $this->_documento_tipo = array(
            1 => 'Pessoa Jurídica',
            2 => 'Pessoa Física'
        );

        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'clientes'), array('*'))
                ->joinLeft(array('o' => 'orcamentos'), 'c.id = o.cliente_id', array('orcamentos' => 'COUNT(o.id)'))
                ->joinLeft(array('ct' => 'clientes_telefones'), 'c.id = ct.cliente_id', array(''))
                ->group('c.id')
        ;
    }

    public function setOrderFields() {
        $this->_orderFields = array(
            'c.razao_social' => 'asc'
        );
    }

    public function setFormFields() {
        $this->_formFields = array(
            'id' => array(
                'type' => 'Hidden'
            ),
            'documento_tipo' => array(
                'type' => 'Select',
                'label' => 'Tipo de Cliente',
                'options' => self::listOptions('documento_tipo'),
                'required' => true
            ),
            'documento' => array(
                'type' => 'Cnpj',
                'label' => 'CPF/CNPJ',
                'required' => true
            ),
            'razao_social' => array(
                'type' => 'Text',
                'label' => 'Razão Social/Nome',
                'required' => true
            ),
            'nome_fantasia' => array(
                'type' => 'Text',
                'label' => 'Nome Fantasia',
                'required' => true
            ),
            'contato' => array(
                'type' => 'Text',
                'label' => 'Contato',
                'required' => true
            ),
            'email' => array(
                'type' => 'Mail',
                'label' => 'E-mail',
                'required' => true
            ),
            'email_cobranca' => array(
                'type' => 'Mail',
                'label' => 'E-mail de Cobrança',
                'required' => true
            ),
            'site' => array(
                'type' => 'Url',
                'label' => 'Site',
            ),
            'observacoes' => array(
                'label' => 'Observações',
                'type' => 'Textarea',
                'required' => false,
            ),
        );
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'razao_social' => 'Razão Social/Nome',
            'contato' => 'Contato',
            'telefones' => 'Telefones',
            'enderecos' => 'Endereços',
            'orcamentos' => 'Nº Orç.',
        );
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            'c.razao_social' => 'text',
            'c.contato' => 'text',
            'ct.telefone' => 'text',
        );
    }

    public static function fetchPair() {
        $db = WS_Model::getDefaultAdapter();
        $consulta = $db->select()
                ->from('clientes', array('id', 'razao_social'))
                ->order('razao_social');
        return $db->fetchPairs($consulta);
    }

    public function adjustToView($data) {
        $ClientesTelefonesModel = new ClientesTelefones_Model();
        $ClientesEnderecosModel = new ClientesEnderecos_Model();

        $telefones = $ClientesTelefonesModel->buscaTelefones($data['id']);
        $data['telefones'] = join(' | ', $this->arrayImplode($telefones));

        $enderecos = $ClientesEnderecosModel->getSumByClient($data['id']);
        $data['enderecos'] = $enderecos['total'];

        if ($data['enderecos'] == 0):
            $data['enderecos'] = 'Não possui';
        endif;

        return parent::adjustToView($data);
    }

    public function ajusteRelatorioCondominio($data) {
        $ClientesTelefonesModel = new ClientesTelefones_Model();
        $telefones = $ClientesTelefonesModel->buscaTelefones($data['id']);
        $data['telefones'] = join(' | ', $this->arrayImplode($telefones));

        $data['enderecos'] = '';
        $item['endereco'] = '';
        $clientesEnderecosModel = new ClientesEnderecos_Model();
        $enderecos = $clientesEnderecosModel->buscarPorCliente($data['id']);
        if (!empty($enderecos)):
            foreach ($enderecos AS $endereco):
                $item['endereco'] .= $endereco['endereco'] . ', ' . $endereco['numero'] . ' ' . $endereco['complemento'] . ' - ' . $endereco['bairro'] . ' | ' . $endereco['cidade'] . ' - ' . $endereco['uf'] . '<br />';
            endforeach;
            $data['enderecos'] = $item['endereco'];
        endif;
        return $data;
    }

    public function buscarPorOrcamento($orcamento_id) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'clientes'), array('email', 'contato', 'cliente_id' => 'id'))
                ->joinInner(array('o' => 'orcamentos'), 'c.id = o.cliente_id', array(''))
                ->where('o.id = ?', $orcamento_id);
        $item = $consulta->query()->fetch();
        return $item;
    }

    public function relatorioAdministradores($administrador_id = '') {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'clientes'), array('*'));
        if (!empty($administrador_id)):
            $consulta->where('c.administrador_id = ?', $administrador_id);
        endif;
        $consulta->order('c.razao_social');

        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

    public function geraRandomico() {
        $CaracteresAceitos = '0123456789';
        $max = strlen($CaracteresAceitos) - 1;
        $password = null;
        for ($i = 0; $i < 4; $i++) {
            $password .= $CaracteresAceitos{mt_rand(0, $max)};
        }
        return $password;
    }

    public function relatorio() {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('c' => 'clientes'), array('razao_social', 'id'))
                ->joinLeft(array('ac' => 'administradores_condominios'), 'ac.id = c.administrador_id', array('administrador' => 'nome'))
                ->order('c.razao_social');
        return $consulta->query()->fetchAll();
    }

    public function ajusteRelatorio($data) {
        $OrdensServicoModel = new OrdensServico_Model();
        $OrdemServico = $OrdensServicoModel->getLastByClient($data['id']);
        if (!empty($OrdemServico['data'])):
            $data['ordem_servico'] = WS_Date::adjustToView($OrdemServico['data']);
        else:
            $data['ordem_servico'] = '';
        endif;
        if (!empty($OrdemServico['data_emissao'])):
            $data['orcamento'] = WS_Date::adjustToView($OrdemServico['data_emissao']);
        else:
            $data['orcamento'] = '';
        endif;
        return $data;
    }

}