<?php

class Erp_OrcamentosController extends Erp_Controller_Action {

    public function init() {
        $this->model = new Orcamentos_Model();
        $this->form = WS_Form_Generator::generateForm('Orcamentos', $this->model->getFormFields());
        parent::init();
    }

    public function formularioAction() {
        $auth = new WS_Auth('erp');
        $usuario = $auth->getIdentity();

        $cliente_id = $this->_getParam('parent_id');

        if (!empty($cliente_id)):
            $this->model->_params['cliente_id'] = $this->_getParam('parent_id');
        endif;

        $this->options['noList'] = true;
        if (!$this->_hasParam('usuario_id')):
            $this->model->_params['usuario_id'] = $usuario->id;
        endif;

        parent::formularioAction();
    }

    public function visualizarAction() {
        $id = $this->_request->getParam('parent_id');
        $data['orcamento_id'] = $id;
        $this->view->id = $id;

        $arquivo = UPLOAD_PATH . 'orcamentos/Orcamento_' . $id . '.pdf';
        if (is_file($arquivo)):
            $dados = stat($arquivo);
            $this->view->pdf_gerado = $dados[9];
            $this->view->pdf = 'Orcamento_' . $id . '.pdf';
        endif;
    }

    public function enviaremailAction() {
        $dados = $this->model->buscaCliente($this->_request->getParam('parent_id'));
        $form = new Orcamentos_FormEmail();
        if ($this->_request->isPost()):
            if ($form->isValid($this->_request->getPost())) :
                try {
                    $configs = Zend_Registry::get('application');
                    $data = $this->_request->getParams();

                    $mail = new Email_Model('utf-8');

                    $this->view->conteudo = $data;
                    $body = $this->view->render('emails/orcamento.phtml');

                    $mail->setBodyHtml($body, 'utf-8');
                    $mail->setSubject(utf8_decode($data['assunto']) . ' (' . $data['parent_id'] . ')');
                    $mail->setReplyTo($configs->cliente->email);

                    $destinatarios = explode(';', $data['destinatarios']);
                    foreach ($destinatarios AS $destinatario):
                        $mail->addTo($destinatario);
                    endforeach;
                    $mail->addBcc($configs->cliente->email);

                    $arquivo = realpath(UPLOAD_PATH . 'orcamentos/Orcamento_' . $data['parent_id'] . '.pdf');

                    $at = new Zend_Mime_Part(file_get_contents($arquivo));
                    $at->type = 'application/pdf';
                    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                    $at->encoding = Zend_Mime::ENCODING_BASE64;
                    $at->filename = $data['parent_id'] . '.pdf';

                    $mail->addAttachment($at);
                    $mail->envia($configs->cliente->email, $configs->cliente->nome);

                    $auth = new WS_Auth('erp');
                    $usuario = $auth->getIdentity();

                    $this->alerta('sucess', 'E-mail enviado com sucesso!');
                } catch (Zend_Mail_Exception $e) {
                    $this->alerta('error', $e->getMessage());
                }
            else:
                $this->alerta('error', 'Preencha todos os dados corretamente!');
                return false;
            endif;
        else:
            $dados['destinatarios'] = $dados['email'];
            $form->populate($dados);
            $this->view->form = $form;
        endif;
    }

    public function configuracaoAction() {
        $this->form = new Orcamentos_FormConfiguracao();
        $parent_id = $this->_request->getParam('parent_id');
        if (!empty($parent_id)):
            $this->model->_params['id'] = $parent_id;
        endif;

        parent::formularioAction();
    }

    public function verAction() {
        $this->view->noLogo = true;
        $orcamento_id = $this->_request->getParam('id');
        $orcamento = $this->model->find($orcamento_id);
        $this->view->orcamento = $orcamento;

        $ServicosModel = new Servicos_Model();
        $servicosOrcamento = $ServicosModel->buscarPorOrcamento($orcamento_id);
        $this->view->servicos = $servicosOrcamento;
    }

    public function clienteAction() {
        $cliente_id = $this->_request->getParam('parent_id');
        $items = $this->model->buscarPorCliente($cliente_id);
        $this->view->items = $items;
    }

    public function gerarpdfAction() {
        $orcamento_id = $this->_request->getParam('id');
        $timbrado = $this->_request->getParam('timbrado');

        if (!$timbrado):
            $document = $this->model->gerarPdf($orcamento_id);
        else:
            $document = $this->model->gerarPdf($orcamento_id, $timbrado);
            $arquivo = 'Orcamento_' . $orcamento_id . '.pdf';
            $this->getResponse()
                    ->setHeader('Content-Disposition', 'attachment; filename=' . $arquivo)
                    ->setHeader('Content-type', 'application/x-pdf')
                    ->setBody($document);
        endif;
    }

    public function verpdfAction() {
        $id = $this->_request->getParam('id');
        $arquivo = 'Orcamento_' . $id . '.pdf';
        $document = file_get_contents(UPLOAD_PATH . 'orcamentos/' . $arquivo);
        $this->getResponse()
                ->setHeader('Content-Disposition', 'attachment; filename=' . $arquivo)
                ->setHeader('Content-type', 'application/x-pdf')
                ->setBody($document);
    }

}