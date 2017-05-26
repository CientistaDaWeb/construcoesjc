<?php

class Default_FaleConoscoController extends Zend_Controller_Action {

    public function indexAction() {
        $form = new FaleConosco_Form();
        if ($this->_request->isPost()) :
            if ($form->isValid($this->_request->getPost())) :
                try {
                    $dados = $this->_request->getPost();
                    $nome = $dados['nome'];
                    $email = $dados['email'];

                    $mail = new WS_Email("UTF-8");

                    $this->view->conteudo = $dados;
                    $body = $this->view->render('emails/contato.phtml');

                    $mail->setBodyHtml($body, 'UTF-8')
                            ->setSubject(utf8_decode('Contato enviado pelo site Acquasana'))
                            ->setFrom($email, $nome)
                            ->setReplyTo($email)
                            ->Envia();
                    $this->_helper->FlashMessenger(array('sucess' => 'E-mail enviado com sucesso!'));
                    $form->reset();
                    $this->_redirect('/fale-conosco');
                } catch (Zend_Mail_Exception $e) {
                    $this->_helper->FlashMessenger(array('error' => 'Erro ao enviar o e-mail - ' . $e->getMessage()));
                }
            else :
                $this->_helper->FlashMessenger(array('error' => 'Preencha todos os campos obrigatórios!'));
                $form->populate($this->_request->getPost())->markAsError();
            endif;
        else :
            $dados = array(
                'estado' => 'RS'
            );
            $form->populate($dados);
        endif;
        $this->view->form = $form;
    }

}

