var carregando = '<br /><br /><center><img src="/images/ajax-loader.gif" /><br />Carregando...</center><br /><br />';

$(function() {
    $(".filter.hour").livequery(function() {
        $(this).mask("99:99:99");
    });
    $(".filter.date").livequery(function() {
        $(this).datepicker({
            dateFormat: 'dd/mm/yy',
            regional: 'pt-BR',
            dayName: ['Domingo', 'Segunda-Feira', 'Ter�a-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'S�bado'],
            dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames:     ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthNamesShort:     ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Aug', 'Set', 'Out', 'Nov', 'Dez'],
            showButtonPanel: true
        });
    });

    $(".filter.phone").livequery(function() {
        $(this).mask("(99) 9999.9999");
    });

    $(".filter.cep").livequery(function() {
        $(this).mask("99999-999");
    });

    $(".filter.money").livequery(function() {
        $(this).maskMoney({
            symbol:"R$ ",
            decimal:".",
            thousands:"",
            showSymbol:false,
            allowZero: true,
            allowNegative: true
        });
    });

    $(".filter.cpf").livequery(function() {
        $(this).mask("999.999.999-99");
    });

    $(".filter.cnpj").livequery(function() {
        $(this).mask("99.999.999/9999-99");
    });

    $("button, input[type='submit'], .botao").livequery(function(){
        $(this).button();
    });

    $(".botao.disabled").livequery(function() {
        $(this).button({
            disabled : true
        });
    });

    $(".btAdicionar").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-plus'
            }
        });
    });

    $(".btVisualizar").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-search'
            },
            'text': false
        });
    });
    $(".btConverter").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-transferthick-e-w'
            },
            'text': false
        });
    });

    $(".buttonLink").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-extlink'
            },
            'text': false
        });
    });

    $("#btExcluir, .btExcluir").livequery(function(){
        $(this).button({
            'icons':{
                primary:'ui-icon-trash'
            },
            'text': false
        });
    });

    $(".btEditar").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-pencil'
            },
            'text': false
        });
    });

    $(".btPagar").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-cart'
            },
            'text': false
        });
    });

    $("#btImprimir").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-print'
            }
        });
    });

    $("#btImprimir").live('click',function() {
        print();
    });

    $("#btEnviarEmail").livequery(function() {
        $(this).button({
            'icons':{
                primary:'ui-icon-mail-closed'
            }
        });
    });

    $("form.formListagem").live('submit', function(){
        if($("input.itemDel:checkbox:checked").length > 0){
            if(!confirm('Deseja mesmo remover esses registros?')){
                return false;
            }
        }else{
            alert('Selecione ao menos um registro.');
            return false;
        }
    });

    $('h1').livequery(function(){
        $(this).each(function(){
            $(this).addClass('ui-widget-header ui-corner-top');
        });
    });
    $('.filter.number').livequery(function(){
        $(this).ForceNumericOnly();
    });

});
jQuery.fn.ForceNumericOnly = function(){
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, arrows, numbers and keypad numbers ONLY
            return (
                key == 8 ||
                key == 9 ||
                key == 46 ||
                (key >= 37 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        })
    })
};

