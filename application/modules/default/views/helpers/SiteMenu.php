<?php

class Zend_View_Helper_SiteMenu extends Zend_View_Helper_Navigation {

    public function SiteMenu() {
        // Home
        $page[] = array(
            'controller' => 'index',
            'route' => 'default',
            'label' => 'Home',
            'title' => 'Home'
        );

        // Institucional
        $page[] = array(
            'controller' => 'institucional',
            'route' => 'default',
            'label' => 'Institucional',
            'title' => 'Institucional'
        );

        // Serviços
        $subpages = array();
        $subpages[] = array(
            'title' => 'Cliente Doméstico',
            'label' => 'Cliente Doméstico',
            'controller' => 'servicos',
            'action' => 'cliente-domestico'
        );
        $subpages[] = array(
            'title' => 'Cliente Industrial',
            'label' => 'Cliente Industrial',
            'controller' => 'servicos',
            'action' => 'cliente-industrial'
        );
        $page[] = array(
            'controller' => 'servicos',
            'route' => 'default',
            'label' => 'Serviços',
            'title' => 'Serviços',
            'pages' => $subpages
        );

        // Vantagens
        $subpages = array();
        $subpages[] = array(
            'title' => 'Cliente Doméstico',
            'label' => 'Cliente Doméstico',
            'controller' => 'vantagens',
            'action' => 'cliente-domestico'
        );
        $subpages[] = array(
            'title' => 'Cliente Industrial',
            'label' => 'Cliente Industrial',
            'controller' => 'vantagens',
            'action' => 'cliente-industrial'
        );
        $subpages[] = array(
            'title' => 'Garantia de Qualidade',
            'label' => 'Garantia de Qualidade',
            'controller' => 'vantagens',
            'action' => 'garantia-de-qualidade'
        );
        $subpages[] = array(
            'title' => 'Responsabilidade Ambiental',
            'label' => 'Responsabilidade Ambiental',
            'controller' => 'vantagens',
            'action' => 'responsabilidade-ambiental'
        );
        $page[] = array(
            'controller' => 'vantagens',
            'route' => 'default',
            'label' => 'Vantagens',
            'title' => 'Vantagens',
            'pages' => $subpages
        );

        // Estação de Tratamento
        $subpages = array();
        $subpages[] = array(
            'title' => 'Funcionamento da Estação',
            'label' => 'Funcionamento da Estação',
            'controller' => 'estacao-de-tratamento',
            'action' => 'funcionamento-da-estacao'
        );
        $subpages[] = array(
            'title' => 'Efluente Doméstico',
            'label' => 'Efluente Doméstico',
            'controller' => 'estacao-de-tratamento',
            'action' => 'efluente-domestico'
        );
        $subpages[] = array(
            'title' => 'Efluente Industrial',
            'label' => 'Efluente Industrial',
            'controller' => 'estacao-de-tratamento',
            'action' => 'efluente-industrial'
        );
        $page[] = array(
            'controller' => 'estacao-de-tratamento',
            'route' => 'default',
            'label' => 'Estação de Tratamento',
            'title' => 'Estação de Tratamento',
            'pages' => $subpages
        );

        // A Água
        $subpages = array();
        $subpages[] = array(
            'title' => 'Direitos da Água',
            'label' => 'Direitos da Água',
            'controller' => 'a-agua',
            'action' => 'direitos-da-agua'
        );
        $subpages[] = array(
            'title' => 'Classe das Águas',
            'label' => 'Classe das Águas',
            'controller' => 'a-agua',
            'action' => 'classes-das-aguas'
        );
        $subpages[] = array(
            'title' => 'O Ciclo das Águas',
            'label' => 'O Ciclo das Águas',
            'controller' => 'a-agua',
            'action' => 'o-ciclo-das-aguas'
        );
        $subpages[] = array(
            'title' => 'Reuso da Água',
            'label' => 'Reuso da Água',
            'controller' => 'a-agua',
            'action' => 'reuso-da-agua'
        );
        $subpages[] = array(
            'title' => 'Curiosidades',
            'label' => 'Curiosidades',
            'controller' => 'a-agua',
            'action' => 'curiosidades'
        );
        $page[] = array(
            'controller' => 'a-agua',
            'route' => 'default',
            'label' => 'A Água',
            'title' => 'A Água',
            'pages' => $subpages
        );

        // Informações
        $subpages = array();
        $subpages[] = array(
            'title' => 'O Esgoto',
            'label' => 'O Esgoto',
            'controller' => 'informacoes',
            'action' => 'o-esgoto'
        );
        $subpages[] = array(
            'title' => 'Como funciona Sua Fossa Séptica',
            'label' => 'Como funciona Sua Fossa Séptica',
            'controller' => 'informacoes',
            'action' => 'como-funciona-sua-fossa-septica'
        );
        $subpages[] = array(
            'title' => 'Por que Terceirizar o Tratamento de Efluente Industrial',
            'label' => 'Por que Terceirizar o Tratamento de Efluente Industrial',
            'controller' => 'informacoes',
            'action' => 'por-que-terceirizar-o-tratamento-de-efluente-industrial'
        );
        $subpages[] = array(
            'title' => 'Utilização do Lodo do Tratamento de Esgotos Como Fertilizante',
            'label' => 'Utilização do Lodo do Tratamento de Esgotos Como Fertilizante',
            'controller' => 'informacoes',
            'action' => 'utilizacao-do-lodo-do-tratamento-de-esgotos-como-fertilizante'
        );
        $subpages[] = array(
            'title' => 'Faça Sua Parte',
            'label' => 'Faça Sua Parte',
            'controller' => 'informacoes',
            'action' => 'faca-sua-parte'
        );
        $page[] = array(
            'controller' => 'informacoes',
            'route' => 'default',
            'label' => 'Informações',
            'title' => 'Informações',
            'pages' => $subpages
        );

        // Legislação Nacional
        $page[] = array(
            'controller' => 'legislacao-nacional',
            'route' => 'default',
            'label' => 'Legislação Nacional',
            'title' => 'Legislação Nacional'
        );

        // Parceiros
        $page[] = array(
            'controller' => 'parceiros',
            'route' => 'default',
            'label' => 'Parceiros',
            'title' => 'Parceiros'
        );

        // Fale Conosco
        $page[] = array(
            'controller' => 'fale-conosco',
            'route' => 'default',
            'label' => 'Fale Conosco',
            'title' => 'Fale Conosco'
        );

        $container = new Zend_Navigation($page);
        $this->setContainer($container);

        return $this;
    }

}

