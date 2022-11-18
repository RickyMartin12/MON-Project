
// VERIFICAR SE O ARRAY ESTA VAZIO
function isObjectEmpty(obj) {
    return Object.keys(obj).length === 0;
}
// ------------------------------------------------------------  LEADS.PHP (EDICAO) ------------------------------------------------------------------------------------------

// --------------------------------------------------------------- ESTADOS 1 A 5  -----------------------------------------------------------------------------------------


// FUNCOES DO CODIGO

/*
prop_conn_type
prop_conn_type_serv_des
conn_prop_id_diff
subscriber_prop_id
subscriber_list
conn_prop_chg_over
prop_id_type_connection_chnage_over
vlansbyolt
ponsbyolt
equip_connection_assoc
num_fiscal_customer
owner_id_prop
new_customer_post
prop_id_conn_id_type
prop_id_type_connection
initial_equip_con_prop
connection_type_prop_internet
conexao_id_prop_equ
*/

// LEADS.PHP
// ESTADO 50
// EQUIPAMENTO, MODELO, SERVIÇO DESLIGADO - DATE_END != '0000-00-00'
var equip = 1;
var modelo = 1;
var servico_des = 1;
// GPON
// OLT
var is_olt=1;
// PON
var is_pon=1;
// FWA
// ANTENNA
var is_antenna=1;

// MUDANACA DO TYPE CONNECTION CORRESPONDENTE AO MODELO
// LEADS.PHP - ESTADOS 1 A 5 // 30 // 50;
// ?propleads=1&lead_id=9355
function updatecpe(type,model, lead_id) 
{
    // INDIQUE OS MODELOS DA TYPE CONNECTION
    var models = document.getElementById('models');
    if(models != null)
    {
        models.innerHTML = "";
        // MODELO DA FSAN OU FWA CPE - MODELOS DA TYPE CONNECTION
        var cpe_text = document.getElementById('cpe_text');
        var text;
        if (type == "GPON") {
            // GPON
            // ATIVA A OPCAO DO SERVIÇO TV
            $("#tv").prop('disabled', false);
            // MODELOS
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            // CPE
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }

            text = '<option  value=zhone-2427';
            if(model=='zhone-2427')
            {	 text += ' selected';}
            text += '>zhone-2427</option><option value=zhone-2428 ';

            if(model=='zhone-2428')
            { text += ' selected';}
            text += '>zhone-2428</option><option value=zhone-2727a ';

            // VALOR INICIAL POR DEEFITO DA GPON
            if(model=='zhone-2727a' || model=='' || model==false)
            { text += ' selected';}
            text += '>zhone-2727a</option><option value=zhone-2301 ';

            if(model=='zhone-2301')
            { text += ' selected';}
            text += '>zhone-2301</option>';

        } else if (type == "COAX") {
            // COAX
            // ATIVA A OPCAO DO SERVIÇO TV
            $("#tv").prop('disabled', false);
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            // VALOR INICIAL POR DEEFITO DA COAX
            text = '<option  value=cve30360 ';
            if(model=='cve30360' || model=='' || model==false)
            {	 text += ' selected';}
            text += '>cve30360</option> <option value=cva30360 ';

            if(model=='cva30360')
            {	text += ' selected';}
            text += '>cva30360</option> ';


        } else if (type == "FWA") {
            // FWA
            // DESATIVAR A OPCAO TV CASO QUE A CONNECTION TYPE FOR FWA
            $("#tv").prop('disabled', true);
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            // VALOR INICIAL POR DEEFITO DA FWA
            text = '<option  value=ltulite ';
            if(model=='ltulite' || model=='' || model==false)
            {	 text += ' selected';}
            text += '>LTU lite</option> <option value=ltulr ';

            if(model=='ltulr')
            {	 text += ' selected';}
            text += '>LTU LR</option> <option value=ltupro ';

            if(model=='ltupro')
            {	 text += ' selected';}
            text += '>LTU pro</option>';
        } else if (type == "DIA") {
            // DIA
            // ATIVA A OPCAO DO SERVIÇO TV
            $("#tv").prop('disabled', false);
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            text = '<option  value=sfp_bidi_1Gb';
            if(model=='sfp_bidi_1Gb' || model=='' || model==false)
            {	 text += ' selected';}
            text += '>SFP bidi 1Gb</option> <option value=sfpp_bidi_10Gb ';

            if(model=='sfpp_bidi_10Gb')
            {	text += ' selected';}
            text += '>sfpp bidi 10Gb</option> ';

        } else if (type == "DARKF") {
            // DARKF
            // ATIVA A OPCAO DO SERVIÇO TV
            $("#tv").prop('disabled', false);
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            // VALOR INICIAL POR DEEFITO DA DARKF
            text = '<option  value=no_cpe';
            if(model=='no_cpe' || model=='' || model==false)
            {	 text += ' selected';}
            text += '>no_cpe</option> ';


        } else if (type == "ETH") {
            // ETH
            // ATIVA A OPCAO DO SERVIÇO TV
            
            $("#tv").prop('disabled', false);
            if(models  != null){
                document.getElementById('models').style.display = 'block';
            }
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'block';
            }
            // VALOR INICIAL POR DEEFITO DA ETH
            text = '<option  value=tplink ';
            if (model == 'tplink' || model == '' || model == false) {
                text += ' selected';
            }
            text += '>tplinkrt</option> <option value=zyxelrt ';

            if (model == 'zyxelrt') {
                text += ' selected';
            }
            text += '>zyxelrt</option><option value=comega ';

            if (model == 'comega') {
                text += ' selected';
            }
            text += '>comega</option> <option value=switch ';

            if (model == 'switch') {
                text += ' selected';
            }
            text += '>switch</option> <option value=switch ';

            if (model == 'sfp') {
                text += ' selected';
            }
            text += '>sfp</option>';
        } else {
            // ATIVA A OPCAO DO SERVIÇO TV
            $("#tv").prop('disabled', false);
            //document.getElementById('models')
            // SE TEM MODELOS DA TYPE CONNECTION
            if(models  != null){
                document.getElementById('models').style.display = 'none';
            }
            // SE TEM A DESCRICAO DO MODELO DA TYPE CONNECTION CORRESPONDENTE
            if(cpe_text != null)
            {
                document.getElementById('cpe_text').style.display = 'none';
            }

            text = "<option selected value=>select connection first</option>";
        }
        $("#models").html(text);

        // lEADS = ESTAD0 50 - INSERIR CONNECTIONS & SERVIÇOS
        var status = $("#idstatus").val();
        // MOSTRAR O EQUIPAMENTO INICIAL POR DEFEITO QUE CORRESPONDENTE A LEAD QUE VAI INDICAR A SUA PROPRIEDADE E O TIPO CONNECTION RELACIONADA, CONNECTION E A SUA PROPRIEDADE
        // ESTADO 50
        if(status == 50)
        {
            var prop_id = $("#prop_id").val(); // PROP ID
            var con_id = $("#con_id").val(); // CONNECTION ID
            if(con_id != "")
            {
                // MOSTRAR O EQUIPAMENTO POR DEFEITO CASO QUE TEM UMA CONNECTION DA PROP SELECCIONADA DE ACORDO COM A LEAD CORRESPONDENTE
                // PROCEDURE = CHANGE OVER & RECONNECTION
                putInitialEquipPropCon(prop_id, con_id, type, lead_id);
            }
            else
            {
                // MOSTRAR O EQUIPAMENTO QUE TEM UMA PROPRIEDADE SEM TER CRIADO UMA CONNECTION
                // PROCEDURE = NEW CONNECTION
                putEquipInitial(prop_id,type, lead_id);
            }
        }
    }
}



// MOSTRAR A PROP DAS CONNECTIONS DO MESMO TYPE COM SERVIÇOS DESABILITADOS
// CHECKED - PROPR. DAS CONNECTIONS COM SERVIÇOS DESABILITADOS
// UNCKCKED - PROP. DA CONNECTION COM OU SEM SERVIÇOS
function changePropServicesDisabled(prop_services, val)
{
    var text_des_ser = '';
    if (prop_services.checked || val == 1)
    {
        text_des_ser += '<input type=hidden id=conn_id_des name=conn_id_des><br>';
        $("#prop_conn_servicos_des").html(text_des_ser);
        // Propriedades dos servicos desativados
        chg_type_conn_check($("#con_type").val());
    }
    else if(val == 0 || !(prop_services.checked ))
    {
        text_des_ser += '';
        $("#prop_conn_servicos_des").html(text_des_ser);
        // Propriedades do tipo de conxao coreespondente
        chg_type_conn_not_check($("#con_type").val());
    }


}


// MANTIDO O CODIGO
// MOSTRAR AS CONNECTIONS DAS PROPRIEDADES COM A TYPE CONNECTION SELECCIONADA
// CHECKED - PROPR. DAS CONNECTIONS COM SERVIÇOS DESABILITADOS
// UNCKCKED - PROP. DA CONNECTION COM OU SEM SERVIÇOS
// disabled_prop_services - FUNCAO changePropServicesDisabled(VALOR_CHECKBOX, VALOR POR DEFEITO - CHECKED)
function change_type_connection(con_type)
{
    var checked = $("#disabled_prop_services:checked").length;
    var prop_id = $("#refe_rec_7").val();
    if(checked == 1)
    {
        // Propriedades dos servicos desativados do tipo de conexao coreespondente

        chg_type_conn_check(con_type);

    }
    else
    {

        chg_type_conn_not_check(con_type);

    }

}


// MOSTRAR AS CONNCETIONS DAS PROP DO MESMO TYPE COM OU SEM SERVIÇOS

function chg_type_conn_not_check(con_type)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type': '1', 'con_type': con_type}})
        .done(function( data )
        {
            if(data != null)
            {
                var result = $.parseJSON(data);
                if(result['prop_conn_type'] != null)
                {
                    var prop_id_con = result['prop_conn_type'][0][1];
                    var conn_id = result['prop_conn_type'][0][2];
                    var html_refe_7 = "";
                    $.each( result['prop_conn_type'], function( key, value )
                    {
                        var prop_conner = value[1]+'-'+value[2];
                        html_refe_7 += "<option value="+value[1]+" data-conn_prop="+prop_conner+">"+value[0]+"</option>";
                    });

                    $("#refe_rec_7").html(html_refe_7);

                    con_prop_rec(prop_id_con, conn_id);
                }
                else
                {
                    refe_rec_7.innerHTML = "";
                    var refe_rec_7_b;
                    refe_rec_7_b = new Option("", "0");
                    refe_rec_7.options.add(refe_rec_7_b);
                    $("#conn_id_prop_rec").val("");
                    $("#conn_assoc_prop_status_7").html('<font color=red>Cannot associate the property to make a reconnection</font><input type=hidden name=rec_assoc value=0>');

                }
            }
            else
            {
                refe_rec_7.innerHTML = "";
                var refe_rec_7_b;
                refe_rec_7_b = new Option("", "0");
                refe_rec_7.options.add(refe_rec_7_b);
                $("#conn_id_prop_rec").val("");
                $("#conn_assoc_prop_status_7").html('<font color=red>Cannot associate the property to make a reconnection</font><input type=hidden name=rec_assoc value=0>');

            }
        });


}

// MOSTRAR AS CONNCETIONS DAS PROP DO MESMO TYPE COM SERVIÇOS DESABILITADOS

function chg_type_conn_check(con_type)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type_serv_des': '1', 'con_type': con_type}})
        .done(function( data )
        {
            var refe_rec_7=document.getElementById("refe_rec_7");
            if(data != null)
            {
                var result = $.parseJSON(data);
                if(result['prop_conn_type'] != null)
                {
                    var prop_id_con = result['prop_conn_type'][0][1];
                    var conn_id = result['prop_conn_type'][0][2];
                    var html_refe_7 = "";
                    $.each( result['prop_conn_type'], function( key, value )
                    {
                        var prop_conner = value[1]+'-'+value[2];
                        html_refe_7 += "<option value="+value[1]+" data-conn_prop="+prop_conner+">"+value[0]+"</option>";
                    });
                    $("#refe_rec_7").html(html_refe_7);
                    con_prop_rec(prop_id_con,conn_id);
                    $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
                }
                else
                {
                    refe_rec_7.innerHTML = "";
                    var refe_rec_7_b;
                    refe_rec_7_b = new Option("", "0");
                    refe_rec_7.options.add(refe_rec_7_b);
                    $("#conn_assoc_prop_status_7").html('<font color=red>Cannot associate the property to make a reconnection</font><input type=hidden name=rec_assoc value=0>');
                    $("#conn_id_prop_rec").val("");

                }
            }
            else
            {
                refe_rec_7.innerHTML = "";
                var refe_rec_7_b;
                refe_rec_7_b = new Option("Nao tem propriedade com esta conexão", "0");
                refe_rec_7.options.add(refe_rec_7_b);
                $("#conn_id_prop_rec").val("");
            }
        });
}

// FORM CHANGE OVER ESTADOS 1,5 & 30
function changeOver_State_1(con_type)
{
        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': con_type}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                // CON TYPE CHANGE OVER

                var html_connection = '';
                var owner_html = '';

                for(var j=0; j<result['arr_con'].length; j++)
                {
                    html_connection += '<option value="' + result['arr_con'][j] + '">' + result['arr_con'][j] + '</option>';
                }

                $('#con_type_chg_over').html(html_connection);


                // SUBSCRIBER
                for(var i=0; i<result['customers'].length; i++)
                {
                    owner_html += '<option value="' + result['customers'][i][1] + '">' + result['customers'][i][0] + '</option>';
                }

                $("#owner_chg").html(owner_html);


                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {
                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });

                    con_prop_type(result[0][1]);
                }
            });
}

// ESTADOS DOS CAMPOS - LEADS 1,5 e 30

function statusFields(soption, status)
{
        if(soption>0 && soption<5)
        {
            $("input[name=ORAC_pits]").prop('disabled', status);
            $("input[name=ORAP_poles]").prop('disabled', status);
            $("input[name=drop_length]").prop('disabled', status);
            $("input[name=connection_cost]").prop('disabled', status);

            $("input[name=plan]").prop('disabled', status);
            $("input[name=planz]").prop('disabled', status);

            $("input[name=is_network_ready]").prop('disabled', status);
            $("input[name=network_cost]").prop('disabled', status);
            $("input[name=estimated_quote]").prop('disabled', status);
            $("input[name=timeframe]").prop('disabled', status);
        }
        else if(soption==30)
        {
            
            // ESTADO 30 - FORMS INITIALS 
                $("#owner_id").prop('disabled', status);
                $("#concelho").prop('disabled', status);
                $("#freg").prop('disabled', status);
                $("#ref").prop('disabled', status);
                $("#owner_id").prop('disabled', status);
                $("#concelho").prop('disabled', status);
                $("#freg").prop('disabled', status);
                $("#ref").prop('disabled', status);
        }
}

// FORMULARIO PARA SELECCIONAR SE E CHANGE OVER OU RECONNECTION
var valor_rec_chg_over = "";

function FormChgOverReconnection(action, changeover, reconnection, con_type, soption, lead_id)
{
    valor_rec_chg_over = action.value;

    if(con_type == "")
    {
        con_type = $("#con_type").val();
    }

    var soption = $("#idstatus").val();

    // SELECCIONAR SE UMA FORM DA RECONNECTION OU CHANGE OVER

    if(valor_rec_chg_over == "is_reconnection" || reconnection==1)
    {
        $("#rec_form").css('display', 'block');
        $("#chg_over_form").css('display', 'none');

        $("#reconnection_form").val(1);
        $("#changeover_form").val(0);
        $("#new_conn_form").val(0);

        $("#disabled_prop_services").prop( "checked", true );
        

        changePropServicesDisabled("", 1);
        
        
        // CAMPOS PARA DESATIVAR

        statusFields(soption, true);
        $("input[name=prev_rev_month]").prop('disabled', true);

        $(".add_client_but").attr('id', 'button_client_reconnection');

        $("#button_client_new_connection").prop('disabled', true);

        $(".add_new_client").attr('id', 'add_new_client_reconnection');
        $(".add_new_client").addClass('add_client_reconnection');
    }

    else if(valor_rec_chg_over == "is_changeover" || changeover==1)
    {
        $("#chg_over_form").css('display', 'block');
        $("#rec_form").css('display', 'none');

        $("#changeover_form").val(1);
        $("#reconnection_form").val(0);
        $("#new_conn_form").val(0);        

        // CAMPOS PARA DESATIVAR

        changeOver_State_1(con_type);

        if(soption>0 && soption<5)
        {
            statusFields(soption, false);
        }
        else if(soption==30)
        {
            statusFields(soption, true);
            $("input[name=prev_rev_month]").prop('disabled', false);
        }

        $(".add_client_but").attr('id', 'button_client_change_over');
        $("#add_client_but_new_conn").prop('disabled', true);
        

        $(".add_new_client").attr('id', 'add_new_client_change_over');
        $(".add_new_client").addClass('add_client_is_changeover');

        
    }

    else if(valor_rec_chg_over == "is_new_connection" || reconnection==0 || changeover==0)
    {

        $("#chg_over_form").css('display', 'none');
        $("#rec_form").css('display', 'none');

        $("#new_conn_form").val(1);
        $("#changeover_form").val(0);
        $("#reconnection_form").val(0);

        statusFields(soption, false);
        $("input[name=prev_rev_month]").prop('disabled', false);

        $("#add_client_but_new_conn").prop('disabled', false);
        $(".add_new_client").attr('id', 'add_new_client');
        $(".add_new_client").addClass('add_client_new_connection');


        PropLeadNewConnection(lead_id);
    }


}

// funcoes do formulario da change over - leads - estados 1 a 5, 30
// LISTAR AS CONNECTIONS DE CONVERSAO DE TECNOLOGIAS ANTERIORTES


// CHANGE OVER DA PROPRIEDADE QUE FAZ A LISTA DE PROPRIEDADES COM CONNECTIOS ANTERIORES 
// GPON - LISTAR AS CONNECTIONS DE FWA E COAX
// FWA - LISTAR AS CONNECTIONS DE GPON
function changeOverTypeConnection(con_type_chg_over)
{
    var refe=document.getElementById("refe");
    $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_chg_over': '1', 'type': con_type_chg_over}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                console.log(result);
                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {
                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });
                    // BUSCAR A CONNECTION DA PROPERTY QUE MOSTRA AS CONEXOES ANTERIORES DA CHANGE OVER 
                    // GPON - LISTAR AS CONNECTIONS DE FWA E COAX
                    // FWA - LISTAR AS CONNECTIONS DE GPON
                    con_prop_type(result[0][1]);
                }

            });
}

// LEADS ESTADOS 1, 5 & 30
// LISTAR AS CONNECTIONS DA PROPRIEDADE SELECCIONADA
function con_prop_type(prop_id)
{
    var type = $("#con_type").val();
    var form_conn = '';
    // MOSTRAR ALISTA DE CONNECTIONS DE UMA DADA PROPRIEDADE
    form_conn += '<span id=wat_rello>Waiting for List Connections... <div id="size_roller"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#conn_type_fsan").html(form_conn);
    // PROP ID, CONNECTION ID E OWNER CHG
    $("#refe").prop('disabled', true);
    $("#con_id").prop('disabled', true);
    $("#owner_chg").prop('disabled', true);
    // PROP ID DA CONNECTION DA CHANGE OVER
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_type_connection': '1', 'prop_id': prop_id, 'type': type}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            if(!isObjectEmpty(result))
            {
                // se existir conexao na changeover
                if($("#is_changeover_val").val() == 1)
                {
                    $( "#is_changeover" ).prop( "checked", true );
                }
                // SELECCIONE A LISTA DE CONEXOES DESTA PROPRIEDADE SELECCIONADA
                var conn_id;
                // Connection da Change Over
                $.each( result['conexoes'], function( key, value )
                {
                    //conn_id = new Option(value['connection_id']+ "- "+value['referencia'], value['connection_id']);
					conn_id += "<option value="+value['connection_id']+">"+value['connection_id']+ "- "+value['referencia']+"</option>";
                    //con_id.options.add(conn_id);
                });
				// MOSTRAR A LISTA DE CONNEECTIONS DE UMA DADA PROPRIEDADE (FUNCIONALIDADE DA CHANGE OVER)
				$("#con_id").html(conn_id);
                var connection_id = '';
                // CONNECTION ID SE JA TEM O VALOR DO CAMPO FIELD DA CONNECTION
                if($("#con_id").val() != '')
                {
                    connection_id = $("#con_id").val();
                }
                else
                {
                    // VAI BUSCAR A PRIMEIRA CONNECTION DA PROPRIEDADE SELECCIONADA
                    connection_id = result['conexoes'][0]['connection_id'];
                }

                // INDIQUE O EQUIPMENTO DA CONEXAO CORRESPONDENTE
                connection_equip(connection_id);

                // Connection DA LIGACAO DO EQUIPAENTO DA TYPE CONNECTION ANTERIOR
                $.each( result['t_conn'], function( key, value )
                {
                    $("#con_type_id").val(value['type']);
                });

                // SUBSRIBER DA CHANGE OVER - LISTAGM DE CLIENTES
                $.each( result['subs'], function( key, value )
                {
                    // ID DO CLIENTE
                    var id_cust = value['id'];
                    // NOME
                    var nome = value['name'];
                    // NUMERO FISCAL
                    var fiscal = value['fiscal_nr'];
                    var span_owner=id_cust+"-"+nome+"#"+fiscal;
                    $("#select2-owner_chg-container").attr('title', span_owner);
                    $("#select2-owner_chg-container").html(span_owner);
                    $("#owner_chg").val(value['id']);
                });
            }
        });
        if($("#changeover_form").val() == 1 )
        {   
            getaddressPropId(prop_id);
        }
}

function getaddressPropId(prop_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'address_prop_id': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            console.log(result);
            $("input[name=address]").val(result['address']);
            var list_freg = '';

            $.each( result['freg_list'], function( key, value )
                {
                    list_freg += "<option value="+value[1]+">"+value[0]+"</option>";
                });
            $("#freg").html(list_freg);
            $("#freg").val(result['freguesia']);
            var span_owner=result['ref']+" - "+result['desc'];
            $("#select2-ref-container").attr('title', span_owner);
            $("#select2-ref-container").html(span_owner);
            $("#ref").val(result['ref']);
            $.each( result['subs'], function( key, value )
                {
                    // ID DO CLIENTE
                    var id_cust = value['id'];
                    // NOME
                    var nome = value['name'];
                    // NUMERO FISCAL
                    var fiscal = value['fiscal_nr'];
                    var span_owner=id_cust+"-"+nome+"#"+fiscal;
                    $("#select2-owner_id-container").attr('title', span_owner);
                    $("#select2-owner_id-container").html(span_owner);
                    $("#owner_id").val(value['id']);
                });
				
        });
}

// LEADS ESTADOS 1, 5 & 30
// MOSTRA OS DETALHES DA CONNECTION SELECCIONADA
function connections_list(conn_id)
{
    var form_conn = '';

    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_conn_id_type': '1', 'conn_id': conn_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            // PROP ID DA CONNECTION LIST
            var prop_id = $("#refe").val();
            // TYPE DA CONNECTION ANTERIOR
            var t = result['t_conn'][0]['type'];
            $.ajax({ method: "GET", url: "webservice.php", data: { 'conexao_id_prop_equ': '1', 'prop_id': prop_id, 'tipo': t, 'conn_id': conn_id}})
                .done(function( data )
                {

                    $("#refe").prop('disabled', false);
                    $("#con_id").prop('disabled', false);
                    $("#owner_chg").prop('disabled', false);


                    $("#wat_rello").html('');
                    var result2 = $.parseJSON(data);
                    var c = $("#con_id").val();
                    form_conn += '<td>Conexao Numero <input type=hidden id=con_id_edit_change name=con_id_edit_change value='+c+'>'+c;
                    form_conn += '<td>Equipment: <span>'+result2['equip']+'</span><input type=hidden name=equip_id_chg value='+result2['equip']+'>';
                    form_conn += '<td>Type Connection OLD: '+result['t_conn'][0]['type']+'<input type=hidden name=type_conn_old value='+result['t_conn'][0]['type']+'>';
                    $("#conn_type_fsan").html(form_conn);

                    //$("#con_type").val(t);


                    //updatecpe(t);

                });


        });
}

function connection_equip(conn_id)
{
    connections_list(conn_id);
}

// RECONNECTION DA PROP ID DA CONNECTION CORRESPONDENTE (MESMA CONNECTION TYPE)
function con_prop_rec(prop_id, conn_id)
{
    var checked = $("#disabled_prop_services:checked").length;
    var con_type = $("#con_type").val();
    var prop_conn = $('#refe_rec_7 option:selected').data('conn_prop');
    var str_prop_conn = prop_conn.split('-');
    conn_id = str_prop_conn[1];
    if(checked == 1)
    {
        // SE TIVER A CONNECTIONN DE UMA DADA PROPERTY
        if(conn_id != null)
        {
            $("#conn_id_des").val(conn_id);
            $("#conn_id_prop_rec").val(conn_id);
            $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
        }
        else
        {
            $("#conn_assoc_prop_status_7").html('<font color=red>Cannot exists connections on property number '+prop_id+' to make a reconnection</font><input type=hidden name=rec_assoc value=0>');
        }


        $("#conn_id_des").val(conn_id);
    }
    else
    {
        // Listar as Propriedades das conxoes deste tipo de conexao com serviços
        if(conn_id != null)
        {
            $("#conn_id_prop_rec").val(conn_id);
            $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
        }
        else
        {
            $("#conn_assoc_prop_status_7").html('<font color=red>Cannot exists connections on property number '+prop_id+' to make a reconnection</font><input type=hidden name=rec_assoc value=0>');
        }
    }
    // OBTER O SUBSCRITOR DA PROP ID - RECONNECTION
    // ESTADO 30 - LEAD
    getSubsPropId(prop_id);
    if($("#reconnection_form").val() == 1 )
        {
            getaddressPropId(prop_id);
        }
    
}

// GET SUBSCRIBER ON PROPERTY
// OBTER O CLIENTE DA PROPRIEDADE CORRESPONDENTE
// ESTADO 30
function getSubsPropId(prop_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'subscriber_list': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var owner_html = "";
            for(var i=0; i<result['list_cuts'].length; i++)
            {
                owner_html += '<option value="' + result['list_cuts'][i][1] + '">' + result['list_cuts'][i][0] + '</option>';
            }
            $("#owner_rec").html(owner_html);
            $.each( result['subs'], function( key, value )
                {
                    var id_cust = value['id'];
                    var nome = value['name'];
                    var fiscal = value['fiscal_nr'];
                    var span_owner=id_cust+"-"+nome+"#"+fiscal;
                    $("#select2-owner_rec-container").attr('title', span_owner);
                    $("#select2-owner_rec-container").html(span_owner);
                    $("#owner_rec").val(value['id']);
                });
        });    
}


// RECONNECTIONS ATRIBUIDO POR DEFEITO - INSERIDO NOS ESTADOS 1 A 5 & 30 - LEADS

// ESTADO 30 - LEADS

function RecPropConnType(prop_id,con_type,conn_id,lead_sub,checked)
{
    if(checked == "checked")
    {
        $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type_serv_des': '1', 'con_type': con_type}})
        .done(function( data )
        {
            //var refe_rec_7=document.getElementById("refe_rec_7");
            if(data != null)
            {
                // PROP ID - SERVICES DISABLED
                var result = $.parseJSON(data);
                if(result['prop_conn_type'] != null)
                {
                    var html_refe_7 = "";
                    $.each( result['prop_conn_type'], function( key, value )
                    {
                        var prop_conner = value[1]+'-'+value[2];
                        html_refe_7 += "<option value="+value[1]+" data-conn_prop="+prop_conner+">"+value[0]+"</option>";
                    });
                    $("#refe_rec_7").html(html_refe_7);
                    $("#refe_rec_7").val(prop_id);

                    $("#conn_id_des").val(conn_id);
                    $("#conn_id_prop_rec").val(conn_id);
                    $("#conn_assoc_prop_status_7").html('<input type=hidden name=rec_assoc value=1>');
                }
            }
        });


    }
    else
    {
        $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_conn_type': '1', 'con_type': con_type}})
        .done(function( data )
        {
            if(data != null)
            {
                // PROP ID - WITH SERVICES OR NOT SERVICES
                var result = $.parseJSON(data);
                if(result['prop_conn_type'] != null)
                {
                    var html_refe_7 = "";
                    $.each( result['prop_conn_type'], function( key, value )
                    {
                        var prop_conner = value[1]+'-'+value[2];
                        html_refe_7 += "<option value="+value[1]+" data-conn_prop="+prop_conner+">"+value[0]+"</option>";
                    });
                    $("#refe_rec_7").html(html_refe_7);
                    $("#refe_rec_7").val(prop_id);

                    $("#conn_id_des").val(conn_id);
                    $("#conn_id_prop_rec").val(conn_id);
                }
            }
        });
        
    }

    // PROP ID BY CUSTOMER 
    subscriber_lead_sub_reconnection(lead_sub, prop_id);

}


// CHANGE OVER ATRIBUIDO POR DEFEITO - INSERIDO NOS ESTADOS 1 A 5 - LEADS

// ESTADO 30 - LEADS

function ChgOverPropConnType(prop_id,con_type,con_type_ant,conn_id,lead_sub)
{
    
    // CONNECTIONS CHANGE OVER

    changeOverCOnnectionType(con_type,con_type_ant);

    // PROPERTIES CHANGE OVER ANT CONNECTION

    changeOverPropertiesConnections(con_type_ant, prop_id);
    
    // CONNECTIONS
    
    connection_prop_id_Change(prop_id,con_type_ant, conn_id);
    
    // SUBSCRIBER

    subscriber_lead_sub_change_over(lead_sub,prop_id);
    
    
}

function subscriber_lead_sub_change_over(lead_sub,prop_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'subscriber_list': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
                var result = $.parseJSON(data);
                var owner_html = '';
                    // SUBSCRIBER
                    for(var i=0; i<result['list_cuts'].length; i++)
                    {
                        owner_html += '<option value="' + result['list_cuts'][i][1] + '">' + result['list_cuts'][i][0] + '</option>';
                    }

                    $("#owner_chg").html(owner_html);
                    if(lead_sub != 0 || lead_sub != "")
                    {
                        $("#owner_chg").val(lead_sub);
                    }
                    else
                    {
                        // Subscriber
                        $.each( result['subs'], function( key, value )
                        {
                            console.log(value['id']);
                            var id_cust = value['id'];
                            var nome = value['name'];
                            var fiscal = value['fiscal_nr'];
                            var span_owner=id_cust+"-"+nome+"#"+fiscal;
                            $("#select2-owner_chg-container").attr('title', span_owner);
                            $("#select2-owner_chg-container").html(span_owner);
                            $("#owner_chg").val(value['id']);
                        });
                    }
                    
        });
}

function subscriber_lead_sub_reconnection(lead_sub,prop_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'subscriber_list': '1', 'prop_id': prop_id}})
        .done(function( data )
        {
                var result = $.parseJSON(data);
                var owner_html = '';
                    // SUBSCRIBER
                    for(var i=0; i<result['list_cuts'].length; i++)
                    {
                        owner_html += '<option value="' + result['list_cuts'][i][1] + '">' + result['list_cuts'][i][0] + '</option>';
                    }

                    $("#owner_rec").html(owner_html);
                    if(lead_sub != 0 || lead_sub != "")
                    {
                        $("#owner_rec").val(lead_sub);
                    }
                    else
                    {
                        // Subscriber
                        $.each( result['subs'], function( key, value )
                        {
                            console.log(value['id']);
                            var id_cust = value['id'];
                            var nome = value['name'];
                            var fiscal = value['fiscal_nr'];
                            var span_owner=id_cust+"-"+nome+"#"+fiscal;
                            $("#select2-owner_rec-container").attr('title', span_owner);
                            $("#select2-owner_rec-container").html(span_owner);
                            $("#owner_rec").val(value['id']);
                        });
                    }                    
        });
}


// LEAD ESTADO 30 - FORMULARIO CHANGE OVER AND RECONNECTION

// RECONNECTION


// CHANGE OVER

// CONNECTION DA CHANGE OVER TYPE - ANTERIOR CONNECTIONS
function changeOverCOnnectionType(con_type, con)
{
        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': con_type}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                // CON TYPE CHANGE OVER

                console.log(result['arr_con']);
                var html_connection = '';
                var owner_html = '';

                for(var j=0; j<result['arr_con'].length; j++)
                {
                    html_connection += '<option value="' + result['arr_con'][j] + '">' + result['arr_con'][j] + '</option>';
                }

                $('#con_type_chg_over').html(html_connection);

                $('#con_type_chg_over').val(con);
            });
}

// CHANGE OVER DAS CONNECTIOSN ANTERIORES DAS PROPRIEDADES

function changeOverPropertiesConnections(con_type_ant,prop_id)
{

    $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_chg_over': '1', 'type': con_type_ant}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                console.log(result);

                var html_connection = '';

                for(var j=0; j<result.length; j++)
                {
                    html_connection += '<option value="' + result[j][1] + '">' + result[j][0] + '</option>';
                }

                $('#refe').html(html_connection);
                $('#refe').val(prop_id);

            });


}

// OBTER A CONNECTION DA PROP ID 
// LEADS 1 & 5 - 30
function connection_prop_id_Change(prop_id, type_con_chg_over, connect_id)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Connections... <div id="size_roller"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#conn_type_fsan").html(form_conn);
    $("#refe").prop('disabled', true);
    $("#con_id").prop('disabled', true);
    $("#owner_chg").prop('disabled', true);
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_id_type_connection_chnage_over': '1', 'prop_id': prop_id, 'type': type_con_chg_over}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            if(!isObjectEmpty(result))
            {
                var conn_id;
                // Connection da Change Over
                $.each( result['conexoes'], function( key, value )
                {
                    //conn_id = new Option(value['connection_id']+ "- "+value['referencia'], value['connection_id']);
					conn_id += "<option value="+value['connection_id']+">"+value['connection_id']+ "- "+value['referencia']+"</option>";
                    //con_id.options.add(conn_id);
                });
				
				$("#con_id").html(conn_id);
                $("#con_id").val(connect_id);

                connections_list(connect_id);

            }
        });




}

// --------------------------------------------------------------- ESTADO 30 -----------------------------------------------------------------------------------------

// CONTAGEM PARA ATIVAR E DESATIVAR O FORMULARIO DE ADICIONAR CLIENTES CASO FOR RECONNECTION = 1
// LEADS = ESTADO 30 = ASSINATURA DE CONTRATOS (IS_RECONNECTION = 1)
// LEADS.PHP - ESTADO 30
// ?propleads=1&lead_id=9355
var cont = 0;
var cont_chg_over = 0;
var cont_rec = 0;

var d=0;

// ADICIONAR NOVO CLIENTE - LEAD 30 - RECONNECTION
function addClientNew()
{

    var chg = $("#changeover_form").val();
    var rec = $("#reconnection_form").val();

    console.log(chg, rec);

    if(chg == 1)
    {
        console.log(cont_chg_over);
        cont_chg_over++;
        if(cont_chg_over % 2 == 0)
        {
            $("#add_new_client_chg_over").fadeOut();
            $("#add_new_client_chg_over").fadeOut("slow");
            $("#add_new_client_chg_over").fadeOut(3000);
        }
        else
        {
            $("#add_new_client_chg_over").fadeIn();
            $("#add_new_client_chg_over").fadeIn("slow");
            $("#add_new_client_chg_over").fadeIn(3000);
        }
    }
    else if(rec == 1)
    {
        cont_rec++;
        if(cont_rec % 2 == 0)
        {
            $("#add_new_client_reconn").fadeOut();
            $("#add_new_client_reconn").fadeOut("slow");
            $("#add_new_client_reconn").fadeOut(3000);
        }
        else
        {
            $("#add_new_client_reconn").fadeIn();
            $("#add_new_client_reconn").fadeIn("slow");
            $("#add_new_client_reconn").fadeIn(3000);
        }
    }
    else
    {
        cont++;
        if(cont % 2 == 0)
        {
            $("#add_new_client").fadeOut();
            $("#add_new_client").fadeOut("slow");
            $("#add_new_client").fadeOut(3000);
        }
        else
        {
            $("#add_new_client").fadeIn();
            $("#add_new_client").fadeIn("slow");
            $("#add_new_client").fadeIn(3000);
        }
    }
}


function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
  }


function validateCustForm(lead_id)
{
    var chg = $("#changeover_form").val();
    var rec = $("#reconnection_form").val(); 
    var form_client = '';
    var formready=0;
    var formready_rec=0;
    var formready_chg_over=0;

    if(chg == 1)
    {
        form_client = "#add_new_client_chg_over";
        
    }
    else if(rec == 1)
    {
        form_client = "#add_new_client_reconn";
    }
    else
    {
        form_client = "#add_new_client";
    }


    
    // Nome Cust
    var name_cust = $(form_client + " #name_cust").val();
    // ENDERECO
    var address_cust = $(form_client + " #address_cust").val();
    // EMAIL
    var email = $(form_client + " #email_cust").val();
    // TELEFONE
    var telef = $(form_client + " #telef_cust").val();
    // NUMERO FISCAL
    var fiscal_nr = $(form_client + " #fiscal_nr_cust").val();

    

    if (name_cust == "")
    {
            $(form_client + " #divname").html("<b>Name:</b> <font color=red>*</font>");
    }
    else
    {
        $(form_client + " #divname").html("<b>Name:</b> <font color=green>*</font>");
        if(chg == 1)
        {
            formready_chg_over += 1;
            
        }
        else if(rec == 1)
        {
            formready_rec += 1;
        }
        else
        {
            formready += 1;
        }
    }


    // Address
    // SE NAO TEM O CAMPO ENDERECO PREENCHIDO
    if (address_cust == "")
    {
        $(form_client + " #divbillingaddr").html("<b>Billing Address:</b> <font color=red>*</font>");
            //act_button_cust -= 1;
    }
    else
    {
        $(form_client + " #divbillingaddr").html("<b>Billing Address:</b> <font color=green>*</font>");
        if(chg == 1)
        {
            formready_chg_over += 1;
            
        }
        else if(rec == 1)
        {
            formready_rec += 1;
        }
        else
        {
            formready += 1;
        }
    }

    // EXPRESSOA REGULAR DE EMAILS
    var filter =/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/;

    // VERIFICAR SE TME O EMAIL PREENCHIDO
    if(email == "")
    {
        $(form_client + " #divemailcust").html("<b>Email:</b> <font color=red>*</font>");
    }
    else
    {
        // VERIFICAR SE TEM EMAIL VÁLIDO
        if (!filter.test(email))
        {
            $(form_client + " #divemailcust").html("<b>Email:</b> <font color=red>*</font>");
        }
        else
        {
            $(form_client + " #divemailcust").html("<b>Email:</b> <font color=green>*</font>");
            if(chg == 1)
            {
                formready_chg_over += 1;
                
            }
            else if(rec == 1)
            {
                formready_rec += 1;
            }
            else
            {
                formready += 1;
            }
        }
    }

    // VERIFICAR SE NUMERO FISCAL E PREENCHIDO 
    var filter_nr_fiscal =/^[+]?([0-9])+$/;
    if(fiscal_nr == "")
    {
        $(form_client + " #divfiscalnumber").html("<b>Fiscal Number:</b> <font color=red>*</font>");
        //act_button_cust -= 1;
    }
    else
    {
        if(isNumber(fiscal_nr))
        {
            // VERIFICAR SE O NUMERO FISCAL É UM NUMERO INTEIRO 
            if (!filter_nr_fiscal.test(fiscal_nr))
            {
                $(form_client + " #divfiscalnumber").html("<b>Fiscal Number:</b> <font color=red>*</font>");
            }
            else
            {
                // NUMERO FISCAL TEM 9 CARACTERES (TAMANHO)
                if(chg == 1)
                {
                    formready_chg_over += checkFiscalNumber(fiscal_nr, form_client);
                }
                else if(rec == 1)
                {
                    formready_rec += checkFiscalNumber(fiscal_nr, form_client);
                }
                else
                {
                    formready += checkFiscalNumber(fiscal_nr, form_client);
                }
            }
        }
        else
        {
            $(form_client + " #divfiscalnumber").html("<b>Fiscal Number:</b> <font color=red>*</font>");
            $(form_client + " #fiscal_num_warn").html("<br><font color=red>The fiscal number must be a number</font>");
        }
        

    }


    // EXPRESSAO REGULAR DE NUMERO DE TELEFONE
    var filter_phone =/^[+]?([0-9])+$/;
    // VERIFICAR SE O NUMERO DE TELEFONE ESTA PREENCHIDO
    if(telef == "")
    {
        $(form_client + " #divphone").html("<b>Phone:</b> <font color=red>*</font>");
    }
    else
    {
        // NUMERO FISCAL POSSUI SO NUMEROS
        if (!filter_phone.test(telef))
        {
            $(form_client + " #divphone").html("<b>Phone:</b> <font color=red>*</font>");
        }
        else
        {
            $(form_client + " #divphone").html("<b>Phone:</b> <font color=green>*</font>");
            if(chg == 1)
            {
                formready_chg_over += 1;
            }
            else if(rec == 1)
            {
                formready_rec += 1;
            }
            else
            {
                formready += 1;
            }
        }
    }

    // CAMPOS QUE ESTAO PREENCHIDOS E SEM ERROS DE PREENCHIMENTO (TELEFONE, NOME, EMAIL, NUMERO FISCAL, ENDERECO)
    // SE OS CAMPOS ESTAM BEM PREENCHIDOS ENTAO O BOTAO DE SUBMISSAO DE ADICIONAR CLIENTES ESTA ATIVADO
    if(formready_chg_over == 5 && formready_rec != 5 && formready != 5)
    {
        $(form_client + " #new_cust").prop('disabled', false);
        $(form_client + " #new_cust").attr('onClick', 'NewCustomerState30('+lead_id+',"'+form_client+'");');
    }

    else if(formready_rec == 5 && formready_chg_over != 5 && formready != 5) 
    {
        $(form_client + " #new_cust").prop('disabled', false);
        $(form_client + " #new_cust").attr('onClick', 'NewCustomerState30('+lead_id+',"'+form_client+'");');
    }

    else if(formready == 5 && formready_chg_over != 5 && formready_rec != 5)
    {
        $(form_client + " #new_cust").prop('disabled', false);
        $(form_client + " #new_cust").attr('onClick', 'NewCustomerState30('+lead_id+',"'+form_client+'");');
    }

    else
    {
        $(form_client + " #new_cust").prop('disabled', true);
    }

}



function checkFiscalNumber(num_fiscal, form_client)
{
    // SE POSSUI VALORES NO CAMPO NUMERO FISCAL NO FORMUALRIO ADICIONAR CLIENTES NA LEAD 30 - ADD CLIENT
        var val=0;

        $.ajax({
            async: false,
            url: 'webservice.php',
            type: 'get',
            data: { 'num_fiscal_customer': '1', 'num_fiscal': num_fiscal},
            success: function(data)
            {
                var result = $.parseJSON(data);
                    // SE UM NUMERO E POSSUI 9 NUMEROS E QUE NAO PODE POSSUI O VALOR '999999990'E QUE TEM TER NUMEROS DIFERENTES DOS OUTTROS CLIENTES
                    if(result['eq_check'] == 1)
                    {
                        $(form_client + " #divfiscalnumber").html("<b>Fiscal Number:</b> <font color=green>*</font>");
                        val = 1;
                    }
                    // SE E UM NUMERO E NAO POSSUI 9 NUMEROS E POSSUI O VALOR '999999990' E QUE TEM NUMERO IGUAL A UM DOS CLIENTES PERTENCENTES NA LISTA DE CUSTOMERS NA BASE DE DADOS
                    else if(result['eq_check'] == 0)
                    {
                        $(form_client + " #divfiscalnumber").html("<b>Fiscal Number:</b> <font color=red>*</font>");
                        val = 0;
                    }
                    $(form_client + " #fiscal_num_warn").html("<br>"+result['msg']);  
                    
            }
        });
        return val;
}


// NEW CUSTOMER NO ESTADO 30 - IS RECONNECTION
// SUBMISSAO DE ADICIONAR NOVO CLIENTE NO ESTADO 30 NO CASO DE FOR RECONNECTION (RADIO BUTTON = RECONNECTION)
// LEADS.PHP - ESTADO 30
// ?propleads=1&lead_id=9355
function NewCustomerState30(lead_id,form_client)
{
    
    // NOME DO UTILIZADOR
    var localuser = $("#localuser_username").html();
    // NOME DO CLIENTE
    var salut = $(form_client + " select[name=salut_cust]").val();
    var name = $(form_client + " input[name=name_cust]").val();
    // EMAIL, & ENDERECO
    var address = $(form_client + " input[name=address_cust]").val();
    var email = $(form_client + " input[name=email_cust]").val();
    // TELEFONE
    var telef = $(form_client + " input[name=telef_cust]").val();
    // NUMERO FISCAL
    var fiscal_nr = $(form_client + " input[name=fiscal_nr_cust]").val();
    // LINGUA
    var lang = $(form_client + " select[name=lang_cust]").val();

    // NOTAS
    var notes = $(form_client + " input[name=notes_cust]").val();

    // OUTRAS OPCOES
    var is_commercial = $(form_client + " input[name=is_commercial_cust]:checked").length;
    var is_management = $(form_client + " input[name=is_management_cust]:checked").length;
    var is_agent = $(form_client + " input[name=is_agent_cust]:checked").length;

    //console.log(localuser,salut, name, address, email, telef, fiscal_nr, lang, notes, is_commercial, is_management, is_agent);
    
    $.ajax({

        // setting the url
        url: "webservice.php",
        type: "POST",

        data: {
            'new_customer_post': '1',
            'localuser': localuser,
            'salut': salut,
            'name': name,
            'address': address,
            'email': email,
            'telef': telef,
            'fiscal_nr': fiscal_nr,
            'lang': lang,
            'is_commercial': is_commercial,
            'is_management': is_management,
            'is_agent': is_agent,
            'notes': notes,
            'lead_id': lead_id
        },

        success: (function (data) {

            var result = $.parseJSON(data);
            // MOSTRAR OS ERROS DA MENSAGEM DE ERROR CASO QUE FALTA CAMPOS A PREENCHER
            if(result['msg'] != '' && result['error'] == '' && result['succ'] == '')
            {
                $(form_client + " #warn_submit_cust").html(result['msg']);
            }
            // SUBMISSOAO DO FORMULARIO DE ADICIONAR CLIENTE FEITO COM SUCESSO
            else if(result['msg'] == '' && result['error'] == '' && result['succ'] != '')
            {
                $(form_client + " #warn_submit_cust").html(result['succ']);
                // APAGAR OS DADOS
                $(form_client + " input[name=name_cust]").val('');
                $(form_client + " input[name=address_cust]").val('');
                $(form_client + " input[name=email_cust]").val('');
                $(form_client + " input[name=telef_cust]").val('');
                $(form_client + " input[name=fiscal_nr_cust]").val('');
                // DESATIVAR AS CHECKBOXES
                $(form_client + " input[name=is_commercial_cust]").prop("checked", false);
                $(form_client + " input[name=is_management_cust]").prop("checked", false);
                $(form_client + " input[name=is_agent_cust]").prop("checked", false);

                $(form_client + " input[name=notes_cust]").val('');

                $(form_client + " #fiscal_num_warn").html('');

                // ADICIONAR O CUSTOMER A SELECT LIST
                var html = '';

                $.each( result['owner'], function( key, value )
                    {
                        html += '<option value="' + value[1] + '">' + value[0] + '</option>';

                    });

                //Reconnection
                if(form_client.match("_reconn"))
                {
                    $("#owner_rec").html(html);
                    $("#owner_rec").val(result['owner_select']);
                }
                //Change Over
                else if(form_client.match("_chg_over"))
                {
                    $("#owner_chg").html(html);
                    $("#owner_chg").val(result['owner_select']);
                }
                //New Connection
                else
                {
                    $("#owner_id").html(html);
                    $("#owner_id").val(result['owner_select']);
                }
            }
            // ERRO DE SUBMISSAO DO FORMULARIO
            else if(result['msg'] == '' && result['error'] != '' && result['succ'] == '')
            {
                $(form_client + " #warn_submit_cust").html(result['error']);
            }
        })

    });



}





function PropLeadNewConnection(lead_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'prop_lead_address': '1', 'lead_id': lead_id}})
        .done(function( data )
        {
            console.log(data);
            var result = $.parseJSON(data);
            console.log(result);
            $("input[name=address]").val(result['address']);
            if(result['subs'] != null)
            {
                $.each( result['freg_arr'], function( key, value )
                {
                    $("#concelho").val(value['conc_id']).trigger("change");
                    updatefregep(value['conc_id'], value['freg_id']);
                });

                $("#ref").val(result['ref']).trigger("change");
                $("#owner_id").val(result['subs']).trigger("change");
            }
            
				

        });
}




















/*
var cont_chg_over = 0;
function addClientNewChgOver()
{
    cont_chg_over++;
    // SIM OU NAO
    if(cont_chg_over % 2 == 0)
    {
        $("#add_new_client_chg_over").fadeOut();
        $("#add_new_client_chg_over").fadeOut("slow");
        $("#add_new_client_chg_over").fadeOut(3000);
    }
    else
    {
        $("#add_new_client_chg_over").fadeIn();
        $("#add_new_client_chg_over").fadeIn("slow");
        $("#add_new_client_chg_over").fadeIn(3000);
    }
}


var cont_new_connection = 0;
function addClientNewConnection()
{
    cont_new_connection++;
    // SIM OU NAO
    if(cont_new_connection % 2 == 0)
    {
        $("#add_new_client_new_connection").fadeOut();
        $("#add_new_client_new_connection").fadeOut("slow");
        $("#add_new_client_new_connection").fadeOut(3000);
    }
    else
    {
        $("#add_new_client_new_connection").fadeIn();
        $("#add_new_client_new_connection").fadeIn("slow");
        $("#add_new_client_new_connection").fadeIn(3000);
    }
}*/




/*
function NewCustomerState30ChangeOver(lead_id)
{
    // NOME DO UTILIZADOR
    var localuser = $("#localuser_username").html();
    // NOME DO CLIENTE
    var salut = $("select[name=salut_cust_chg_over]").val();
    var name = $("input[name=name_cust_chg_over]").val();
    // EMAIL, & ENDERECO
    var address = $("input[name=address_cust_chg_over]").val();
    var email = $("input[name=email_cust_chg_over]").val();
    // TELEFONE
    var telef = $("input[name=telef_cust_chg_over]").val();
    // NUMERO FISCAL
    var fiscal_nr = $("input[name=fiscal_nr_cust_chg_over]").val();
    // LINGUA
    var lang = $("select[name=lang_cust_chg_over]").val();

    // NOTAS
    var notes = $("input[name=notes_cust_chg_over]").val();

    // OUTRAS OPCOES
    var is_commercial = $("input[name=is_commercial_cust_chg_over]:checked").length;
    var is_management = $("input[name=is_management_cust_chg_over]:checked").length;
    var is_agent = $("input[name=is_agent_cust_chg_over]:checked").length;

    $.ajax({

        // setting the url
        url: "webservice.php",
        type: "POST",

        data: {
            'new_customer_post': '1',
            'localuser': localuser,
            'salut': salut,
            'name': name,
            'address': address,
            'email': email,
            'telef': telef,
            'fiscal_nr': fiscal_nr,
            'lang': lang,
            'is_commercial': is_commercial,
            'is_management': is_management,
            'is_agent': is_agent,
            'notes': notes,
            'lead_id': lead_id
        },

        success: (function (data) {

            var result = $.parseJSON(data);
            // MOSTRAR OS ERROS DA MENSAGEM DE ERROR CASO QUE FALTA CAMPOS A PREENCHER
            if(result['msg'] != '' && result['error'] == '' && result['succ'] == '')
            {
                $("#warn_submit_cust_chg_over").html(result['msg']);
            }
            // SUBMISSOAO DO FORMULARIO DE ADICIONAR CLIENTE FEITO COM SUCESSO
            else if(result['msg'] == '' && result['error'] == '' && result['succ'] != '')
            {
                $("#warn_submit_cust_chg_over").html(result['succ']);
                // APAGAR OS DADOS
                $("input[name=name_cust_chg_over]").val('');
                $("input[name=address_cust_chg_over]").val('');
                $("input[name=email_cust_chg_over]").val('');
                $("input[name=telef_cust_chg_over]").val('');
                $("input[name=fiscal_nr_cust_chg_over]").val('');
                // DESATIVAR AS CHECKBOXES
                $("input[name=is_commercial_cust_chg_over]").prop("checked", false);
                $("input[name=is_management_cust_chg_over]").prop("checked", false);
                $("input[name=is_agent_cust_chg_over]").prop("checked", false);

                $("input[name=notes_cust_chg_over]").val('');

                $("#fiscal_num_warn_chg_over").html('');

                // change over
                    // ADICIONAR OS DADOS DO CLIENTE A LISTA DE CLIENTES NA FIELD OWNRE ID (SUBSCRIBER) - FUNCIONA NAS RECONNECTIONS
                    var owner_id=document.getElementById("owner_chg");
                    owner_id.innerHTML = "";
                    var owner_id_b;
                    // LISTAR OS CLIENTES NUMA SELECT BOX
                    $.each( result['owner'], function( key, value )
                    {
                        owner_id_b = new Option(value[0], value[1]);
                        owner_id.options.add(owner_id_b);
                    });

                    // SELECCIONAR O CLIENTE ADICIONADO NA LISTA DE CLIENTES (SUBSCRIBERS) - FUNCIONA NAS RECONNECTIONS
                    $("#owner_chg").val(result['owner_select']);




            }
            // ERRO DE SUBMISSAO DO FORMULARIO
            else if(result['msg'] == '' && result['error'] != '' && result['succ'] == '')
            {
                $("#warn_submit_cust_chg_over").html(result['error']);
            }
        })

    });



}


function NewCustomerState30NewConnection(lead_id)
{
    // NOME DO UTILIZADOR
    var localuser = $("#localuser_username").html();
    // NOME DO CLIENTE
    var salut = $("select[name=salut_cust_new_connection]").val();
    var name = $("input[name=name_cust_new_connection]").val();
    // EMAIL, & ENDERECO
    var address = $("input[name=address_cust_new_connection]").val();
    var email = $("input[name=email_cust_new_connection]").val();
    // TELEFONE
    var telef = $("input[name=telef_cust_new_connection]").val();
    // NUMERO FISCAL
    var fiscal_nr = $("input[name=fiscal_nr_cust_new_connection]").val();
    // LINGUA
    var lang = $("select[name=lang_cust_new_connection]").val();

    // NOTAS
    var notes = $("input[name=notes_cust_new_connection]").val();

    // OUTRAS OPCOES
    var is_commercial = $("input[name=is_commercial_cust_new_connection]:checked").length;
    var is_management = $("input[name=is_management_cust_new_connection]:checked").length;
    var is_agent = $("input[name=is_agent_cust_new_connection]:checked").length;

    $.ajax({

        // setting the url
        url: "webservice.php",
        type: "POST",

        data: {
            'new_customer_post': '1',
            'localuser': localuser,
            'salut': salut,
            'name': name,
            'address': address,
            'email': email,
            'telef': telef,
            'fiscal_nr': fiscal_nr,
            'lang': lang,
            'is_commercial': is_commercial,
            'is_management': is_management,
            'is_agent': is_agent,
            'notes': notes,
            'lead_id': lead_id
        },

        success: (function (data) {

            var result = $.parseJSON(data);
            // MOSTRAR OS ERROS DA MENSAGEM DE ERROR CASO QUE FALTA CAMPOS A PREENCHER
            if(result['msg'] != '' && result['error'] == '' && result['succ'] == '')
            {
                $("#warn_submit_cust_new_connection").html(result['msg']);
            }
            // SUBMISSOAO DO FORMULARIO DE ADICIONAR CLIENTE FEITO COM SUCESSO
            else if(result['msg'] == '' && result['error'] == '' && result['succ'] != '')
            {
                $("#warn_submit_cust_new_connection").html(result['succ']);
                // APAGAR OS DADOS
                $("input[name=name_cust_new_connection]").val('');
                $("input[name=address_cust_new_connection]").val('');
                $("input[name=email_cust_new_connection]").val('');
                $("input[name=telef_cust_new_connection]").val('');
                $("input[name=fiscal_nr_cust_cust_new_connection]").val('');
                // DESATIVAR AS CHECKBOXES
                $("input[name=is_commercial_cust_new_connection]").prop("checked", false);
                $("input[name=is_management_cust_new_connection]").prop("checked", false);
                $("input[name=is_agent_cust_cust_new_connection]").prop("checked", false);

                $("input[name=fiscal_num_warn_new_connection]").val('');

                $("#fiscal_num_warn_new_connection").html('');

                // change over
                    // ADICIONAR OS DADOS DO CLIENTE A LISTA DE CLIENTES NA FIELD OWNRE ID (SUBSCRIBER) - FUNCIONA NAS RECONNECTIONS
                    var owner_id=document.getElementById("owner_id");
                    owner_id.innerHTML = "";
                    var owner_id_b;
                    // LISTAR OS CLIENTES NUMA SELECT BOX
                    $.each( result['owner'], function( key, value )
                    {
                        owner_id_b = new Option(value[0], value[1]);
                        owner_id.options.add(owner_id_b);
                    });

                    // SELECCIONAR O CLIENTE ADICIONADO NA LISTA DE CLIENTES (SUBSCRIBERS) - FUNCIONA NAS RECONNECTIONS
                    $("#owner_id").val(result['owner_select']);




            }
            // ERRO DE SUBMISSAO DO FORMULARIO
            else if(result['msg'] == '' && result['error'] != '' && result['succ'] == '')
            {
                $("#warn_submit_cust_new_connection").html(result['error']);
            }
        })

    });



}*/

// PROPERTY ID QUE VAI BUSCAR O PROPERTARIO (FUNCIONA NA NEW CONNECTION)
function owner_prop(owner_id)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'owner_id_prop': '1', 'owner_id': owner_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            if(result != null)
            {
                var id_prop = result['prop_cust'][0]['id'];
                var type = result['prop_cust'][0]['type'];
                $("#ref").val(id_prop);
                //$("#con_type").val(type);
                //updatecpe(type);
            }
        });
}

// UPDATE DA INTERNET DO TYPE CONNECTION ESCOLHIDO NO ESTADO 30 
function updateInternet(con_type)
{
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'connection_type_prop_internet': '1',
            'type': con_type,
        },
        success: function (data) {

            var result = $.parseJSON(data);

            var internet_prof=document.getElementById("internet_prof");


            internet_prof.innerHTML = "";

            var internet_prof_b;
            internet_prof_b = new Option("no internet", "0");
            internet_prof.options.add(internet_prof_b);
            $.each( result['internet_select'], function( key, value )
            {

                internet_prof_b = new Option(value['name'], value['id']);
                internet_prof.options.add(internet_prof_b);
            });



        }
    });



}




function checkFiscalNumber_new_connection(num_fiscal)
{

    var fiscal_valid = 0;
    var filter_nr_fiscal =/^[+]?([0-9])+$/;
    // SE POSSUI VALORES NO CAMPO NUMERO FISCAL NO FORMUALRIO ADICIONAR CLIENTES NA LEAD 30 - ADD CLIENT
    if(num_fiscal != "")
    {

        $("#fiscal_nr_cust_new_connection").html('');
        
        $.ajax({ method: "GET", url: "webservice.php", data: { 'num_fiscal_customer': '1', 'num_fiscal': num_fiscal}})
            .done(function( data )
            {
                
                var result = $.parseJSON(data);

    
                // VERIFICAR SE O NUMERO FISCAL POSSUI NUMEROS INTEIROS (TEM QUE SER UM NUMERO)
                if (!filter_nr_fiscal.test(num_fiscal))
                {
                    $("#divfiscalnumber_new_connection").html("<b>Fiscal Number:</b> <font color=red>*</font>");
                    $("#fiscal_num_warn_new_connection").html("<font color=red>The Fiscal Number must be a number</font>");
                }
                else
                {
                    // SE UM NUMERO E POSSUI 9 NUMEROS E QUE NAO PODE POSSUI O VALOR '999999990'E QUE TEM TER NUMEROS DIFERENTES DOS OUTTROS CLIENTES
                    if(result['eq_check'] == 1)
                    {
                        $("#divfiscalnumber_new_connection").html("<b>Fiscal Number:</b> <font color=green>*</font>");

                        fiscal_valid += 1;

                    }
                    // SE E UM NUMERO E NAO POSSUI 9 NUMEROS E POSSUI O VALOR '999999990' E QUE TEM NUMERO IGUAL A UM DOS CLIENTES PERTENCENTES NA LISTA DE CUSTOMERS NA BASE DE DADOS
                    else if(result['eq_check'] == 0)
                    {
                        $("#divfiscalnumber_new_connection").html("<b>Fiscal Number:</b> <font color=red>*</font>");
                        //$("#fiscal_num_warn").html("<br>"+result['msg']);
                        //fiscal_valid -= 1;
                    }
                    $("#fiscal_num_warn_new_connection").html("<br>"+result['msg']);
                }


                //$("#equip_assoc_not").val(result['eq_check']);

                d = fiscal_valid;

            });
    }
    else
    {
        // FALTA DE INSERIR O CAMPO DO NUMERO FISCAL
        $("#fiscal_num_warn_new_connection").html('<font color=red>Missing Fiscal Number</font><br>');
        //$("#fiscal_num_warn").html("<br>"+result['msg']);
        //fiscal_valid -= 1;
        fiscal_valid = 0;
        d = fiscal_valid;
    }
}

function checkFiscalNumber_chg_over(num_fiscal)
{

    var fiscal_valid = 0;
    var filter_nr_fiscal =/^[+]?([0-9])+$/;
    // SE POSSUI VALORES NO CAMPO NUMERO FISCAL NO FORMUALRIO ADICIONAR CLIENTES NA LEAD 30 - ADD CLIENT
    if(num_fiscal != "")
    {

        $("#fiscal_num_warn_chg_over").html('');
        
        $.ajax({ method: "GET", url: "webservice.php", data: { 'num_fiscal_customer': '1', 'num_fiscal': num_fiscal}})
            .done(function( data )
            {
                
                var result = $.parseJSON(data);

    
                // VERIFICAR SE O NUMERO FISCAL POSSUI NUMEROS INTEIROS (TEM QUE SER UM NUMERO)
                if (!filter_nr_fiscal.test(num_fiscal))
                {
                    $("#divfiscalnumber_chg_over").html("<b>Fiscal Number:</b> <font color=red>*</font>");
                    $("#fiscal_num_warn_chg_over").html("<font color=red>The Fiscal Number must be a number</font>");
                }
                else
                {
                    // SE UM NUMERO E POSSUI 9 NUMEROS E QUE NAO PODE POSSUI O VALOR '999999990'E QUE TEM TER NUMEROS DIFERENTES DOS OUTTROS CLIENTES
                    if(result['eq_check'] == 1)
                    {
                        $("#divfiscalnumber_chg_over").html("<b>Fiscal Number:</b> <font color=green>*</font>");

                        fiscal_valid += 1;

                    }
                    // SE E UM NUMERO E NAO POSSUI 9 NUMEROS E POSSUI O VALOR '999999990' E QUE TEM NUMERO IGUAL A UM DOS CLIENTES PERTENCENTES NA LISTA DE CUSTOMERS NA BASE DE DADOS
                    else if(result['eq_check'] == 0)
                    {
                        $("#divfiscalnumber_chg_over").html("<b>Fiscal Number:</b> <font color=red>*</font>");
                        //$("#fiscal_num_warn").html("<br>"+result['msg']);
                        //fiscal_valid -= 1;
                    }
                    $("#fiscal_num_warn_chg_over").html("<br>"+result['msg']);
                }


                //$("#equip_assoc_not").val(result['eq_check']);

                d = fiscal_valid;

            });
    }
    else
    {
        // FALTA DE INSERIR O CAMPO DO NUMERO FISCAL
        $("#fiscal_num_warn_chg_over").html('<font color=red>Missing Fiscal Number</font><br>');
        //$("#fiscal_num_warn").html("<br>"+result['msg']);
        //fiscal_valid -= 1;
        fiscal_valid = 0;
        d = fiscal_valid;
    }
}






// --------------------------------------------------------------- ESTADO 50 -----------------------------------------------------------------------------------------


// DESATIVAR OS SERVIÇOS HABILITADOS DE UMA DADA CONNECTION (RECONNECTION = 1)
// LEAD 50
function disabled_services_check_update(dis_serv_rec)
{
    if (dis_serv_rec.checked)
    {

        if(equip == 0 || modelo == 0 || is_olt==0 || is_pon==0 || is_antenna==0)
        {
            $("input[name='supdatelead']").attr('disabled','disabled');
            servico_des = 0;
        }
        else
        {
            $("input[name='supdatelead']").removeAttr('disabled');
            servico_des = 1;
        }

    }
    else
    {
        $("input[name='supdatelead']").attr('disabled','disabled');
    }

}





// ASSOCIA-SE AO EQUIPAMENTO DE UMA DADA TIPO DE CONEXAO - ESTADO 50 - LEADS - FWA & GPON
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function equipConnectionAssoc(equip_id, prop_id, conn_id)
{
    var equip_length = equip_id.length;
    var dis_serv_rec = $("#dis_serv_rec:checked").length;
    $.ajax({ method: "GET", url: "webservice.php", data: { 'equip_connection_assoc': '1', 'equip_id': equip_id, 'prop_id': prop_id, 'conn_id': conn_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            $("#equip_conn_assoc").html("<br>"+result['msg']);
            $("#equip_assoc_not").val(result['eq_check']);
            if(equip_id == "")
            {
                equip = 0;
            }
            else
            {
                if(result['eq_check'] == 0)
                {
                    equip = 0;
                }
                else if(result['eq_check'] != 0)
                {
                    equip = 1;
                    $("#warn_equip").html("");
                }
            }
            if(equip_length >= 15)
            {
                equip = 0;
                $("#equip_conn_assoc").html("<font color=red>The Equipment should not to be more than 15 characteres</font><br>");
            }
            var model = $("#models").val();
            if(model == "" || model == null)
            {
                $("#warn_model").html("<font color=red>Missing Model</font><br>");
                modelo = 0;
            }
            else
            {
                $("#warn_model").html("");
                modelo = 1;
            }
            if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
            {
                $("input[name='supdatelead']").attr('disabled','disabled');
            }
            else
            {
                $("input[name='supdatelead']").removeAttr('disabled');
            }
        });
}

// IDENTIFICAR O EQUIPMENTO ASSOCIADO DA PROPRIEDADE DA CONEXAO CORRESPONDENTE
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function putInitialEquipPropCon(prop_id, con_id, type, lead_id)
{
    var i=0;
    $.ajax({ method: "GET", url: "webservice.php", data: { 'initial_equip_con_prop': '1', 'type': type, 'prop_id': prop_id, 'con_id': con_id, 'lead_id': lead_id}})
        .done(function( data )
        {
            var result = $.parseJSON(data);

            // GPON
            if(type == "GPON")
            {
                // EQUIPAMENTO ID DA CONNECTION
                
                if(result['equip_id'] == "" || result['equip_id'] == null )
                {
                    $("#warn_equip").html("<font color=red>Missing Equipment</font><br>");
                    equip = 0;
                }
                else
                {
                    $("#fsan").val(result['equip_id']);
                    equip = 1;
                    
                }

                // MODELOS

                if(result['me_model'] != "" || result['me_model'] != null )
                {
                    $("#models").val(result['me_model']);
                    $("#select2-models-container").attr('title', result['me_model']);
                    $("#select2-models-container").html(result['me_model']);
                    modelo=1;


                }
                // OLT
                if(result['olt'] == "" || result['olt'] == null || $("#olt_id").val() == 0)
                {
                    is_olt = 0;
                    $("#warn_olt").html("<font color=red>Missing OLT</font><br>");
                }
                else
                {
                    $("#olt_id").val(result['olt']);
                    $("#warn_olt").html("");
                    is_olt=1;

                    // pons

                    var html = '';
                    $.each( result['pon'], function( key, value )
                    {
                        var val = value['card']+"-"+value['pon'];
                        var text =   value['card']+"-"+value['pon']+" - "+value['name'];

                        html += '<option value="' + val + '">' + text + '</option>';

                    });

                    $('#pons').html(html);

                    updatevlan($("#olt_id").val());
                }

                // ONT ID
                if(result['ont_id'] == "" || result['ont_id'] == null || $("#pons").val() == 0)
                {
                    is_pon = 0;
                    $("#warn_pon").html("<font color=red>Missing PON</font><br>");
                }
                else
                {
                    var ont_id = result['ont_id'];
                    const ont_id_array = ont_id.split("-");

                    var pon_id = ont_id_array[1]+"-"+ont_id_array[2];


                    $('#pons').val(pon_id);
                    is_pon=1;
                    $("#warn_olt").html("");


                }



            }
            // FWA
            else if(type == "FWA")
            {
                // EQUIPAMENTO ID DA CONNECTION

                if(result['equip_id'] == "" || result['equip_id'] == null)
                {
                    $("#warn_equip").html("<font color=red>Missing Equipment</font><br>");
                    equip = 0;
                }
                else
                {
                    $("#fsan").val(result['equip_id']);
                    equip = 1;
                }

                // MODELOS

                if(result['model_fwa'] == null || result['model_fwa'] == "")
                {
                    $("#models").val("ltulite");
                }
                else
                {
                    $("#models").val(result['model_fwa']);
                }


                // ANTENNA

                if(result['antenna'] == "" || result['antenna'] == null)
                {
                    $("#warn_antenna").html("<font color=red>Missing Antenna FWA CPE</font><br>");
                    $("#antenna").val("");
                    is_antenna=0;
                }
                else
                {
                    $("#antenna").val(result['antenna']);
                    $("#select2-antenna-container").attr('title', result['antenna_des']);
                    $("#select2-antenna-container").html(result['antenna_des']);
                    $("#warn_antenna").html("");
                    is_antenna=1;
                }
            }


            // NAO MODELOS ASSOCIADOS AO EQUIPAMENTO

            if($("#models").val() == "" || $("#models").val() == null)
            {
                $("#warn_model").html("<font color=red>Missing Model</font><br>");
                modelo = 0;
            }
            else
            {
                $("#warn_model").html("");
                modelo = 1;
            }

            // EQUIPAMENTO DA CONNECTION ONT & ANTENNA

            if($("#fsan").val() == "" || $("#fsan").val() == "ZNTS" || $("#fsan").val() == "aabbcc" )
            {
                $("#warn_equip").html("<font color=red>Missing Equipment</font><br>");
                equip = 0;
            }
            else
            {
                $("#warn_equip").html("");
                equip = 1;
            }

            // VERIFICAR A CONNECTION COM SERVIÇOS DESATIVOS

            // SERVIÇOS DESLIGADO, MODELO, EQUIPAMENTO, PON, OLT, ANTENNA

            if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
            {
                $("input[name='supdatelead']").attr('disabled','disabled');
            }
            else
            {
                $("input[name='supdatelead']").removeAttr('disabled');
            }
        });


}


// MOSTRAR O EQUIPAMENTO QUE TEM UMA PROPRIEDADE SEM TER CRIADO UMA CONNECTION
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function putEquipInitial(prop_id,type, lead_id)
{
    // VERIFICAR SE TEM O EQUIPAMENTO ID 
    if($("#fsan").val() == "" || $("#fsan").val() == "ZNTS" || $("#fsan").val() == "aabbcc" ||  $("#fsan").val() == null)
    {
        // WARNING SE NAO TIVER EQUIPAMENTO ID NA CONNECTION DA PROP
        $("#warn_equip").html("<font color=red>Missing Equipment</font><br>");
        equip = 0;
    }
    else
    {
        // DESPARECER O WARNING DO EQUIPAMENTO ID
        $("#warn_equip").html("");
        equip = 1;
    }

    // VERIFICAR SE TEM MODELO
    if($("#models").val() == "" || $("#models").val() == null)
    {
        // WARNING SE NAO TIVER MODEL NA CONNECTION DA PROP
        $("#warn_model").html("<font color=red>Missing Model</font><br>");

        modelo = 0;
    }
    else
    {
        // DESPARECER O WARNING MODEL
        $("#warn_model").html("");

        modelo = 1;
    }


    // GPON
    if(type == "GPON")
    {
        // OLT
        // VERIFICAR SE TEM OLT ID
        if($("#olt_id").val() == "" || $("#olt_id").val() == null || $("#olt_id").val() == 0)
        {
            // WARNING SE NAO TIVER OLT ID NA FTTH_ONT
            $("#warn_olt").html("<font color=red>Missing OLT</font><br>");
            is_olt = 0;
        }
        else
        {
            $("#warn_olt").html("");
            is_olt = 1;
        }

        // PON
        // VERIFICAR SE TEM A PON
        if($("#pons").val() == "" || $("#pons").val() == 0)
        {
            // WARNING SE NAO TIVER PON NA FTTH_ONT
            is_pon = 0;
            $("#warn_pon").html("<font color=red>Missing PON</font><br>");

        }
        else
        {
            is_pon=1;
            $("#warn_pon").html("");
        }
    }

    //FWA
    else if(type == "FWA")
    {
        // VERIFICAR SE TEM ANTENNA
        if($("#antenna").val() == "" || $("#antenna").val() == 0)
        {
            // WARNING SE NAO TIVER ANTENNA NA FWA_CPE
            $("#warn_antenna").html("<font color=red>Missing Antenna FWA CPE</font><br>");
            is_antenna=0;

        }
        else
        {
            $("#warn_antenna").html("");
            is_antenna=1;
        }
    }


}

// MUDANÇA DO MODELO DO EQUIPAMENTO ASSOCIADO
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function ModelChg(model)
{
    var dis_serv_rec = $("#dis_serv_rec:checked").length;
    if(model != "")
    {
        modelo = 1;
        $("#warn_model").html('');

    }
    else
    {
        modelo = 0;
        $("#warn_model").html('<font color=red>Missing Model</font><br>');
    }

    // SERVIÇOS DESLIGADO, MODELO, EQUIPAMENTO, PON, OLT, ANTENNA

    if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
    {
        $("input[name='supdatelead']").attr('disabled','disabled');
    }
    else
    {
        $("input[name='supdatelead']").removeAttr('disabled');
    }


}

// MUDANCA DA PON
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function PONChg(pon)
{
    var dis_serv_rec = $("#dis_serv_rec:checked").length;
    if(pon == "" || pon == 0)
    {
        is_pon = 0;
        $("#warn_pon").html('<font color=red>Missing PON</font><br>');
    }
    else
    {
        is_pon = 1;
        $("#warn_pon").html('');

    }

    // SERVIÇOS DESLIGADO, MODELO, EQUIPAMENTO, PON, OLT, ANTENNA

    if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
    {
        $("input[name='supdatelead']").attr('disabled','disabled');
    }
    else
    {
        $("input[name='supdatelead']").removeAttr('disabled');
    }
}

// MUDANCA DA ANTENNA
// LEADS.PHP - ESTADO 50
// ?propleads=1&lead_id=9355
function AntennaChg(antenna)
{
    var dis_serv_rec = $("#dis_serv_rec:checked").length;
    if(antenna == "" || antenna == 0)
    {
        is_antenna = 0;
        $("#warn_antenna").html('<font color=red>Missing Antenna</font><br>');
    }
    else
    {
        is_antenna = 1;
        $("#warn_antenna").html('');
    }

    if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
    {
        $("input[name='supdatelead']").attr('disabled','disabled');
    }
    else
    {
        $("input[name='supdatelead']").removeAttr('disabled');
    }
}


function updatepon(olt)
{
    if(olt == "" || olt == 0)
    {
        is_olt = 0;
        $("#warn_olt").html('<font color=red>Missing OLT</font><br>');
    }
    else
    {
        is_olt = 1;
        $("#warn_olt").html('');
    }
    $.ajax({ method: "GET", url: "webservice.php", data: { 'ponsbyolt': olt}})
        .done(function( data )
        {

            var result = $.parseJSON(data);
            var pona=document.getElementById("pons");
            pona.innerHTML = "";
            var ponb;
            if(result != null)
            {
                $.each( result, function( key, value )
                {
                    ponb = new Option(value[0]+' - '+value[1], value[0]);
                    pona.options.add(ponb);
                });


                if($("#pons").val() != null)
                {
                    is_pon = 1;
                    $("#warn_pon").html("");
                }
            }
            else if(result == null)
            {
                ponb = new Option("Select PON", "0");
                pona.options.add(ponb);
                is_pon = 0;
                $("#warn_pon").html("<font color=red>Missing PON</font><br>");


            }

            if(modelo == 0 || equip == 0 || is_pon==0 || is_olt==0 || is_antenna==0)
            {
                $("input[name='supdatelead']").attr('disabled','disabled');
            }
            else
            {
                $("input[name='supdatelead']").removeAttr('disabled');
            }

        });


}



// UPDATE VLAN 
function updatevlan(olt)
{
    $.ajax({ method: "GET", url: "webservice.php", data: { 'vlansbyolt': olt}})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var pona=document.getElementById("vlans");
            pona.innerHTML = "";
            var ponb;
            // LISTAR AS VLANS DA OLT ID DO TYPE CONNECTION GPON
            $.each( result, function( key, value )
            {
                ponb = new Option(value[1]+' - '+value[3]+' of '+value[2], value[0]);
                pona.options.add(ponb);
            });
        });
}



//---------------------------------------------------------------------------------------------------------------------------------------------------------------------

// props.php & services.php e outros

// Fazer uma nova conexao caso que a propriedade nao tem conexoes
function new_con_prop(prop_id)
{
    var loca_conn = '?props=1&conadd='+prop_id;

    location.replace(loca_conn);
}


function changeOverState(change_over)
{

    if (change_over.checked)
    {
        change_over.checked = true; // Check
        document.getElementById("conexao_changeOVER").style.display = "block";
        //conexao_RECONNECTION

        $("#ref").prop('disabled', true);
        $("#owner_id").prop('disabled', true);

        $("#freg").prop('disabled', true);
        $("#concelho").prop('disabled', true);

        $("#text_conn_prop").html('Property Change Over Connection:');

        // SELECCIONE AS PROPERTIES DIFERNTES DAS CONEXOES

        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': $("#con_type").val()}})
            .done(function( data )
            {

                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {

                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });

                    con_prop_type(result[0][1]);
                }
            });



    }
    else
    {
        $("#ref").prop('disabled', false);
        $("#owner_id").prop('disabled', false);

        $("#freg").prop('disabled', false);
        $("#concelho").prop('disabled', false);
        document.getElementById("conexao_changeOVER").style.display = "none";
        //document.getElementById("conexao_RECONNECTION").style.display = "none";
    }

}

function changeConOver(con_type)
{

    var checked = $("#is_changeover:checked").length;

    // SE A OPCAO FOR CHANGE OVER LISTA AS CONEXOES DIFERENTE DA CONEXAO CORRESPONDENTE
    if(checked == 1)
    {
        $.ajax({ method: "GET", url: "webservice.php", data: { 'conn_prop_id_diff': '1', 'type': con_type}})
            .done(function( data )
            {
                var result = $.parseJSON(data);
                var refe=document.getElementById("refe");
                if (typeof(refe) != 'undefined' && refe != null)
                {
                    refe.innerHTML = "";
                    var refeb;
                    $.each( result, function( key, value )
                    {

                        refeb = new Option(value[0], value[1]);
                        refe.options.add(refeb);
                    });
                    con_prop_type(result[0][1]);
                }
            });
    }
}





















function validate_form_search_serv()
{
    var prop_ref = $("#input_prop_ref").val();
    var prop_address = $("#input_prop_address").val();
    var con_type = $("#con_type_serv").val();
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: { 'search_validate_serv_disabled': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type } ,
        success: function (data) {
            var table_body = '';
            var linhas_dados = '';

            var result = $.parseJSON(data);

            if(result['list_conn_des_serv'] != null)
            {
                for(var i=0; i<result['list_conn_des_serv'].length; i++)
                {
                    table_body += '<tr>' +
                        '<td><a href=?props=1&propid='+result['list_conn_des_serv'][i].prop_id+'>'+result['list_conn_des_serv'][i].ref_prop+'</a></td>' +
                        '<td>'+result['list_conn_des_serv'][i].prop_addr+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].conn_type+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].code_area+'</td>' +


                        '</tr>';






                }

                // LInhas de Dados

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];

                if(num > 50)
                {
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset=0&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">|<</a>";
                    }
                    if(curpage>2)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset="+(curpage-3)*50 +"&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset="+(curpage-2)*50 +"&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">"+(curpage-1)+"</a> ";
                    }

                    linhas_dados += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset="+(curpage)*50 +"&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset="+(curpage+1)*50+"&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=NOS&offset="+(lastp-1)*50+"&con_type="+con_type+"&addr="+prop_address+"&prop_ref="+prop_ref+">>|</a> ";
                    }

                }

                linhas_dados += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#serv_conn_disabled").html(table_body);

                $("#paging").html(linhas_dados);
            }
            else
            {
                $("#serv_conn_disabled").html("No Result Matching Search");

                linhas_dados += "showing 0 to 0 of 0 results";

                $("#paging").html(linhas_dados);
            }
        },
        error: function () {
            alert("error");
        }
    });


    /*$.ajax({ method: "POST", url: "webservice.php", data: { 'search_validate_serv_disabled': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type }})
        .done(function( data )
        {




        });*/





}

var cont_show_ena_serv = 0;
function ShowEnabledServices(conn_id, type)
{
    enabledServicesConn(conn_id, type);
}

function disabledServicesConn(conn_id, type)
{
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'disabled_serv_conn': '1',
            'conn_id': conn_id,
            'type': type
        },
        success: function (data) {
            var table_services = '';
            var result = $.parseJSON(data);

            $("#dis_serv_lists-"+conn_id).html(result['text']);


        }
    });
}

function enabledServicesConn(conn_id, type)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Services Enabled... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -500px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#dis_serv_lists-"+conn_id).html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'enabled_serv_conn': '1',
            'conn_id': conn_id,
            'type': type
        },
        success: function (data) {
            var table_services = '';
            var result = $.parseJSON(data);

            $("#dis_serv_lists-"+conn_id).html(result['text']);


        }
    });
}

function ShowEnabledServicesEachTypeRecent(conn_id, type)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Services Disabled Each Type... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -630px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#dis_serv_lists-"+conn_id).html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'disabled_serv_conn_each_type_recent': '1',
            'conn_id': conn_id,
            'type': type
        },
        success: function (data) {
            var table_services = '';
            var result = $.parseJSON(data);

            $("#dis_serv_lists-"+conn_id).html(result['text']);

            if(result['dis_serv_id'] != null)
            {
                for(var i=0; i<result['dis_serv_id'].length; i++)
                {
                    //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                    mouseEntreDisServer(result['dis_serv_id'][i]);
                }
            }

            if(result['dis_conn_id'] != null)
            {
                for(var i=0; i<result['dis_conn_id'].length; i++)
                {
                    //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                    mouseEntreDisConnect(result['dis_conn_id'][i]);
                }
            }






        }
    });
}

var cont_all_services = 0;
function ShowAllServices(conn_id, type)
{
    var form_conn = '';
    var enabled = $("#enabled_services-"+conn_id).val();
    var disabled = $("#disabled_services-"+conn_id).val();

    //var en_serv_conn = $("#click_en_serv-"+conn_id).val();

    var en_s = parseInt($("#click_en_serv-"+conn_id).val()) + 1;
    var dis_s = parseInt($("#click_des_serv-"+conn_id).val()) + 1;

    if(disabled == 0 && enabled == 1)
    {
        $("#click_en_serv-"+conn_id).val(en_s);
        if($("#click_en_serv-"+conn_id).val() % 2 == 0)
        {
            enabledServicesConn(conn_id, type);
            $("#en_serv-"+conn_id).html('Show All Services');
        }
        else
        {
            var form_conn = '';
            form_conn += '<span id=wat_rello>Waiting for List All Services ... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -400px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
            $("#dis_serv_lists-"+conn_id).html(form_conn);
            $.ajax({
                url: 'webservice.php',
                type: 'GET',
                data: {
                    'show_all_services': '1',
                    'conn_id': conn_id,
                    'type': type
                },
                success: function (data) {
                    var table_services = '';
                    var result = $.parseJSON(data);

                    $("#dis_serv_lists-"+conn_id).html(result['text']);

                    if(result['dis_serv_id'] != null)
                    {
                        for(var i=0; i<result['dis_serv_id'].length; i++)
                        {
                            //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                            mouseEntreDisServer(result['dis_serv_id'][i]);
                        }
                    }

                    if(result['dis_conn_id'] != null)
                    {
                        for(var i=0; i<result['dis_conn_id'].length; i++)
                        {
                            //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                            mouseEntreDisConnect(result['dis_conn_id'][i]);
                        }
                    }




                }
            });
            $("#en_serv-"+conn_id).html('Show Enabled Services');

        }



    }
    else if(disabled == 1 && enabled == 0)
    {
        $("#click_des_serv-"+conn_id).val(dis_s);
        if($("#click_des_serv-"+conn_id).val() % 2 == 0)
        {
            ShowEnabledServicesEachTypeRecent(conn_id, type);
            $("#des_serv-"+conn_id).html('Show All Services');
        }
        else
        {
            var form_conn = '';
            form_conn += '<span id=wat_rello>Waiting for List Services ... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -380px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
            $("#dis_serv_lists-"+conn_id).html(form_conn);
            $.ajax({
                url: 'webservice.php',
                type: 'GET',
                data: {
                    'show_all_services': '1',
                    'conn_id': conn_id,
                    'type': type
                },
                success: function (data) {
                    var table_services = '';
                    var result = $.parseJSON(data);

                    $("#dis_serv_lists-"+conn_id).html(result['text']);

                    if(result['dis_serv_id'] != null)
                    {
                        for(var i=0; i<result['dis_serv_id'].length; i++)
                        {
                            //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                            mouseEntreDisServer(result['dis_serv_id'][i]);
                        }

                    }

                    if(result['dis_conn_id'] != null)
                    {
                        for(var i=0; i<result['dis_conn_id'].length; i++)
                        {
                            //$("#serv-dis-link-"+result['dis_serv_id'][i]+":hoe")
                            mouseEntreDisConnect(result['dis_conn_id'][i]);
                        }
                    }





                }
            });
            $("#des_serv-"+conn_id).html('Show Recent Disabled Services by Each Type');


        }

    }


}

function mouseEntreDisServer(serv_id)
{
    $( "#tr-serv-dis-link-"+serv_id ).mouseover(function() {
        //alert(serv_id);
        $('#serv_span-'+serv_id).css('display','block');
        //$('#serv_span-'+serv_id).fadeIn( "slow" );
        //$('#serv_span-'+serv_id).fadeIn( "slow" );
        $('#serv_span-'+serv_id).html('Service number '+serv_id+' is suspended');

    }).mouseout(function() {
        //alert(serv_id);
        $('#serv_span-'+serv_id).css('display','none');
        //$('#serv_span-'+serv_id).fadeOut( "slow" );
        $('#serv_span-'+serv_id).html('');
    })
}


function mouseEntreDisConnect(serv_id)
{
    $( "#tr-serv-dis-link-"+serv_id ).mouseover(function() {
        //alert(serv_id);
        $('#serv_span-'+serv_id).css('display','block');
        //$('#serv_span-'+serv_id).fadeIn( "slow" );
        //$('#serv_span-'+serv_id).fadeIn( "slow" );
        $('#serv_span-'+serv_id).html('Service number '+serv_id+' is disconnected');

    }).mouseout(function() {
        //alert(serv_id);
        $('#serv_span-'+serv_id).css('display','none');
        //$('#serv_span-'+serv_id).fadeOut( "slow" );
        $('#serv_span-'+serv_id).html('');
    })
}

function mouseOverDisServer(serv_id)
{
    $('#serv_span-'+serv_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-'+serv_id).html('Service number '+serv_id+' is suspended');
}

function mouseOutDisServer(serv_id)
{
    $('#serv_span-'+serv_id).css('display','none');
    //$('#serv_span-'+serv_id).fadeOut( "slow" );
}




function mouseOverDisAddService(conn_id)
{
    $('#add-serv-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-serv-span-'+conn_id).html('The Connection number '+conn_id+' cannot add services because the property is disconnected');
}

function mouseOutDisAddService(conn_id)
{
    $('#add-serv-span-'+conn_id).css('display','none');
    //$('#serv_span-'+serv_id).fadeOut( "slow" );
}




function validate_form_search_serv_int()
{
    var prop_ref = $("#input_prop_ref").val();
    var prop_address = $("#input_prop_address").val();
    var con_type = $("#con_type_int").val();


    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: { 'search_validate_int_serv': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type } ,
        success: function (data) {
            var table_body = '';
            var linhas_dados = '';

            var result = $.parseJSON(data);

            if(result['list_conn_des_serv'] != null)
            {
                for(var i=0; i<result['list_conn_des_serv'].length; i++)
                {
                    table_body += '<tr>' +
                        '<td><a href=?props=1&propid='+result['list_conn_des_serv'][i].prop_id+'>'+result['list_conn_des_serv'][i].ref+'</a></td>' +
                        '<td>'+result['list_conn_des_serv'][i].address+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].type+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].serv_date_start+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].serv_date_start+'</td>' +

                        '</tr>';






                }

                // LInhas de Dados

                var num = result['num_rows'];

                $("#num_INT_only").html(num);

                var lastp = result['lastp'];

                var curpage = result['curpage'];

                if(num > 50)
                {
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset=0&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">|<</a>";
                    }
                    if(curpage>2)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset="+(curpage-3)*50 +"&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset="+(curpage-2)*50 +"&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-1)+"</a> ";
                    }

                    linhas_dados += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset="+(curpage)*50 +"&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset="+(curpage+1)*50+"&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=INTonly&offset="+(lastp-1)*50+"&con_type_int="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">>|</a> ";
                    }

                }

                linhas_dados += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#serv_int_only").html(table_body);

                $("#paging").html(linhas_dados);
            }
            else
            {
                $("#serv_int_only").html("No Result Matching Search");

                linhas_dados += "showing 0 to 0 of 0 results";

                $("#paging").html(linhas_dados);
            }
        },
        error: function () {
            alert("error");
        }
    });


    /*$.ajax({ method: "POST", url: "webservice.php", data: { 'search_validate_serv_disabled': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type }})
        .done(function( data )
        {




        });*/





}

function validate_form_search_serv_int_all()
{
    var prop_ref = $("#input_prop_ref").val();
    var prop_address = $("#input_prop_address").val();
    var con_type = $("#con_type_int_all").val();


    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: { 'search_validate_int_serv_all': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type } ,
        success: function (data) {
            var table_body = '';
            var linhas_dados = '';

            var result = $.parseJSON(data);

            if(result['list_conn_des_serv'] != null)
            {
                for(var i=0; i<result['list_conn_des_serv'].length; i++)
                {
                    table_body += '<tr>' +
                        '<td><a href=?props=1&propid='+result['list_conn_des_serv'][i].prop_id+'>'+result['list_conn_des_serv'][i].ref+'</a></td>' +
                        '<td>'+result['list_conn_des_serv'][i].address+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].type+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].value+'</td>' +
                        '</tr>';






                }

                // LInhas de Dados

                var num = result['num_rows'];

                $("#num_INT").html(num);

                var lastp = result['lastp'];

                var curpage = result['curpage'];

                if(num > 50)
                {
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset=0&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">|<</a>";
                    }
                    if(curpage>2)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset="+(curpage-3)*50 +"&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset="+(curpage-2)*50 +"&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-1)+"</a> ";
                    }

                    linhas_dados += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset="+(curpage)*50 +"&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset="+(curpage+1)*50+"&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=INT&offset="+(lastp-1)*50+"&con_type_int_all="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">>|</a> ";
                    }

                }

                linhas_dados += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#serv_int_all").html(table_body);

                $("#paging").html(linhas_dados);
            }
            else
            {
                $("#serv_int_all").html("No Result Matching Search");

                linhas_dados += "showing 0 to 0 of 0 results";

                $("#paging").html(linhas_dados);
            }
        },
        error: function () {
            alert("error");
        }
    });
}

function validate_form_search_serv_tv()
{
    var prop_ref = $("#input_prop_ref").val();
    var prop_address = $("#input_prop_address").val();
    var con_type = $("#con_type_tv").val();

    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: { 'search_validate_tv_serv_all': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type } ,
        success: function (data) {
            var table_body = '';
            var linhas_dados = '';

            var result = $.parseJSON(data);

            if(result['list_conn_des_serv'] != null)
            {
                for(var i=0; i<result['list_conn_des_serv'].length; i++)
                {
                    table_body += '<tr>' +
                        '<td><a href=?props=1&propid='+result['list_conn_des_serv'][i].prop_id+'>'+result['list_conn_des_serv'][i].ref+'</a></td>' +
                        '<td>'+result['list_conn_des_serv'][i].address+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].type+'</td>' +
                        '<td></td>' +
                        '</tr>';






                }

                // LInhas de Dados

                var num = result['num_rows'];

                $("#num_TV").html(num);

                var lastp = result['lastp'];

                var curpage = result['curpage'];

                if(num > 50)
                {
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset=0&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">|<</a>";
                    }
                    if(curpage>2)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset="+(curpage-3)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset="+(curpage-2)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-1)+"</a> ";
                    }

                    linhas_dados += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset="+(curpage)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset="+(curpage+1)*50+"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=TV&offset="+(lastp-1)*50+"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">>|</a> ";
                    }

                }

                linhas_dados += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#serv_tv").html(table_body);

                $("#paging").html(linhas_dados);
            }
            else
            {
                $("#serv_tv").html("No Result Matching Search");

                linhas_dados += "showing 0 to 0 of 0 results";

                $("#paging").html(linhas_dados);
            }
        },
        error: function () {
            alert("error");
        }
    });
}

function validate_form_search_serv_tv_only()
{
    var prop_ref = $("#input_prop_ref").val();
    var prop_address = $("#input_prop_address").val();
    var con_type = $("#con_type_tv_only").val();

    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: { 'search_validate_tv_serv': '1', 'prop_ref': prop_ref, 'prop_address': prop_address, 'con_type': con_type } ,
        success: function (data) {
            var table_body = '';
            var linhas_dados = '';

            var result = $.parseJSON(data);

            if(result['list_conn_des_serv'] != null)
            {
                for(var i=0; i<result['list_conn_des_serv'].length; i++)
                {
                    table_body += '<tr>' +
                        '<td><a href=?props=1&propid='+result['list_conn_des_serv'][i].prop_id+'>'+result['list_conn_des_serv'][i].ref+'</a></td>' +
                        '<td>'+result['list_conn_des_serv'][i].address+'</td>' +
                        '<td>'+result['list_conn_des_serv'][i].type+'</td>' +
                        '<td><a href=?servs=1&sid='+result['list_conn_des_serv'][i].serv_id+'>sid:'+result['list_conn_des_serv'][i].serv_id+'</a></td>' +
                        '</tr>';






                }

                // LInhas de Dados

                var num = result['num_rows'];

                $("#num_TV_ONLY").html(num);

                var lastp = result['lastp'];

                var curpage = result['curpage'];

                if(num > 50)
                {
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset=0&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">|<</a>";
                    }
                    if(curpage>2)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset="+(curpage-3)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset="+(curpage-2)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage-1)+"</a> ";
                    }

                    linhas_dados += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset="+(curpage)*50 +"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset="+(curpage+1)*50+"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        linhas_dados += "<a href=?servs=1&type=TVonly&offset="+(lastp-1)*50+"&con_type_tv="+con_type+"&input_prop_address="+prop_address+"&input_prop_ref="+prop_ref+">>|</a> ";
                    }

                }

                linhas_dados += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#serv_tv_only").html(table_body);

                $("#paging").html(linhas_dados);
            }
            else
            {
                $("#serv_tv_only").html("No Result Matching Search");

                linhas_dados += "showing 0 to 0 of 0 results";

                $("#paging").html(linhas_dados);
            }
        },
        error: function () {
            alert("error");
        }
    });


}

function clickDisService(conn_id)
{
    var modal = $("#modal-conn-"+conn_id);
    modal.toggleClass("show-modal");
}

function clickReaService(conn_id)
{
    var modal = $("#modal-conn-rea-"+conn_id);
    modal.toggleClass("show-modal");
}

function clickCloseReaServices(conn_id)
{
    var modal = $("#modal-conn-rea-"+conn_id);
    modal.removeClass('show-modal');
}

function clickCloseDisServices(conn_id)
{
    var modal = $("#modal-conn-"+conn_id);
    modal.removeClass('show-modal');
}

function disablePropConnection(conn_id)
{
    var modal = $("#modal-prop-conn-"+conn_id);
    modal.toggleClass("show-modal");
}

function clickClosePropConnection(conn_id)
{
    var modal = $("#modal-prop-conn-"+conn_id);
    modal.removeClass('show-modal');
}

function submitDisabledServices(conn_id, prop_id)
{
    $("#submit-dis-services-"+conn_id).html("");
    var date_submit_disabled = $("#date_end_services-"+conn_id).val();

    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'disabled_services_all_date': '1',
            'conn_id': conn_id,
            'date_submit_disabled': date_submit_disabled,
            'prop_id': prop_id
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#submit-dis-services-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}

function submitPropConnection(conn_id, prop_id)
{
    $("#submit-dis-conn-"+conn_id).html("");
    var date_end_conn = $("#date_end_conn-"+conn_id).val();

    var remove_equipment = $("#remove_equipment:checked").length;

    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'disabled_conn_all_date': '1',
            'conn_id': conn_id,
            'date_end_conn': date_end_conn,
            'remove_equipment': remove_equipment,
            'prop_id': prop_id
        },
        success: function (data) {

            var result = $.parseJSON(data);
            $("#submit-dis-conn-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}


function submitReactiveServices(conn_id, prop_id)
{
    $("#submit-rea-services-"+conn_id).html("");
    var date_end_conn = $("#date_end_services_rea-"+conn_id).val();


    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'reactivate_services_all_date': '1',
            'conn_id': conn_id,
            'date_submit_reactivated': date_end_conn,
            'prop_id': prop_id
        },
        success: function (data) {

            var result = $.parseJSON(data);
            $("#submit-rea-services-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}

function SubmitSugestion(prop_id)
{
    var notes = $("#notes").val();
    var count = 0;
    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'submit_log_entry': '1',
            'notes': notes,
            'prop_id': prop_id
        },
        success: function (data) {
            if(notes != "")
            {
                var result = $.parseJSON(data);
                count = result['count'];

                $("#submit_notes_succ").html('<font color=green>Log Entry was submitted sucessfully. Page will reload in <span id=sec_relo>'+count+'</span> seconds</font>');

                var sc_rel = $("#sec_relo").html();



                var n=sc_rel;
                var c=n;

                setInterval(function(){
                    c--;

                    if(c>0){
                        $('#sec_relo').html(c);
                    }
                    if(c==0){
                        $('#submit_notes_succ').html('waiting reloading....');
                        window.location.reload();
                    }
                },1000);
            }
            else
            {
                $("#submit_notes_succ").html('<font color=red>Insert Log entry text</font>');

            }

        },
        error: function (data) {
            alert("error");
        }
    });





}


// SERVIVES IDS - EDIT

// DISABLE

function mouseOverCancelServer(sid)
{
    $('#serv_span-delete-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-delete-'+sid).html('Service number '+sid+' cannot be canceled, because the connection is suspended');
}

function mouseOutCancelServer(sid)
{
    $('#serv_span-delete-'+sid).css('display','none');
}

// ENABLE

function mouseOverEnabledServer(sid)
{
    $('#serv_span-enabled-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-enabled-'+sid).html('Service number '+sid+' cannot be activated, because the connection is suspended');
}

function mouseOutEnabledServer(sid)
{
    $('#serv_span-enabled-'+sid).css('display','none');
}

function mouseOverDisabledServer(sid)
{
    $('#serv_span-disabled-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-disabled-'+sid).html('Service number '+sid+' cannot be deactivated, because the connection is suspended');
}

function mouseOutDisabledServer(sid)
{
    $('#serv_span-disabled-'+sid).css('display','none');
}


// EDIT SERVICES

function mouseOverEditServerSusp(sid)
{
    $('#serv_span-edit-serv-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-edit-serv-'+sid).html('Service number '+sid+' cannot be edited, because the connection is suspended');
}

function mouseOutEditServerSusp(sid)
{
    $('#serv_span-edit-serv-'+sid).css('display','none');
}

// STATUS, SYNC, RESET, REBOOT

function mouseOverStatusONT(conn_id)
{
    $('#add-status-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-status-span-'+conn_id).html('Connection number '+conn_id+' cannot show ´status´ because the connection didn´t have OLT and ONT');
}

function mouseOutStatusONT(conn_id)
{
    $('#add-status-span-'+conn_id).css('display','none');
}

function mouseOverRebootONT(conn_id)
{
    $('#add-reboot-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-reboot-span-'+conn_id).html('Connection number '+conn_id+' cannot ´reboot´ because the connection didn´t have OLT and ONT');
}

function mouseOutRebootONT(conn_id)
{
    $('#add-reboot-span-'+conn_id).css('display','none');
}

function mouseOverSyncONT(conn_id)
{
    $('#add-sync-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-sync-span-'+conn_id).html('Connection number '+conn_id+' cannot ´sync´ because the connection didn´t have OLT and ONT');
}

function mouseOutSyncONT(conn_id)
{
    $('#add-sync-span-'+conn_id).css('display','none');
}

function mouseOverResetONT(conn_id)
{
    $('#add-reset-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-reset-span-'+conn_id).html('Connection number '+conn_id+' cannot ´reset´ because the connection didn´t have OLT and ONT');
}

function mouseOutResetONT(conn_id)
{
    $('#add-reset-span-'+conn_id).css('display','none');
}


function mouseOverStatusCTMS(conn_id)
{
    $('#add-status-coax-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-status-coax-span-'+conn_id).html('Connection number '+conn_id+' cannot show ´status´ because the connection didn´t have CMTS and Equipment COAX');
}

function mouseOutStatusCTMS(conn_id)
{
    $('#add-status-coax-span-'+conn_id).css('display','none');
}


function mouseOverRebootsCTMS(conn_id)
{
    $('#add-reboot-coax-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-reboot-coax-span-'+conn_id).html('Connection number '+conn_id+' cannot ´reboot´ because the connection didn´t have CMTS and Equipment COAX');
}

function mouseOutRebootsCTMS(conn_id)
{
    $('#add-reboot-coax-span-'+conn_id).css('display','none');
}

// LEADS & PROPS

function searchprop(str)
{
    var paging_lead = "";
    $.ajax({ method: "GET", url: "webservice.php", data: {'searchprop': str, 'search_props': '1' }})

        .done(function( data )
        {

            var result = $.parseJSON(data);
            var string = "<table><tr> <th>ref</th><th>address</th><tr>";
            if(result['props'] != null)
            {
                $.each( result['props'], function( key, value )
                {
                    string += "<tr><td><a href=?props=1&propid="+ value['id'] +">  " + value['ref'] + "</a> </td><td>"
                        + value['address'] + "</td></tr>";
                });
                string += '</table>';

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];




                if(num > 50)
                {
                    if(curpage>1)
                    {
                        paging_lead += "<a href=?a href=?searchb="+str+"&props=1&offset=0>|<</a>";
                    }
                    if(curpage>2)
                    {
                        //a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-3)*30 +"
                        paging_lead += "<a href=?searchb="+str+"&props=1&offset="+(curpage-3)*50 +">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        // linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-2)*30 +">"+(curpage-1)+"</a> ";
                        paging_lead += "<a href=?searchb="+str+"&props=1&offset="+(curpage-2)*50 +">"+(curpage-1)+"</a> ";
                    }

                    paging_lead += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        paging_lead += "<a href=?searchb="+str+"&props=1&offset="+(curpage)*50 +">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage+1)*30 +">"+(curpage+2)+"</a> ";
                        paging_lead += "<a href=?searchb="+str+"&props=1&offset="+(curpage+1)*50 +">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(lastp-1)*30 +">"+(curpage+2)+"</a> "
                        paging_lead += "<a href=?searchb="+str+"&props=1&offset="+(lastp-1)*50 +">>|</a> ";
                    }

                }

                paging_lead += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#tablec").html(string);

                $("#paging").html(paging_lead);

            }
            else
            {

                string += "<tr><td>No Result Matching Search";
                $("#tablec").html(string);

                paging_lead += "showing 0 to 0 of 0 results";

                $("#paging").html(paging_lead);
            }

        });
}

function getStatus(str,status,owner)
{
    var s = '';
    s += '<option value ="">status</option>';
    $.ajax({
        url:'webservice.php',
        data:{'search_leads_status': '1' },
        type:'GET',
        success: function(data)
        {
            var result = $.parseJSON(data);
            if(result['props_status'] != null)
            {
                $.each( result['props_status'], function( key, value )
                {
                    s += '<option value="'+value['status']+'">'+value['status']+'</option>';
                });
                $("select[name=status]").html(s);

                $("select[name=status]").val(status);

            }
        },
        error:function(data)
        {

        }
    });



}

function getUsername(str,status,owner)
{
    var s = '';
    s += '<option value ="">created by</option>';
    $.ajax({
        url:'webservice.php',
        data:{'search_leads_users': '1' },
        type:'GET',
        success: function(data)
        {
            var result = $.parseJSON(data);
            if(result['props_username'] != null)
            {
                $.each( result['props_username'], function( key, value )
                {
                    s += '<option value="'+value['username']+'">'+value['username']+'</option>';
                });
                $("select[name=owner]").html(s);

                $("select[name=owner]").val(owner);

            }
        },
        error:function(data)
        {

        }
    });
}

// LEADS

function searchlead(str, status, owner)
{
    var paging_lead = "";
    $.ajax({ method: "GET", url: "webservice.php", data: {'searchlead': str, 'status': status, 'owner': owner, 'search_leads': '1' }})

        .done(function( data )
        {

            var result = $.parseJSON(data);
            var string = "<table><tr> <th>id</th><th>address</th><th>name</th>";
            string += "<th><form method=get><input type=hidden name=propleads value=1>";
            string += "<select name=status onchange=setStatus('"+str+"','"+owner+"') style=\"width: 100px\"></select></form></th>";
            string += "<th>date_in</th>";
            string += "<th>" +
                "<form method=get>" +
                "<input type=hidden name=propleads value=1>" +
                "<select name=owner onchange=setOwner('"+str+"','"+status+"') style=\"width: 100px\"></select></form></th>";
            var i=0;
            if(result['props'] != null)
            {
                $.each( result['props'], function( key, value )
                {
                    var strArray = value['notes'].split(" ");

                    string += "<tr><td><a href=?propleads=1&lead_id="+ value['id'] +">" +value['id'] +" </a></td><td width=400px> " + value['address'] + "</a> </td><td width=200px>"
                        + value['name'] + "</td><td align=center title=\"notes: "+strArray[0]+"\">"+value['status']+"</td><td>"+value['date_lead']+"</td><td>"+value['created_by']+"</td></tr>";
                });
                string += '</table>';

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];




                if(num > 50)
                {
                    if(curpage>1)
                    {
                        paging_lead += "<a href=?a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset=0>|<</a>";
                    }
                    if(curpage>2)
                    {
                        //a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-3)*30 +"
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-3)*50 +">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        // linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-2)*30 +">"+(curpage-1)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-2)*50 +">"+(curpage-1)+"</a> ";
                    }

                    paging_lead += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage)*50 +">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage+1)*30 +">"+(curpage+2)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage+1)*50 +">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(lastp-1)*30 +">"+(curpage+2)+"</a> "
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(lastp-1)*50 +">>|</a> ";
                    }

                }

                paging_lead += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#tablec").html(string);

                $("#paging").html(paging_lead);

            }
            else
            {

                string += "<tr><td>No Result Matching Search";
                $("#tablec").html(string);

                paging_lead += "showing 0 to 0 of 0 results";

                $("#paging").html(paging_lead);
            }





            getStatus(str,status,owner);
            getUsername(str,status,owner);

            $("select").select2();


        });
}

function setStatus(str, owner)
{
    var status = $("select[name=status]").val();
    var paging_lead = "";
    $.ajax({ method: "GET", url: "webservice.php", data: {'searchlead': str, 'status': status, 'owner':owner, 'search_status_owner_cond2': '1' }})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var string = "<table><tr> <th>id</th><th>address</th><th>name</th>";
            string += "<th><form method=get><input type=hidden name=propleads value=1>";
            string += "<select name=status onchange=setStatus('"+str+"','"+owner+"') style=\"width: 100px\"></select></form></th>";
            string += "<th>date_in</th>";
            string += "<th>" +
                "<form method=get>" +
                "<input type=hidden name=propleads value=1>" +
                "<select name=owner onchange=setOwner('"+str+"','"+status+"') style=\"width: 100px\"></select></form></th>";
            //var total= result['address'];
            var i=0;
            if(result['props'] != null)
            {
                $.each( result['props'], function( key, value )
                {
                    var strArray = value['notes'].split(" ");

                    string += "<tr><td><a href=?propleads=1&lead_id="+ value['id'] +">" +value['id'] +" </a></td><td width=400px> " + value['address'] + "</a> </td><td width=200px>"
                        + value['name'] + "</td><td align=center title=\"notes: "+strArray[0]+"\">"+value['status']+"</td><td>"+value['date_lead']+"</td><td>"+value['created_by']+"</td></tr>";
                });
                string += '</table>';

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];




                if(num > 50)
                {
                    if(curpage>1)
                    {
                        paging_lead += "<a href=?a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset=0>|<</a>";
                    }
                    if(curpage>2)
                    {
                        //a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-3)*30 +"
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-3)*50 +">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        // linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-2)*30 +">"+(curpage-1)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-2)*50 +">"+(curpage-1)+"</a> ";
                    }

                    paging_lead += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage)*50 +">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage+1)*30 +">"+(curpage+2)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage+1)*50 +">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(lastp-1)*30 +">"+(curpage+2)+"</a> "
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(lastp-1)*50 +">>|</a> ";
                    }

                }

                paging_lead += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#tablec").html(string);

                $("#paging").html(paging_lead);

            }
            else
            {

                string += "<tr><td>No Result Matching Search";
                $("#tablec").html(string);

                paging_lead += "showing 0 to 0 of 0 results";

                $("#paging").html(paging_lead);
            }

            getStatus(str,status,owner);
            getUsername(str,status,owner);

            $("select").select2();
        });




}

function setOwner(str, status)
{
    var owner = $("select[name=owner]").val();
    var paging_lead = "";
    $.ajax({ method: "GET", url: "webservice.php", data: {'searchlead': str, 'status': status, 'owner':owner, 'search_status_owner_cond1': '1' }})
        .done(function( data )
        {
            var result = $.parseJSON(data);
            var string = "<table><tr> <th>id</th><th>address</th><th>name</th>";
            string += "<th><form method=get><input type=hidden name=propleads value=1>";
            string += "<select name=status onchange=setStatus('"+str+"','"+owner+"') style=\"width: 100px\"></select></form></th>";
            string += "<th>date_in</th>";
            string += "<th>" +
                "<form method=get>" +
                "<input type=hidden name=propleads value=1>" +
                "<select name=owner onchange=setOwner('"+str+"','"+status+"') style=\"width: 100px\"></select></form></th>";
            var i=0;
            if(result['props'] != null)
            {
                $.each( result['props'], function( key, value )
                {
                    var strArray = value['notes'].split(" ");

                    string += "<tr><td><a href=?propleads=1&lead_id="+ value['id'] +">" +value['id'] +" </a></td><td width=400px> " + value['address'] + "</a> </td><td width=200px>"
                        + value['name'] + "</td><td align=center title=\"notes: "+strArray[0]+"\">"+value['status']+"</td><td>"+value['date_lead']+"</td><td>"+value['created_by']+"</td></tr>";
                });
                string += '</table>';

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];




                if(num > 50)
                {
                    if(curpage>1)
                    {
                        paging_lead += "<a href=?a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset=0>|<</a>";
                    }
                    if(curpage>2)
                    {
                        //a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-3)*30 +"
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-3)*50 +">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        // linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-2)*30 +">"+(curpage-1)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage-2)*50 +">"+(curpage-1)+"</a> ";
                    }

                    paging_lead += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage)*50 +">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage+1)*30 +">"+(curpage+2)+"</a> ";
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(curpage+1)*50 +">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(lastp-1)*30 +">"+(curpage+2)+"</a> "
                        paging_lead += "<a href=?propleads=1&searchb="+str+"&status="+status+"&owner="+owner+"&propleadslist=1&offset="+(lastp-1)*50 +">>|</a> ";
                    }

                }

                paging_lead += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#tablec").html(string);

                $("#paging").html(paging_lead);

            }
            else
            {

                string += "<tr><td>No Result Matching Search";
                $("#tablec").html(string);

                paging_lead += "showing 0 to 0 of 0 results";

                $("#paging").html(paging_lead);
            }

            getStatus(str,status,owner);
            getUsername(str,status,owner);

            $("select").select2();
        });
}

// CUSTOMERS - SEARCH

function searchcust(cust)
{

    var paging_lead = "";
    $.ajax({ method: "GET", url: "webservice.php", data: {'searchcusts': cust, 'search_custs': '1' }})

        .done(function( data )
        {

            var result = $.parseJSON(data);
            var string = "<table><tr> <th>id</th><th>name</th><th>fiscal</th><th>email</th><th>phone</th>";
            if(result['custs'] != null)
            {
                $.each( result['custs'], function( key, value )
                {
                    string += "<tr><td width=60px><a href=?custs=1&cust_id="+ value['id'] +">  " + value['id'] + "</a> </td>";
                    string += "<td width=200px>"+value['name']+"</td>";
                    string += "<td>"+value['fiscal_nr']+"</td>";
                    string += "<td>"+value['email']+"</td>";
                    string += "<td>"+value['telef']+"</td></tr>";
                });
                string += '</table>';

                var num = result['num_rows'];

                var lastp = result['lastp'];

                var curpage = result['curpage'];




                if(num > 50)
                {
                    if(curpage>1)
                    {
                        paging_lead += "<a href=?a href=?searchb="+cust+"&custs=1&offset=0>|<</a>";
                    }
                    if(curpage>2)
                    {
                        //a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-3)*30 +"
                        paging_lead += "<a href=?searchb="+cust+"&custs=1&offset="+(curpage-3)*50 +">"+(curpage-2)+"</a> ";
                    }
                    if(curpage>1)
                    {
                        // linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage-2)*30 +">"+(curpage-1)+"</a> ";
                        paging_lead += "<a href=?searchb="+cust+"&custs=1&offset="+(curpage-2)*50 +">"+(curpage-1)+"</a> ";
                    }

                    paging_lead += "<b>"+curpage+"</b>";

                    if(curpage<lastp)
                    {
                        paging_lead += "<a href=?searchb="+cust+"&custs=1&offset="+(curpage)*50 +">"+(curpage+1)+"</a> ";
                    }
                    if(curpage<lastp-1)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(curpage+1)*30 +">"+(curpage+2)+"</a> ";
                        paging_lead += "<a href=?searchb="+cust+"&custs=1&offset="+(curpage+1)*50 +">"+(curpage+2)+"</a> ";
                    }

                    if(curpage<lastp)
                    {
                        //linhas_dados += "<a href=?propleads=1&searchb="+str+"&propleadslist=1&offset="+(lastp-1)*30 +">"+(curpage+2)+"</a> "
                        paging_lead += "<a href=?searchb="+cust+"&custs=1&offset="+(lastp-1)*50 +">>|</a> ";
                    }

                }

                paging_lead += "showing "+ (curpage-1)*50 +" to "+curpage*50+" of "+num+" results";

                $("#tablec").html(string);

                $("#paging").html(paging_lead);

            }
            else
            {

                string += "<tr><td>No Result Matching Search";
                $("#tablec").html(string);

                paging_lead += "showing 0 to 0 of 0 results";

                $("#paging").html(paging_lead);
            }

        });

}

function updatefregep(conc, freg)
{
    console.log(conc, freg);
    $.ajax({ method: "GET", url: "webservice.php", data: {'getfreg': 1 , 'conc': conc}})

        .done(function( data )
        {
            var result = $.parseJSON(data);
            var frega=document.getElementById("freg");
            frega.innerHTML = "";
            var i=0;
            var fregb;
            $.each( result, function( key, value )
            {
                fregb = new Option(value['freguesia'], value['id']);
                frega.options.add(fregb);
            });

            if(freg != "")
            {
                $("#freg").val(freg).trigger("change");
            }
           
        });
}

function updateconcelhosep(country)
{
    $.ajax({ method: "GET", url: "webservice.php", data: {'getconcelho': 1 , 'country': country}})

        .done(function( data )
        {
            var result = $.parseJSON(data);


            //CONCELHOS
            var concelho=document.getElementById("concelho");
            concelho.innerHTML = "";
            var concelho_b;
            //FREGUESIAS
            var frega=document.getElementById("freg");
            frega.innerHTML = "";
            var frega_b;

            $.each( result['concelhos'], function( key, value ) {
                var vl = value['distrito'] + ' - ' + value['concelho'];
                concelho_b = new Option(vl, value['id']);
                concelho.options.add(concelho_b);

            });

            $.each( result['freguesia'], function( key, value ) {
                var vl = value['freguesia'];
                freg_b = new Option(vl, value['concelho']);
                frega.options.add(freg_b);

            });
        });
}

function status1()
{
    document.getElementById('popup1').style.display = 'block';
}
function status1o()
{
    document.getElementById('popup1').style.display = 'none';
}


function gpslink(opt) {

    var url;
    var coord;
    var address;
    var frega;
    frega=document.getElementById("freg");
    url="gps.php?coord=";
    coord=document.getElementById("coord").value.replace(/ /g,'');
    address=document.getElementById("address").value.replace(/ /g, "+");
    address += "," + frega.options[frega.selectedIndex].text;
//	alert("updated gps" + url + coord + "&address=" + address);
    window.open(url + coord + "&address=" + address + "&mode=" + opt, 'Pagina', 'STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, TOP=10, LEFT=10, WIDTH=800px, HEIGHT=600px');
}

function mouseOverDisService(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-dis-services-'+conn_id).html('Suspended Services on connection <b>'+conn_id+'</b>');
}

function mouseOutDisService(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','none');
}


function mouseOverPropConn(conn_id)
{
    $('#title-dis-conn-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-dis-conn-'+conn_id).html('Disconnect the connection number <b>'+conn_id+'</b>');
}

function mouseOutPropConn(conn_id)
{
    $('#title-dis-conn-'+conn_id).css('display','none');
}

function mouseOverReaService(conn_id)
{
    $('#title-rea-services-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-rea-services-'+conn_id).html('Reactive Services on connection <b>'+conn_id+'</b>');
}

function mouseOutReaService(conn_id)
{
    $('#title-rea-services-'+conn_id).css('display','none');
}



function mouseOverDivSuspServices(conn_id, date)
{
    $('#title-dis-services-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-dis-services-'+conn_id).html('The Services on connection <b>'+conn_id+'</b> are going to be disabled on date <b>'+date+'</b>');
}

function mouseOutDivSuspServices(conn_id,date)
{
    $('#title-dis-services-'+conn_id).css('display','none');
}

function mouseOverDivDisConn(conn_id, date)
{
    $('#title-dis-conn-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-dis-conn-'+conn_id).html('The Connection number <b>'+conn_id+'</b> are going to be disconnected on date <b>'+date+'</b>');
}

function mouseOutDivDisConn(conn_id,date)
{
    $('#title-dis-conn-'+conn_id).css('display','none');
}

function mouseOverDivReaServ(conn_id, date)
{
    $('#title-rea-services-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-rea-services-'+conn_id).html('The Services on connection number <b>'+conn_id+'</b> are going to be reactivated on date <b>'+date+'</b>');
}

function mouseOutDivReaServ(conn_id,date)
{
    $('#title-rea-services-'+conn_id).css('display','none');
}


function mouseOverDisConn(serv_id)
{
    $('#serv_span-'+serv_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-'+serv_id).html('Service number '+serv_id+' is disconnected');
}

function mouseOutDisConn(serv_id)
{
    $('#serv_span-'+serv_id).css('display','none');
    //$('#serv_span-'+serv_id).fadeOut( "slow" );
}


function mouseOverEditConnDisconnected(sid)
{
    $('#serv_span-edit-serv-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-edit-serv-'+sid).html('Service number '+sid+' cannot be edited, because the connection is disconnected');
}

function mouseOutEditConnDisconnected(sid)
{
    $('#serv_span-edit-serv-'+sid).css('display','none');
}


function mouseOverDisabledConnection(sid)
{
    $('#serv_span-disabled-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-disabled-'+sid).html('Service number '+sid+' cannot be deactivated, because the connection is disconnected');
}

function mouseOutDisabledConnection(sid)
{
    $('#serv_span-disabled-'+sid).css('display','none');
}


function mouseOverEnabledConnection(sid)
{
    $('#serv_span-enabled-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-enabled-'+sid).html('Service number '+sid+' cannot be activated, because the connection is disconnected');
}

function mouseOutEnabledConnection(sid)
{
    $('#serv_span-enabled-'+sid).css('display','none');
}



function mouseOverCancelConnection(sid)
{
    $('#serv_span-delete-'+sid).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#serv_span-delete-'+sid).html('Service number '+sid+' cannot be canceled, because the connection is disconnected');
}

function mouseOutCancelConnection(sid)
{
    $('#serv_span-delete-'+sid).css('display','none');
}


function mouseOverSuspAddService(conn_id)
{
    $('#add-serv-span-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#add-serv-span-'+conn_id).html('The Connection number '+conn_id+' cannot add services because the property is suspended');
}

function mouseOutSuspAddService(conn_id)
{
    $('#add-serv-span-'+conn_id).css('display','none');
    //$('#serv_span-'+serv_id).fadeOut( "slow" );
}


var cont_all_connections = 0;
function ShowAllConnections(prop_id)
{
    cont_all_connections++;
    if(cont_all_connections % 2 == 0)
    {
        $("#all_conn").val(0);
        $("#conn_enab").val(1);
        showConnectionEnabled(prop_id);

        $("#prop-conn-id-"+prop_id).html('Show All Connections');
    }
    else
    {
        $("#all_conn").val(1);
        $("#conn_enab").val(0);

        showConnections(prop_id);

        $("#prop-conn-id-"+prop_id).html('Show Connection(s) Enabled');
    }

}

function showConnections(prop_id)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Connections... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -102px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#lists_connections").html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'show_all_connections': '1',
            'prop_id': prop_id
        },
        success: function (data) {
            var table_services = '';
            var result = $.parseJSON(data);

            $("#lists_connections").html(result['text']);






        }
    });

}

function showConnectionEnabled(prop_id)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Connections Enabled... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -294px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#lists_connections").html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'show_connections_enabled': '1',
            'prop_id': prop_id
        },
        success: function (data) {
            var table_services = '';
            var result = $.parseJSON(data);

            $("#lists_connections").html(result['text']);






        }
    });
}



function mouseOverDivSuspNotServices(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','block');
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    //$('#serv_span-'+serv_id).fadeIn( "slow" );
    $('#title-dis-services-'+conn_id).html('The connection number <b>'+conn_id+'</b> does not exist services');
}

function mouseOutDivSuspNotServices(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','none');
}

function submitEachServiceAttr(serv_attr, type, sid, propid)
{
    var text = "This Services attribute "+serv_attr+" on type service "+type+" is going to be removed.\n Do you want to proceed?";
    if (confirm(text) == true) {
        $.ajax({
            url: 'webservice.php',
            type: 'POST',
            data: {
                'submit_each_services_attr': '1',
                'prop_id': propid,
                'sid': sid,
                'type': type,
                'serv_attr': serv_attr
            },
            success: function (data) {
                var result = $.parseJSON(data);
                //$("#remove_services_attr").html('aaa');
                //setTimeout(function() {window.location.reload();},2000);
                window.location.href = 'index.php?servs=1&sid='+sid+"&warn_text="+result['msg'];

            }
        });

    }


}


































function popup2(cmd,ctype,olt,ont,conn_id) {
    $("#info_connection_"+conn_id).css('position', 'absolute');
    $("#info_connection_"+conn_id).html("<div id=popu_warn style=\"position: relative\"><div id=popup2 style=\"background-color:white; z-index:2; display:none; position:absolute; width:400px; border:1px solid #ff0000; top: 0!important;\">"+
    "<div id=popupresult align=center>" +
    "<img width=160px src=\"img/wait.gif\">" +
    "</div>" +
    "<div style=\"position:absolute; top:10px; right:10px;\"> <img src=\"img/cancel.png\" onclick=\"closepopup2("+conn_id+")\"></div>" +
    "</div>");

   var e = document.getElementById("popup2");
   e.style.display = 'block';
   e.style.top=window.scrollY;

    

   document.getElementById("popupresult").innerHTML ='<img width=160px src="img/wait.gif">';
if(cmd=="status")
{	   
   
console.log(cmd,ctype,olt,ont,conn_id);




   if(ctype=="GPON")
{
 $.ajax({ method: "GET",type : "GET", url: "webservice.php", data: {'status_ont': 1 , 'olt': olt, 'ont': ont},error: function(e){
        console.log(e.message);
    }}
 )

.done(function( data ) 
{ 
    console.log(data);
  var result = $.parseJSON(data); 
  var string = "<br><br><table><tr> <th>Item</th><th>value</th><tr>";
 string += "<tr><td>date:<td>" + result['date'] +
     " <tr><td>model:<td>" + result['model'] +
     " <tr><td>fsan:<td>" + result['fsan'] +
    " <tr><td>status:<td>" + result['status'] +
    " <tr><td>config:<td>" + result['config'] +
    " <tr><td>download:<td>" + result['download'] +
    " <tr><td>rx:<td>" + result['rx'] +
    " <tr><td>tx:<td>" + result['tx'] +
    " <tr><td>rf:<td>" + result['rf'] +
    " <tr><td>uptime:<td>" + result['uptime'] +
    " <tr><td>sw:<td>" + result['sw'] +
    " <tr><td>ip_mng:<td>" + result['ip_mng'] +
    " <tr><td>ip_voip:<td>" + result['ip_voip'] +
    " <tr><td>ip_wan:<td>" + result['ip_wan'] +
    " <tr><td>msg:<td>" + result['msg'] +
    "</td></tr>";
string += '</table>'; 
document.getElementById("popupresult").innerHTML = string; 
});  
}
else if(ctype=="COAX")
{

    $.ajax({ method: "GET",type : "GET", url: "webservice.php", data: {'status_modem': 1 , 'cmts': olt, 'modem': ont},error: function(e){
        console.log(e.message);
    }}
 )

.done(function( data ) 
{ 
  var result = $.parseJSON(data); 
  var string = "<br><br><table><tr> <th>Item</th><th>value</th><tr>";
 string += "<tr><td>date:<td>" + result['date'] +
     " <tr><td>model:<td>" + result['model'] +
     " <tr><td>bootfile:<td>" + result['bootfile'] +
    " <tr><td>uptime:<td>" + result['uptime'] +
    " <tr><td>phone:<td>" + result['phone'] +
    " <tr><td>ssid:<td>" + result['ssid'] +
    " <tr><td>pass:<td>" + result['pass'] +
    " <tr><td>pubip:<td>" + result['pubip'] +
    " <tr><td>ds_freq:<td>" + result['ds_freq'] +
    " <tr><td>ds_lev:<td>" + result['ds_lev'] +			
    " <tr><td>ds_snr:<td>" + result['ds_snr'] +
    
    " <tr><td>us_freq:<td>" + result['us_freq'] +
    " <tr><td>us_lev:<td>" + result['us_lev'] +

    " <tr><td>msg:<td>" + result['msg'] +
    
    
    "</td></tr>";
string += '</table>'; 
document.getElementById("popupresult").innerHTML = string; 
}); 
}







}
else if(cmd=="reboot")
{



 if(ctype=="GPON")
{

$.ajax({ method: "GET",type : "GET", url: "webservice.php", data: {'reboot_ont': 1 , 'olt': olt, 'ont': ont},error: function(e){
        console.log(e.message);
    }}
 )

.done(function( data ) 
{ 
  var result = $.parseJSON(data); 
  var string = "<br><br><table><tr> <th>Item</th><th>value</th><tr>";
 string += "<tr><td>date:<td>" + result['date'] +
    " <tr><td>msg:<td>" + result['msg'] +
    "</td></tr>";
string += '</table>'; 
document.getElementById("popupresult").innerHTML = string; 




});

}
else if(ctype=="COAX")
{

    $.ajax({ method: "GET",type : "GET", url: "webservice.php", data: {'reboot_modem': 1 , 'cmts': olt, 'modem': ont},error: function(e){
        console.log(e.message);
    }}
 )

.done(function( data ) 
{ 
  var result = $.parseJSON(data); 
  var string = "<br><br><table><tr> <th>Item</th><th>value</th><tr>";
 string += "<tr><td>date:<td>" + result['date'] +
    " <tr><td>msg:<td>" + result['msg'] +
    "</td></tr>";
string += '</table>'; 
document.getElementById("popupresult").innerHTML = string; 

}); 

}

}
}

function closepopup2(conn_id) {

   var e = document.getElementById("popup2");
   e.style.display = 'none';

   $("#info_connection_"+conn_id).html("");

}



// SUSPENDED, DISCONNECTED, REACTIVE WITH NO SERVICES FUNCTIONS - DISABLED BUTTON SUSPENDED

function spanMouseOverNoService(conn_id)
{
    $('#title-sus-services-'+conn_id).css('display','block');
    $('#title-sus-services-'+conn_id).html('The connection number <b>'+conn_id+'</b> does not exist services');

    $('#title-dis-services-'+conn_id).css('display','block');
    $('#title-dis-services-'+conn_id).html('The connection number <b>'+conn_id+'</b> does not exist services');
}


function spanMouseOutNoService(conn_id)
{
    $('#title-sus-services-'+conn_id).css('display','none');
    $('#title-dis-services-'+conn_id).css('display','none');
}

// SUSPENDED WITH SERVICES FUNCTIONS - BUTTON ENABLED

function mouseOverSuspendedServices(conn_id)
{
    $('#title-sus-services-'+conn_id).css('display','block');
    $('#title-sus-services-'+conn_id).html('Suspend Services on connection <b>'+conn_id+'</b>');
}

function mouseOutSuspendedServices(conn_id)
{
    $('#title-sus-services-'+conn_id).css('display','none');
}

// REACTIVE WITH SERVICES FUNCTIONS - BUTTON ENABLED

function mouseOverReactiveServices(conn_id)
{
    $('#title-rea-services-'+conn_id).css('display','block');
    $('#title-rea-services-'+conn_id).html('Reactive Services on connection <b>'+conn_id+'</b>');
}

function mouseOutReactiveServices(conn_id)
{
    $('#title-rea-services-'+conn_id).css('display','none');
}


// DISCONNECTED WITH NO SERVICES FUNCTIONS - DISABLED BUTTON DISCONNECTED


// DISCONNECTED WITH SERVICES FUNCTIONS - BUTTON ENABLED

function mouseOverDisconnectedServices(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','block');
    $('#title-dis-services-'+conn_id).html('Disconnect Services on connection <b>'+conn_id+'</b>');
}

function mouseOutDisconnectedServices(conn_id)
{
    $('#title-dis-services-'+conn_id).css('display','none');
}







function clickSuspendedServicesModal(conn_id, prop_id, date_now)
{
    var modal = $(".act_button_susp_"+conn_id);
    
    var html = '';
    html += "<div class=\"modal-content\">"+
        "<span class=close-button onclick=\"closeSuspendedServicesModal("+conn_id+");\">×</span>" +
        "<h1>Suspend Serviçes "+conn_id+"</h1>" +
        "<br>"+
        "<div style=\"text-align: center; \">" +
            "<button type=button onclick=\"submitSuspendedServicesModal("+conn_id+", "+prop_id+",'"+date_now+"');\" >Submit Suspend Services</button>" +
        "</div>" +
        "<span id=submit-sus-services-"+conn_id+"></span>" +
    "</div>";
    $(".act_button_susp_"+conn_id).html(html);

    modal.toggleClass('show-modal');
}

function clickDisconnectedServicesModal(conn_id, prop_id, date_now)
{
    var modal = $(".act_button_disc_"+conn_id);
    var html = "";
    html += "<div class=\"modal-content\">"+
        "<span class=close-button onclick=\"closeDisconnectedServicesModal("+conn_id+");\">×</span>" +
        "<h1>Disconnect Serviçes "+conn_id+"</h1>" +
        "<br>"+
        "<input type=checkbox name=remove_equipment id=remove_equipment> Remove Equipment<br>" +
        "<br>"+
        "<div style=\"text-align: center; \">" +
            "<button type=button onclick=\"submitDisconnectedServicesModal("+conn_id+", "+prop_id+",'"+date_now+"');\" >Submit Disconnect Services</button>" +
        "</div>" +
        "<span id=submit-dis-services-"+conn_id+"></span>" +
    "</div>";
    $(".act_button_disc_"+conn_id).html(html);

    modal.toggleClass('show-modal');
}

function clickReactiveServicesModal(conn_id, prop_id, date_now)
{
    var modal = $(".act_button_rea_"+conn_id);
    var html = "";
    html += "<div class=\"modal-content\">"+
        "<span class=close-button onclick=\"closeReactiveServicesModal("+conn_id+");\">×</span>" +
        "<h1>Reactivate Serviçes "+conn_id+"</h1>" +
        "<br>"+
        "<div style=\"text-align: center; \">" +
            "<button type=button onclick=\"submitReactiveServicesModal("+conn_id+", "+prop_id+",'"+date_now+"');\" >Submit Reactivate Services</button>" +
        "</div>" +
        "<span id=submit-rea-services-"+conn_id+"></span>" +
    "</div>";
    $(".act_button_rea_"+conn_id).html(html);

    modal.toggleClass('show-modal');
}


function closeSuspendedServicesModal(conn_id)
{
    var modal = $(".act_button_susp_"+conn_id);
    modal.html('');
    modal.removeClass('show-modal');
}


function closeDisconnectedServicesModal(conn_id)
{
    var modal = $(".act_button_disc_"+conn_id);
    modal.html('');
    modal.removeClass('show-modal');
}

function closeReactiveServicesModal(conn_id)
{
    var modal = $(".act_button_rea_"+conn_id);
    modal.html('');
    modal.removeClass('show-modal');
}


function submitSuspendedServicesModal(conn_id, prop_id, date_now)
{
    $("#submit-sus-services-"+conn_id).html("");
    var date_submit_disabled = $("#date_end_services-"+conn_id).val();

    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'services_suspended_all_date': '1',
            'conn_id': conn_id,
            'date_submit_disabled': date_now,
            'prop_id': prop_id
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#submit-sus-services-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}

function submitDisconnectedServicesModal(conn_id, prop_id, date_now)
{
    var checked = $("#remove_equipment:checked").length;
    $("#submit-dis-services-"+conn_id).html('');

    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'services_disconnected_all_date': '1',
            'conn_id': conn_id,
            'date_submit_disconncted': date_now,
            'prop_id': prop_id,
            'remove_equipament': checked
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#submit-dis-services-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}

function submitReactiveServicesModal(conn_id, prop_id, date_now)
{
    $("#submit-rea-services-"+conn_id).html("");
    var date_submit_disabled = $("#date_end_services-"+conn_id).val();

    $.ajax({
        url: 'webservice.php',
        type: 'POST',
        data: {
            'services_reactive_all_date': '1',
            'conn_id': conn_id,
            'date_submit_reactive': date_now,
            'prop_id': prop_id
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#submit-rea-services-"+conn_id).html(result['msg']);
            setTimeout(function() {window.location.reload();},2000);
        },
        error: function (data) {
            alert("error");
        }
    });
}



function mouseOverSuspendedServicesId(serv_id)
{
    $('#serv_span-susp-'+serv_id).css('display','block');
    $('#serv_span-susp-'+serv_id).html('Service number '+serv_id+' is suspended');
}

function mouseOutSuspendedServicesId(serv_id)
{
    $('#serv_span-susp-'+serv_id).css('display','none');
}


function mouseOverDisconnectedServicesId(serv_id)
{
    $('#serv_span-dis-'+serv_id).css('display','block');
    $('#serv_span-dis-'+serv_id).html('Service number '+serv_id+' is disconnected');
}

function mouseOuDisconnectedServicesId(serv_id)
{
    $('#serv_span-dis-'+serv_id).css('display','none');
}










// LIST ALL CONNECTIONS & SERVICES

var all_connections_cont = 0;
function ShowAllConnectionsProps(prop_id)
{
    cont_all_connections++;
    if(cont_all_connections % 2 == 0)
    {
        showConnectionEnabledProps(prop_id);
        $("#prop-conn-id-"+prop_id).html('Show All Connections');
    }
    else
    {
        showConnectionsProps(prop_id);
        $("#prop-conn-id-"+prop_id).html('Show Connection(s) Enabled');
    }
}


// LIST CONNECTIONS ENABLED & DISABLED - DATE_END

function showConnectionEnabledProps(prop_id)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Connections Enabled... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -294px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#list_connetions-"+prop_id).html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'show_connections_enabled_prop': '1',
            'prop_id': prop_id
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#list_connetions-"+prop_id).html(result['text']);
        }
    });
}




function showConnectionsProps(prop_id)
{
    var form_conn = '';
    form_conn += '<span id=wat_rello>Waiting for List Connections... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -102px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
    $("#list_connetions-"+prop_id).html(form_conn);
    $.ajax({
        url: 'webservice.php',
        type: 'GET',
        data: {
            'show_all_connections_prop': '1',
            'prop_id': prop_id
        },
        success: function (data) {
            var result = $.parseJSON(data);
            $("#list_connetions-"+prop_id).html(result['text']);
        }
    });
}

// LIST SERVICES ENABLED & DISABLED - DATE_END

    var en_s = 0;
    var dis_s = 0;
    function ShowAllServicesConn(conn_id, type, serv_des, serv_en)
    {
        if(serv_des == 1)
        {
            dis_s = parseInt($("#click_des_serv-"+conn_id).val()) + 1
        }
        else if(serv_en == 1)
        {
            en_s = parseInt($("#click_en_serv-"+conn_id).val()) + 1;
        }
        console.log(en_s, dis_s);
        if(serv_en == 1)
        {
            $("#click_en_serv-"+conn_id).val(en_s);
            if($("#click_en_serv-"+conn_id).val() % 2 == 0)
            {
                // LISTAR OS SERVICOS HABILITADOS
                ShowEnabledServicesByConn(conn_id, type);
                $("#dis_en_serv-"+conn_id).html('Show All Services');
            }
            else
            {   
                // LISTAR TODOS OS SERVIÇOS
                showAllServicesConnByIdEnabl(conn_id, type);
                $("#dis_en_serv-"+conn_id).html('Show Enabled Services');
            }
        }
        else if(serv_des == 1)
        {
            $("#click_des_serv-"+conn_id).val(dis_s);
            if($("#click_des_serv-"+conn_id).val() % 2 == 0)
            {
                // LISTAR OS SERVICOS DESCONNECTADOS PARA CADA SERVIÇO PELO ULTIMA DATA
                showAllServicesDisabledEachTypeConn(conn_id, type);
                $("#dis_en_serv-"+conn_id).html('Show Recent Disabled Services by Each Type');
            }
            else
            {   
                // LISTAR OS SERVICOS HABILITADOS
                ShowDisabledServicesByConn(conn_id, type);
                $("#dis_en_serv-"+conn_id).html('Show All Services Disabled');
            }
        }
       


        // LISTAR OS SERVICOS DESCONNECTADOS PARA CADA SERVIÇO PELO ULTIMO
        // LISTAR OS SERVICOS HABILITADOS
    }

    // LISTAR TODOS OS SERVICOS DA CONNECTION
    function ShowEnabledServicesByConn(conn_id, type)
    {
        var form_conn = '';
        form_conn += '<span id=wat_rello>Waiting for List Enabled Services... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -500px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
        $("#servs_lists-"+conn_id).html(form_conn);
        $.ajax({
            url: 'webservice.php',
            type: 'GET',
            data: {
                'enabled_serv_conn_all_services': '1',
                'conn_id': conn_id,
                'type': type
            },
            success: function (data) {
                var result = $.parseJSON(data);
                $("#servs_lists-"+conn_id).html(result['text']);


            }
        });
    }

    // LISTAR OS SERVICOS HABILITADOS DA CONNECTION
    function  showAllServicesConnByIdEnabl(conn_id, type)
    {
        var form_conn = '';
        form_conn += '<span id=wat_rello>Waiting for List All Services ... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -400px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
        $("#servs_lists-"+conn_id).html(form_conn);
            $.ajax({
                url: 'webservice.php',
                type: 'GET',
                data: {
                    'show_all_services_enable_serv': '1',
                    'conn_id': conn_id,
                    'type': type
                },
                success: function (data) {
                    var result = $.parseJSON(data);
                    $("#servs_lists-"+conn_id).html(result['text']);
                }
            });
    }



    function showAllServicesDisabledEachTypeConn(conn_id, type)
    {
        var form_conn = '';
        form_conn += '<span id=wat_rello>Waiting for List Enabled Services... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -500px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
        $("#servs_lists-"+conn_id).html(form_conn);
        $.ajax({
            url: 'webservice.php',
            type: 'GET',
            data: {
                'disabled_service_each_type_conn': '1',
                'conn_id': conn_id,
                'type': type
            },
            success: function (data) {
                var result = $.parseJSON(data);
                $("#servs_lists-"+conn_id).html(result['text']);


            }
        });
    }

    function ShowDisabledServicesByConn(conn_id, type)
    {
        var form_conn = '';
        form_conn += '<span id=wat_rello>Waiting for All List Disabled Services... <div id="size_roller"><div class="lds-roller" style="margin-top: -23px; margin-right: -500px;"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div></span>';
        $("#servs_lists-"+conn_id).html(form_conn);
        $.ajax({
            url: 'webservice.php',
            type: 'GET',
            data: {
                'disabled_serv_conn_all_services': '1',
                'conn_id': conn_id,
                'type': type
            },
            success: function (data) {
                var result = $.parseJSON(data);
                $("#servs_lists-"+conn_id).html(result['text']);


            }
        });
    }



//---------------------------------------------------------------------------------------------------------------------------------------------------------------------

// end props.php & services.php e outros


