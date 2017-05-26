<?php

$taxa_boleto = 0;
$data_venc = WS_Date::adjustToView($boleto['data_vencimento']);  // Prazo de X dias OU informe data: "13/04/2006";
$valor_cobrado = $boleto['valor'] - $boleto['valor_retido'];
$valor_boleto = number_format($valor_cobrado + $taxa_boleto, 2, ',', '');

$dadosboleto["inicio_nosso_numero"] = date("y"); // Ano da gera��o do t�tulo ex: 07 para 2007
$dadosboleto["nosso_numero"] = $boleto['id'];    // Nosso numero (m�x. 5 digitos) - Numero sequencial de controle.
$dadosboleto["numero_documento"] = $boleto['id']; // Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto;  // Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $boleto['cliente'];
$dadosboleto["endereco1"] = $boleto['cliente_endereco'] . ', ' . $boleto['cliente_numero'] . ' - ' . $boleto['cliente_bairro'];
$dadosboleto["endereco2"] = $boleto['cliente_cidade'] . ' / ' . $boleto['cliente_uf'] . ' ' . $boleto['cliente_cep'];

$mora = number_format($valor_cobrado * 0.00033, 2, ',', '.');
$multa = number_format($valor_cobrado * 0.02, 2, ',', '.');

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = '';
$dadosboleto["demonstrativo2"] = '';
$dadosboleto["demonstrativo3"] = '';
$dadosboleto["instrucoes1"] = '- APOS VENCIMENTO COBRAR R$ ' . $mora . ' POR DIA DE ATRASO';
$dadosboleto["instrucoes2"] = '- APOS VENCIMENTO COBRAR MULTA DE R$ ' . $multa;
$dadosboleto["instrucoes3"] = '- PROTESTAR APOS 05 DIAS CORRIDOS DO VENCIMENTO';
$dadosboleto["instrucoes4"] = '- ' . $boleto['descricao'];
$dadosboleto["instrucoes5"] = '';

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "S";     // N - remeter cobran�a sem aceite do sacado  (cobran�as n�o-registradas)
// S - remeter cobran�a apos aceite do sacado (cobran�as registradas)
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "A"; // OS - Outros segundo manual para cedentes de cobran�a SICREDI
// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //
// DADOS DA SUA CONTA - SICREDI
$contaCorrente = explode('-', $boleto['conta_corrente']);
if (!is_array($contaCorrente)):
    $contaCorrente = array('0', '0');
endif;
$dadosboleto["agencia"] = $boleto['agencia'];  // Num da agencia (4 digitos), sem Digito Verificador
$dadosboleto["conta"] = $contaCorrente[0];  // Num da conta (5 digitos), sem Digito Verificador
$dadosboleto["conta_dv"] = $contaCorrente[1];  // Digito Verificador do Num da conta
// DADOS PERSONALIZADOS - SICREDI
$dadosboleto["posto"] = "24";      // C�digo do posto da cooperativa de cr�dito
$dadosboleto["byte_idt"] = "2";   // Byte de identifica��o do cedente do bloqueto utilizado para compor o nosso n�mero.
// 1 - Idtf emitente: Cooperativa | 2 a 9 - Idtf emitente: Cedente
$dadosboleto["carteira"] = $boleto['carteira'];   // C�digo da Carteira: A (Simples)
// SEUS DADOS

$dadosboleto["identificacao"] = "";
$dadosboleto["cpf_cnpj"] = $boleto['empresa_documento'];
$dadosboleto["endereco"] = $boleto['empresa_endereco'] . ', ' . $boleto['empresa_numero'] . ' - ' . $boleto['empresa_bairro'];
$dadosboleto["cidade_uf"] = $boleto['empresa_cidade'] . ' / ' . $boleto['empresa_uf'];
$dadosboleto["cedente"] = $boleto['empresa'];

// NÃO ALTERAR!
include("BoletoSicredi/funcoes.php");
?>
