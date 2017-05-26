<?php

class FaleConosco_Form extends Zend_Form {

    public function init() {
        $estadosModel = new Estados_Model();

        $this->setAttrib('id', 'fale-conosco-form')
                ->setAction('fale-conosco')
                ->setMethod('post');
        $this->addElement('text', 'nome', array(
            'label' => 'Nome',
            'required' => true
        ));
        $this->addElement('text', 'email', array(
            'label' => 'E-mail',
            'required' => true,
            'validators' => array(
                'EmailAddress'
            )
        ));
        $this->addElement('text', 'telefone', array(
            'label' => 'Telefone',
        ));
        $this->addElement('text', 'cidade', array(
            'label' => 'Cidade',
        ));
        $this->addElement('select', 'estado', array(
            'label' => 'Estado',
            'multiOptions' => $estadosModel->getEstadosPair()
        ));
        $this->addElement('textarea', 'mensagem', array(
            'label' => 'Mensagem',
            'rows' => '7',
            'cols' => '35',
            'required' => true
        ));
        $this->addElement('button', 'enviar', array(
            'ignore' => true,
            'label' => 'Enviar'
        ));
        $this->getElement('enviar')->removeDecorator('label')->removeDecorator('DtDdWrapper')->setAttrib('type', 'submit');
    }

}