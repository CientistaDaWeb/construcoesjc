<?php

class AclAcessos_Model extends Zend_Acl {

    public function __construct() {

        // Papeis = Role = Grupos
        $this->addRole(new Zend_Acl_Role("V"));
        $this->addRole(new Zend_Acl_Role("U"), 'V');
        $this->addRole(new Zend_Acl_Role("A"), 'U');

        // Recursos
        $this->add(new Zend_Acl_Resource('erp:Index'));
        $this->add(new Zend_Acl_Resource('erp:Auth'));
        $this->add(new Zend_Acl_Resource('erp:Permissao-Negada'));

        //Adiciona a categorias de módulos do banco nos Resources
        $categoriasDB = new ModulosCategorias_Db();
        $categorias = $categoriasDB->fetchAll()->toArray();
        if (!empty($categorias)):
            foreach ($categorias AS $categoria):
                $this->add(new Zend_Acl_Resource('erp:categoria' . $categoria['id']));
            endforeach;
        endif;

        //Adiciona os módulos do banco nos Resources
        $modulosDb = new Modulos_Db();
        $modulos = $modulosDb->fetchAll()->toArray();
        if (!empty($modulos)):
            foreach ($modulos AS $modulo):
                if (!$this->has("erp:" . $modulo['controller'])):
                    $this->add(new Zend_Acl_Resource('erp:' . $modulo['controller']));
                endif;
            endforeach;
        endif;

        // Privilegios
        $this->allow("V", "erp:Index");
        $this->allow("V", "erp:Auth");
        $this->allow("V", "erp:Permissao-Negada");
        //Se o tipo de usuario for usuario
        $auth = new WS_Auth('erp');
        if ($auth->hasIdentity()):
            $usuario = $auth->getIdentity();
            if ($usuario->papel == 'U'):
                $aclSistema = new AclAcessoSistema_Model();
                //pega permissões dos grupos de módulos que pode acessar
                $permissoes = $aclSistema->getPermissionsGroup($usuario->id);
                if (!empty($permissoes)):
                    foreach ($permissoes AS $permissao):
                        if (!$this->hasRole("erp:categoria" . $permissao['id'])):
                            $this->allow("U", "erp:categoria" . $permissao['id']);
                        endif;
                    endforeach;
                endif;

                //pega permissões dos módulos que pode acessar
                $permissoes = $aclSistema->getPermissions($usuario->id);

                if (!empty($permissoes)):
                    foreach ($permissoes AS $permissao):
                        if (!$this->hasRole("erp:" . $permissao['controller'])):
                            $this->allow("U", "erp:" . $permissao['controller']);
                        endif;
                    endforeach;
                endif;
            endif;
        endif;
        /* Submodulos */
        /* Resources */
        $this->add(new Zend_Acl_Resource('erp:Acl-Acesso-Sistema'));
        $this->add(new Zend_Acl_Resource('erp:Clientes-Enderecos'));
        $this->add(new Zend_Acl_Resource('erp:Clientes-Telefones'));
        $this->add(new Zend_Acl_Resource('erp:Clientes-Crm'));
        $this->add(new Zend_Acl_Resource('erp:Notas-Fiscais'));
        $this->add(new Zend_Acl_Resource('erp:Templates'));
        $this->add(new Zend_Acl_Resource('erp:Ponto'));
        $this->add(new Zend_Acl_Resource('erp:Consulta'));
        //$this->add(new Zend_Acl_Resource('erp:Textos'));
        /* Permissoes */
        $this->allow("A", "erp:Acl-Acesso-Sistema");
        $this->allow("U", "erp:Clientes-Enderecos");
        $this->allow("U", "erp:Clientes-Telefones");
        $this->allow("U", "erp:Clientes-Crm");
        $this->allow("U", "erp:Notas-Fiscais");
        $this->allow("U", "erp:Templates");
        $this->allow("U", "erp:Ponto");
        $this->allow("U", "erp:Consulta");
        //$this->allow("U", "erp:Textos");

        $this->deny("U", "erp:Modulos");
        $this->allow("A", "erp:Modulos");
        $this->allow("A");
    }

}

