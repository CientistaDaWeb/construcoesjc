<?php

class Servicos_Model extends WS_Model {

    public function __construct() {

        $this->_db = new Servicos_Db();
        $this->_title = 'Gerenciador de Serviços';
        $this->_singular = 'Serviço';
        $this->_plural = 'Serviços';
        $this->_primary = 's.id';
        $this->_layoutList = 'basic';

        parent::__construct();
    }

    public function setBasicSearch() {
        $this->_basicSearch = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('s' => 'servicos'), array('*'))
                ->joinInner(array('sc' => 'servicos_categorias'), 'sc.id = s.categoria_id', array('categoria'));
    }

    public function setViewFields() {
        $this->_viewFields = array(
            'id' => 'Código',
            'servico' => 'Serviço',
            'categoria' => 'Categoria',
            'unidade' => 'Unidade',
        );
    }

    public function setSearchFields() {
        $this->_searchFields = array(
            's.servico' => 'text',
            'sc.categoria' => 'text',
        );
    }

    public function buscarPorOrdemServico($ordem_servico_id) {
        $consulta = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'orcamentos_servicos'), array('preco'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array())
                ->joinInner(array('ors' => 'ordens_servico'), 'ors.orcamento_id = o.id', array())
                ->joinInner(array('s' => 'servicos'), 'os.servico_id = s.id', array('servico', 'id', 'unidade'))
                ->joinInner(array('sc' => 'servicos_categorias'), 's.categoria_id = sc.id', array('categoria'))
                ->joinLeft(array('oss' => 'ordens_servico_servicos'), 'oss.servico_id = os.servico_id AND ors.id = oss.ordem_servico_id', array('qtde', 'valor'))
                ->where('ors.id = ?', $ordem_servico_id)
                ->group('s.id');
        $itens = $consulta->query()->fetchAll();
        return $itens;
    }

    public function buscarPorOrcamento($orcamento_id) {
        $sql = $this->_db->select()
                ->setIntegrityCheck(false)
                ->from(array('os' => 'orcamentos_servicos'), array('qtde', 'preco'))
                ->joinInner(array('o' => 'orcamentos'), 'o.id = os.orcamento_id', array(''))
                ->joinInner(array('s' => 'servicos'), 's.id = os.servico_id', array('servico', 'id', 'unidade', 'descricao'))
                ->joinInner(array('sc' => 'servicos_categorias'), 'sc.id = s.categoria_id', array('categoria'))
                ->group('s.id')
                ->where('o.id = ?', $orcamento_id);
        $itens = $sql->query()->fetchAll();
        return $itens;
    }

}