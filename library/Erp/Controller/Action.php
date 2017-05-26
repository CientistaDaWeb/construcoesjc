<?php

class Erp_Controller_Action extends WS_Controller_Action {

    public function init() {
        parent::init();

        $auth = new WS_Auth('erp');

        if (($this->_request->getControllerName() != 'Auth') && ($this->_request->getControllerName() != 'bater-ponto')):
            if ($auth->hasIdentity()) :
                $this->view->User = $auth->getIdentity();
            else:
                $this->_redirect('/erp/Auth');
            endif;

            $data = $this->_request->getParams();
            if ($auth->hasIdentity()):
                $acl = new AclAcessos_Model();
                $usuario = $auth->getIdentity();
                if ($usuario->papel):
                    $role = $usuario->papel;
                else:
                    $role = 'V';
                endif;
                if (!$acl->isAllowed($role, 'erp:' . $this->_request->getControllerName())):
                    $this->_redirect($this->module . '/Permissao-Negada//'.$this->_request->getControllerName());
                endif;
            else:
                $this->_redirect('/' . $this->module);
            endif;
        endif;
    }

}