/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var URL = window.location,

    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer');

// Sidebar
$(document).ready(function() {

    // $(document).bind("contextmenu",function(e){
    //     return false;
    // });



    $("#cpf").inputmask("999.999.999-99");
    $("#nascimento").inputmask("99/99/9999");
    $("#vencimento").inputmask("99/99/9999");
    $("#data").inputmask("99/99/9999");

    $("#nota").inputmask({
        mask: ["9.9", "99.9", ],
        unmaskAsNumber: true,
        keepStatic: true
    });
   // $("#dataTermino").inputmask("99/99/9999");
    $("#fone").inputmask({
        mask: ["(99) 9999-9999", "(99) 99999-9999", ],
        keepStatic: true
    });
    $("#telefone").inputmask("(99) 9999-9999");

    Ladda.bind( 'button[type=submit]' );

    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.height(),
            leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + $FOOTER.height();

        $RIGHT_COL.css('min-height', contentHeight);
    };

    $SIDEBAR_MENU.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $SIDEBAR_MENU.find('li').removeClass('active');
                $SIDEBAR_MENU.find('li ul').slideUp();
            }
            
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function() {
        if ($BODY.hasClass('nav-md')) {
            $BODY.removeClass('nav-md').addClass('nav-sm');
            $LEFT_COL.removeClass('scroll-view').removeAttr('style');

            if ($SIDEBAR_MENU.find('li').hasClass('active')) {
                $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
            }
        } else {
            $BODY.removeClass('nav-sm').addClass('nav-md');

            if ($SIDEBAR_MENU.find('li').hasClass('active-sm')) {
                $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
            }
        }

        setContentHeight();
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function(){  
        setContentHeight();
    });






});
// /Sidebar

jQuery(window).load(function () {
    $(".loader").delay(200).fadeOut("slow"); //retire o delay quando for copiar!
});

// Panel toolbox
$(document).ready(function() {
    $('.collapse-link').on('click', function() {
        var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function(){
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200); 
            $BOX_PANEL.css('height', 'auto');  
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function () {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
// /Tooltip

// Progressbar
if ($(".progress .progress-bar")[0]) {
    $('.progress .progress-bar').progressbar(); // bootstrap 3
}
// /Progressbar

// Switchery
$(document).ready(function() {

    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));


    for (var i = 0; i < elems.length; i++) {
        var switchery = new Switchery(elems[i]);

        elems[i].onload = function () {
            var id =  $(this).attr('id');

            verificaBoleto(id);
        }

        elems[i].onchange = function () {

            var isChecked = $(this).is(':checked');
            var id =  $(this).attr('id');

            $(".status_"+id).hide();


            if(isChecked){

                alteraBoleto(id, '2');

                verificaBoleto(id);
                //$(".status_"+id).html("<p>Pago</p>").show();

            }else{

                alteraBoleto(id, '1');

                verificaBoleto(id);

                //$(".status_"+id).html("<p>Ativo</p>").show();

            }
        };
    }
    function alteraBoleto(id, status) {

            var request = $.ajax({
                method:"POST",
                url: 'http://'+URL.host+'/dashboard/boleto/status/'+id,
                data: {

                    'status': status,
                },
                dataType: "html",
            });

            request.done(function (data) {

                var arrayOptions = $.parseJSON(data);

                $.each(arrayOptions, function (index, value) {

                   if(value === true){


                   }else{
                        alert('erro ao alterar boleto')
                   }

                })
            });
        }

    function verificaBoleto(id) {

        var request = $.ajax({
            method:"GET",
            url: 'http://'+URL.host+'/dashboard/boleto/verifica/'+id,
            dataType: "html",
        });

        request.done(function (data) {

            //console.log(data);

            var arrayOptions = $.parseJSON(data);

            $.each(arrayOptions, function (index, value) {

                    $.each(value, function (index, resp) {
                        if(resp === '1'){
                            $(".status_"+id).html("<p>Ativo</p>").show();
                        }else{
                            $(".status_"+id).html("<p>Pago</p>").show();
                        }
                    })
            })
        });
    }
});
// /Switchery


// iCheck
$(document).ready(function() {
    if ($("input.flat")[0]) {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'

            });
        });
    }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('table input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        $(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        $(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = $(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}

// Accordion
$(document).ready(function() {
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
});

// NProgress
if (typeof NProgress != 'undefined') {
    $(document).ready(function () {
        NProgress.start();
    });

    $(window).load(function () {
        NProgress.done();
    });
}

/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');


$(document).ready(function() {
    var handleDataTableButtons = function() {
        if ($("#datatable-buttons").length) {
            $("#datatable-buttons").DataTable({
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: "copy",
                        className: "btn-sm"
                    },
                    {
                        extend: "csv",
                        className: "btn-sm"
                    },
                    {
                        extend: "excel",
                        className: "btn-sm"
                    },
                    {
                        extend: "pdfHtml5",
                        className: "btn-sm"
                    },
                    {
                        extend: "print",
                        className: "btn-sm"
                    },
                ],
                responsive: true
            });
        }
    };

    TableManageButtons = function() {
        "use strict";
        return {
            init: function() {
                handleDataTableButtons();
            }
        };
    }();

    var jsonDataTablePortuquese = "//cdn.datatables.net/plug-ins/1.10.13/i18n/Portuguese-Brasil.json";

    $('#datatable-responsive').DataTable(
        {

            "iDisplayLength" : 50,
            "ordering": false,
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );




    $('#datatable-material').DataTable(
        {
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );

    $('#datatable-atividade').DataTable(
        {
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );

    $('#datatable-link').DataTable(
        {
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );

    $('#datatable-video').DataTable(
        {
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );


    $('table.display').DataTable(
        {
            responsive: true,
            language: {
                "url": jsonDataTablePortuquese,
            }
        }

    );




    TableManageButtons.init();
});


$(document).ready( function() {

    $('#tabelaQuestion').DataTable({

        "fnFilter" : false,
        "paging":   false,
        "ordering": false,
        "info":     false
    });


    $("#editor1").ckeditor();

    var url = window.location.host;

    // $("#string").stringToSlug();
    //
    // $("#stringModal").stringToSlug({
    //     getPut: '#permalinkModal',
    // });



    $("#delImg").click( function () {
        $( "#resultImagem" ).empty();
        $("#img").val("");
    });



});

$(document).ready(function(){

     var url = window.location.host;


    $('#tipoVideo').on('change', function(){

        var tipo = $('#tipoVideo option:selected').val();

       // console.log('tipo: '+tipo);
        
        if(tipo == 'Html5') {

            $('#linkVideo').val('');
            $('#linkVideo').attr('disabled', true);
            $('#fileVideo').attr('disabled', false);
        }

        if(tipo == 'YouTube' || tipo == 'Vimeo'){
            $('#linkVideo').attr('disabled', false);
            $("#linkVideo").val('');
            $("#linkVideo").focus();
            $('#fileVideo').val('');
            $('#fileVideo').attr('disabled', true);
        }
    });

    $('#fileVideo').on('click', function () {
        $('#linkVideo').val('');
        $('#linkVideo').attr('disabled', true);
        $('#tipoVideo > option[value="Html5"]').prop('selected', true);
    });

    var ROLE = $('#role');

    if(ROLE.val() == '2'){
        $('#status > option[value="1"]').prop('selected', true);

    };

    ROLE.on('change', function(){

        var TakeROLE = $('#role').val();

        if(TakeROLE != '2'){
            $('#status > option[value="2"]').prop('selected', true);
        }else{
            $('#status > option[value="1"]').prop('selected', true);
        }

    });

    // busca modelo //


    function editalegenda() {
        $(".editLeg").click(function ()
        {
            var url = window.location.host;
           
            var id = $(this).attr('id');
            var controller = $("input[name='controller']").val();


            //console.log('pegar controller'+controller);

            var request = $.ajax({

                method:"GET",
                url: 'http://'+url+'/dashboard/api/'+controller+'/'+id,
                dataType: "HTML",
            });
            request.done(function (e) {

                var arrayOptions = $.parseJSON(e);

                 $.each(arrayOptions, function (index, value) {

                     $.each(value, function (index, resOp) {

                        $("input[name='legenda']").val(resOp.legenda);
                        $("input[name='id']").val(resOp.id);

//console.log(id);

                     })

                 })

                 $('.bs-example-modal-lg_editLeg').on('hide.bs.modal', function() {

                     // limpa input

                        $("#nameIdLegenda").val('');
                        $("#nameLegenda").val('');


                })
            });

            return false;
        });
    };

    editalegenda();

    

    // console.log(url);

    function alterarLegenda() {
        $("#alterarLegenda").click(function ()
        {


            $(".load").html('<h4>Aguarde...</h4>');

            var id = $("input[name='id']").val();
            var legenda = $("input[name='legenda']").val();
            var controller = $("input[name='controller']").val();
            

            var request = $.ajax({

                method:"PUT",
                url: 'http://'+url+'/dashboard/api/'+controller+'/'+id,
                data: '?id='+id+'&legenda='+legenda,
                dataType: "HTML",
            });

            request.done(function (e) {
                //console.log(e);
                location.reload();
            });

            request.fail(function (e) {

                console.log(e)
            });


            return false;
        });
    };

    alterarLegenda();


    $(":input").inputmask();


    $('.deletar').click(function (e) {
        e.preventDefault();

        var urlDel = this.href;
        
        swal({
            title: "Tem certeza?",
            text: "Não será possível recuperar!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sim, excluir!",
            closeOnConfirm: false,
            showLoaderOnConfirm: true

        }, function () {

            window.location = urlDel;
            
        });
    });



    function dataFormatada(d) {
        var data = new Date(d),
            dia = data.getDate(),
            mes = data.getMonth() + 1,
            ano = data.getFullYear();
        return [dia, mes, ano].join('/');
    }

    function popularDateCronograma(){

        $('.modalTime').click(function(){

            $(".dataRes").html('<h3>Carregando, aguarde...</h3>');

            var idTime = $(this).attr('id');

            $('input[name="idTime"]').val(idTime);

            var request = $.ajax({
                    method:"GET",
                    url: 'http://'+url+'/dashboard/api/cronograma/'+idTime,
                    dataType: "HTML",
                    });

            request.done(function (e) {

            var arrayOptions = JSON.parse(e);

                $.each(arrayOptions, function (index, k) {

                    if(k.inicio !== null){

                    $('.dataRes').html('<h3>'+ $.format.date(k.inicio.date, "dd/MM/yyyy") +'</h3>');
                                            
                    }else{

                        $(".dataRes").html('<h3>Sem data Cadastrada!</h3>');
                        
                    }

                    })

            });
        request.fail(function (e) {
            console.log(e);
        });

        });

    }

    popularDateCronograma();

    function alterarDateCronograma(){

        $('#formDate').on('click', function(){

            var idTime = $('#idTime').val();
            var dataAtivacao = $('#dateCronograma').val();

            if(dataAtivacao == ''){

                alert('Insira uma data!');

                $('#resAlt').html('<h3>Necessário uma data!</h3>');
                
                return false;

            }else{

                 var request = $.ajax({

                    method:"PUT",
                    url: 'http://'+url+'/dashboard/api/cronograma/'+idTime,
                    data: '?id='+idTime+'&inicio='+dataAtivacao,
                    dataType: "HTML",
                });

            request.done(function (e) {
                var array = $.parseJSON(e);
                $.each(array, function (index, k) {
                   
                   if(k.success == true){

                       var data = k.inicio.date;

                        $("#dateCronograma").val('');
                        $('.dataRes').html('<h3>'+ $.format.date(data, "dd/MM/yyyy")+' <i class="fa fa-check-square-o"></i></h3>');
                   }else{

                       $('.dataRes').html('<h3>Erro ao cadastrar!</h3>');
                
                   }
            })
               
            });
            }



            return false;
        });

    }

    alterarDateCronograma();

      // limpa input
       $('.time').on('hide.bs.modal', function() {
            $("#idTime").val('');
            $("#dateCronograma").val('');
            $(".dataRes").html('');
       });


    // parar video
    $('.modal').on('hide.bs.modal', function() {
        $('.formModal').each (function(){
            this.reset();
        });
    });


    // parar video
    $('.video').on('hide.bs.modal', function() {
        location.reload();
    });


 
});

$(document).ready(function() {

     $("#myTab li:first-child").addClass("active");
     $("#myTabContent .tab-pane:first-child").addClass("active");


        var cnt = 10;

        TabbedNotification = function(options) {
          var message = "<div id='ntf" + cnt + "' class='text alert-" + options.type + "' style='display:none'><h2><i class='fa fa-bell'></i> " + options.title +
            "</h2><div class='close'><a href='javascript:;' class='notification_close'><i class='fa fa-close'></i></a></div><p>" + options.text + "</p></div>";

          if (!document.getElementById('custom_notifications')) {
            alert('doesnt exists');
          } else {
            $('#custom_notifications ul.notifications').append("<li><a id='ntlink" + cnt + "' class='alert-" + options.type + "' href='#ntf" + cnt + "'><i class='fa fa-bell animated shake'></i></a></li>");
            $('#custom_notifications #notif-group').append(message);
            cnt++;
            CustomTabs(options);
          }
        };

        CustomTabs = function(options) {
          $('.tabbed_notifications > div').hide();
          $('.tabbed_notifications > div:first-of-type').show();
          $('#custom_notifications').removeClass('dsp_none');
          $('.notifications a').click(function(e) {
            e.preventDefault();
            var $this = $(this),
              tabbed_notifications = '#' + $this.parents('.notifications').data('tabbed_notifications'),
              others = $this.closest('li').siblings().children('a'),
              target = $this.attr('href');
            others.removeClass('active');
            $this.addClass('active');
            $(tabbed_notifications).children('div').hide();
            $(target).show();
          });
        };

        CustomTabs();

        var tabid = idname = '';

        $(document).on('click', '.notification_close', function(e) {
          idname = $(this).parent().parent().attr("id");
          tabid = idname.substr(-2);
          $('#ntf' + tabid).remove();
          $('#ntlink' + tabid).parent().remove();
          $('.notifications a').first().addClass('active');
          $('#notif-group div').first().css('display', 'block');
        });
     
     
     
     //crud
     
     $(".tab3").on('click',function () {

         console.log('cliquei' )
         //$("#profile-tab2").click();
     });
     

 });

//alerts
$(document).ready(function(){

    /* CROPPER */

    function init_cropper() {


        if( typeof ($.fn.cropper) === 'undefined'){ return; }
        //console.log('init_cropper');

        var $image = $('#image');
        var $dataX = $('#dataX');
        var $dataY = $('#dataY');
        var $dataHeight = $('#dataHeight');
        var $dataWidth = $('#dataWidth');
        var $dataRotate = $('#dataRotate');
        var $dataScaleX = $('#dataScaleX');
        var $dataScaleY = $('#dataScaleY');
        var options = {
            aspectRatio: 1 / 1,
            preview: '.img-preview',
            crop: function (e) {
                $dataX.val(Math.round(e.x));
                $dataY.val(Math.round(e.y));
                $dataHeight.val(Math.round(e.height));
                $dataWidth.val(Math.round(e.width));
                $dataRotate.val(e.rotate);
                $dataScaleX.val(e.scaleX);
                $dataScaleY.val(e.scaleY);
            }
        };



        // Cropper
        $image.on({
            'build.cropper': function (e) {
                //console.log(e.type);
            },
            'built.cropper': function (e) {
                //console.log(e.type);
            },
            'cropstart.cropper': function (e) {
                //console.log(e.type, e.action);
            },
            'cropmove.cropper': function (e) {
                //console.log(e.type, e.action);
            },
            'cropend.cropper': function (e) {
                //console.log(e.type, e.action);
            },
            'crop.cropper': function (e) {
                //console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
            },
            'zoom.cropper': function (e) {
                //console.log(e.type, e.ratio);
            }
        }).cropper(options);


        // Buttons
        if (!$.isFunction(document.createElement('canvas').getContext)) {
            $('button[data-method="getCroppedCanvas"]').prop('disabled', true);
        }

        if (typeof document.createElement('cropper').style.transition === 'undefined') {
            $('button[data-method="rotate"]').prop('disabled', true);
            $('button[data-method="scale"]').prop('disabled', true);
        }


        // Methods
        $('.docs-buttons').on('click', '[data-method]', function () {
            var $this = $(this);
            var data = $this.data();
            var $target;
            var result;

            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }

            if ($image.data('cropper') && data.method) {
                data = $.extend({}, data); // Clone a new one

                if (typeof data.target !== 'undefined') {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined') {
                        try {
                            data.option = JSON.parse($target.val());
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                result = $image.cropper(data.method, data.option, data.secondOption);

                switch (data.method) {
                    case 'scaleX':
                    case 'scaleY':
                        $(this).data('option', -data.option);
                        break;

                    case 'getCroppedCanvas':

                            // Bootstrap's Modal
                            //$('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

                            $("#image").cropper('getCroppedCanvas').toBlob(function(blob){

                                var formData = new FormData();

                                var idUser = $("#image").attr('iduser');
                                var perfil = $("#image").attr('perfil');

                                formData.append('croppedImage', blob);

                                $.ajax('http://ava.spp.psc.br/dashboard/imagem/savethumb?user='+ idUser, {
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,

                                    success() {

                                        if(perfil === 'true'){
                                            location.href = 'http://ava.spp.psc.br/dashboard/users/perfil/' + idUser;

                                        }else {
                                            location.href = 'http://ava.spp.psc.br/dashboard/users/edit/' + idUser;
                                        }
                                    },
                                    error(e) {
                                        console.log(e);
                                    },

                                });


                            });

                        break;
                }

                if ($.isPlainObject(result) && $target) {
                    try {
                        $target.val(JSON.stringify(result));
                    } catch (e) {
                        console.log(e.message);
                    }
                }

            }
        });

        // Keyboard
        $(document.body).on('keydown', function (e) {
            if (!$image.data('cropper') || this.scrollTop > 300) {
                return;
            }

            switch (e.which) {
                case 37:
                    e.preventDefault();
                    $image.cropper('move', -1, 0);
                    break;

                case 38:
                    e.preventDefault();
                    $image.cropper('move', 0, -1);
                    break;

                case 39:
                    e.preventDefault();
                    $image.cropper('move', 1, 0);
                    break;

                case 40:
                    e.preventDefault();
                    $image.cropper('move', 0, 1);
                    break;
            }
        });



    };

    init_cropper()

    /* CROPPER --- end */



    function filterPolo(){

        $('#linkTurma').on('change', function () {

            var TURMA = $(this).val();
            var CONTROLLER = $('#linkTurma option:selected').attr('controller');

            console.log(URL.host);

            if( TURMA == 'Selecione'){

            }else{

                location.href = 'http://'+ URL.hostname +'/dashboard/'+CONTROLLER+'/filter?turma=' + TURMA;
            }

        })


    }

    filterPolo();

    function buscaAlunoTurma(){

        $('#alunoTurma').on('change', function () {

            var carregando = $(".carregando").show();


            var IdAluno = $(this).val();


            let promise = fetch('/dashboard/alunos/list?usuario=' + IdAluno, {method: 'GET' });

            promise.then(async (response) => {

                const data = await response.json();

                if(response.status == 422){

                    console.log(data);

                }else{

                    var options = '<option value="">Selecione</option>';

                    $.each(data, function (index, resTurma) {

                        $.each(resTurma, function (index, listTurma) {

                            options += '<option value="' + listTurma.id + '">' + listTurma.turma + '</option>'
                        });

                    });

                    $(".carregando").hide();

                    $('#turma').html(options);

                }
            })


        })

    }

    buscaAlunoTurma();




})




