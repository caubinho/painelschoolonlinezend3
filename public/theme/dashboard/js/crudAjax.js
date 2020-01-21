$(document).ready(function() {

    var URL = window.location.hostname;

    function cadastrar() {

        $("#formModal").on('submit', function (e) {

            e.preventDefault();

            $(this).ajaxSubmit(
                {
                    beforeSend:function () {
                        $(".progress").show();
                        $(".progress-bar").attr('aria-valuenow', '0');
                    },
                    uploadProgress:function (event,position,total,percentComplete) {
                        // $("#prog").attr('value', percentComplete);
                        // $("#percent").html(percentComplete+'%');

                        $(".progress-bar").attr('aria-valuenow',percentComplete ).css('width', percentComplete + '%' ).text(percentComplete + '%');
                    },
                    success: function (data) {
                        $(".progress").hide();

                        var success = '<div class="alert alert-success alert-dismissible fade in" role="alert">'+
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>'+
                            '</button>'+
                            'Cadastro com <strong>sucesso!</strong>'+
                            '</div>';

                        $(".res").html(success);
                    },
                    resetForm: true
                }
            );
        });
    }
    cadastrar();

    function deletar() {
        $('.deletarModal').click(function (e) {
            e.preventDefault();

            var urlDel = this.href;
            var id = $(this).attr('id');

            swal({
                title: "Tem certeza?",
                text: "Não será possível recuperar!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sim, excluir!",
                closeOnConfirm: true,
                showLoaderOnConfirm: true

            }, function () {

                var request = $.ajax({

                    method:"DELETE",
                    url: urlDel,
                    dataType: "HTML",
                });

                request.done(function (e) {

                    //console.log(e);

                    var arrayOptions = $.parseJSON(e);

                    $.each(arrayOptions, function (index, value) {

                        //console.log(value);

                        if(value === true){

                            var success = '<div class="alert alert-success alert-dismissible fade in" role="alert">'+
                                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>'+
                                '</button>'+
                                'Deletado com <strong>sucesso aqui!</strong>'+
                                '</div>';

                            $(".resModal").html(success);

                            $("#row_"+id).fadeOut(1000, function(){
                                $("#row_"+id).remove();
                            });

                        }else{

                            $("#resModal").html('Erro ao deletar!');
                        }
                    })
                });

                request.fail(function (e) {

                    $('#resModal').html(e);
                });

                return false;

            });

            return false;
        });
    }
    deletar()

    function delimage() {

        $('.apagarImg').click(function () {

            $("#file").val("");
            $("#resultImagem").html('<p>Apagando imagem...</p>');


            var id = $(this).attr('id');
            var controller = $(this).attr('controller');
            var action = $(this).attr('action');
            var status = $(this).attr('status');

            var request = $.ajax({
                method: "POST",
                url: "http://"+ URL +"/dashboard/"+ controller +"/"+action+"/"+ id,
                data: {
                    'status' : status
                },
                dataType: "HTML",
            });

            request.done(function (e) {
                 //console.log(e);
                location.reload();
            });
        });
    }
    delimage();

    function delContrato() {

        $('.apagarContrato').click(function () {

            $("#resultContrato").html('<p>Excluindo...</p>');

            var id = $(this).attr('id');
            var controller = $(this).attr('controller');
            var action = $(this).attr('action');
            var status = $(this).attr('status');

            var request = $.ajax({
                method: "POST",
                url: "http://"+ URL +"/dashboard/"+ controller +"/"+action+"/"+ id,
                data: {
                    'status' : status
                },
                dataType: "HTML",
            });

            request.done(function (e) {
                //console.log(e);
                location.reload();
            });
        });
    }
    delContrato();

    function password() {

        $('.gerar').click(function () {

            $('.res').html();
            $('.resSenha').val("");

            var qtd = $('#caracteres').val();
            var controller = $(this).attr('controller');

            if(qtd < '6') {

                $('.res').html('<p style="color: #f00000">Digite entre 6 e 9 dígitos</p>');

                return false;

                //alert('menor que 6');
            }else{

                $('.res').html('');

                var request = $.ajax({
                    method: "POST",
                    url: "http://" + URL + "/dashboard/" + controller + "/senha/" + qtd,
                    dataType: "HTML",
                });
                request.done(function (e) {
                    var arrayOptions = $.parseJSON(e);
                    $.each(arrayOptions, function (index, value) {
                        $('.resSenha').val(value);
                    })
                });

                request.fail(function (e) {
                    $('.res').html('<p style="color: #f00000">Erro ao gerar senha!</p>');

                });

            }

            return false;

        });
    }
    password();

    // parar video
    $('.modal').on('hide.bs.modal', function() {
        $('.resSenha').each (function(){
            $(this).val("");
        });
    });

    function anexarItem() {

        $('.anexarItem').on('click', function(){

            var rota            = $(this).attr('rota');
            var controller      = $(this).attr('data-controller');
            var aula            = $(this).attr('data-aula');
            var anexo           = $(this).attr('data-anexo');


            var request = $.ajax({
                method:"POST",
                url: 'http://'+URL+'/dashboard/'+ rota +'/new',
                data: {
                    'aula': aula,
                    'controller': controller,
                    'anexo': anexo,
                },
                dataType: "HTML",
            });

            request.done(function (e) {
                //console.log(e);
                location.reload();
            });
            request.fail(function (e) {
                console.log(e);
            });
            return false;

        });
    }
    anexarItem();

    function deletarItem() {

        $('.deletarItem').on('click', function(){

            var controller      = $(this).attr('controller');
            var id            = $(this).attr('id');

            var request = $.ajax({
                method:"POST",
                url: 'http://'+URL+'/dashboard/'+controller+'/delete/'+id,
                dataType: "HTML",
            });

            request.done(function (e) {

                //console.log(e.data.success);
                location.reload();
            });
            request.fail(function (e) {
                console.log('Falhou!');
            });
            return false;


        });

    }
    deletarItem();

    function anexarAluno() {

        $('.anexarAluno').on('click', function(){

            var usuario            = $(this).attr('data-usuario');
            var turma           = $(this).attr('data-turma');


            var request = $.ajax({
                method:"POST",
                url: 'http://'+URL+'/dashboard/alunos/new',
                data: {
                    'usuario': usuario,
                    'turma': turma,
                },
                dataType: "HTML",
            });

            request.done(function (e) {
                //console.log(e);
                location.reload();
            });
            request.fail(function (e) {
                console.log(e);
            });
            return false;

        });
    }
    anexarAluno();

    function enviarBoleto(){
        $(".enviarBoleto").on('click', function () {

            var idUsuario = $(this).attr('idUsuario');

            let promise = fetch('/dashboard/boleto/enviar/' + idUsuario, {method: 'GET' });

            promise.then(async (response) => {

                const resposta = await response.json();

                if(resposta.data != false){

                    new PNotify({
                        title: 'Sucesso',
                        text: resposta.msg,
                        type: 'success',
                        styling: 'bootstrap3'
                    });

                }else{

                    new PNotify({
                        title: 'Atenção',
                        text: resposta.msg,
                        type: 'error',
                        styling: 'bootstrap3'
                    });

                };

            });


        })
    }

    enviarBoleto();


});

$(document).ready(function () {

    $("input[type=checkbox]").prop('checked', false);

    $("#todos").on('click', function () {

        console.log('clicado 2');

        $('input[name=post]').prop('checked', true);




            if($("#btn-excluir").length == 0){
                $('#select').html('<div id="btn-excluir"><button class="btn btn-warning" type="submit">Excluir</button></div> ')

            }else{

                $("input[type=checkbox]").prop('checked', false);
                $('#btn-excluir').remove();
            }

    });

    $("input[type=checkbox]").on('click', function () {

        if($(this).attr('name') != 'todos'){

            if($(this).is(':checked')){
                $(this).prop('checked', true)


            }
        }

    });
});
