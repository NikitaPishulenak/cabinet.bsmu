$(document).ready(function () {
    var language = $.cookie('StudLang');
    ((language!=0) && (language!=1)) ? language=0 : "";

    var MainURL=location.origin;
    $("#verifyDialog").hide();
    $("#payIdDialog").hide();
    $("#confirm_delPay").hide();
    absenteeisms = new Array("Н", "Н1", "Н2", "Н3", "Н4", "Н5", "Н6", "Н7", "Н1.5", "Н2.5", "Н3.5", "Н4.5", "Н5.5", "Н6.5", "A", "A1", "A2", "A3", "A4", "A5", "AН6", "A7", "A1.5", "A2.5", "A3.5", "A4.5", "A5.5", "A6.5");
    // absenteeisms_with_cause = new Array("Ну", "Нн", "Нб.о");
    var idStudent=$("input#idStudent").val();
    var idSubject="";
    var subjDivWidth=$("div.DialogFakFak").css('width');
    //var p="", l="", ex="";
    var p_mas= new Array(); // 1 занятие пз может быть разным кол-м часов
    var l_mas= new Array(); // 1 лекция- это 1
    var ex_mas= new Array(); //  как и практика передаются часы
    var id_mas=new Array();//массив ид оценок
    //var datLes_mas=new Array();//массив ид оценок

    $("div.DialogFakFak").click(function () {
        openerBlock=$(this);
        var obj_this_contentGrade=$(this).find(".content_grade");
        //obj_this_contentGrade.html("");
        var nameSub=$(this).find("span.fullText").html();
        var idSub=$(this).attr("data-idSubject");
        if(obj_this_contentGrade.is(':hidden')){
            $(this).css("cursor", "default");
            obj_this_contentGrade.html("");

            if($(".content_grade").is(':visible')){
                $("#pay").remove();
                $(".CO").not($(this).find(".CO")).show();
                $(".content_grade").not(obj_this_contentGrade).hide();
                $(".fullText").not($(this).find(".fullText")).hide();
                $(".shortText").not($(this).find(".shortText")).show();
                $(".fullTextClose").not($(this)).css('display', 'none');
                $(".DialogFakFak").animate({width: subjDivWidth}, 400);
                 $(".DialogFakFak").not($(this)).css("cursor", "pointer");
                $("div.selected").each(function () { //удаляю выделенные Н в скрытых блоках(предмет)
                    $(this).removeClass("selected");
                }); 
            }

            idSubject=$(this).attr('data-idSubject');

            $(this).find(".CO").hide();
            $(this).find(".shortText").hide();
            $(this).find(".fullText").show();
            obj_this_contentGrade.show();
            $(this).animate({width: "95%"}, 400, function () {
                $.ajax({
                    type: 'get',
                    url: 'view.php',
                    data: {
                        'idStudent': idStudent,
                        'idSubject': idSubject,
                        'ajaxTrue':"1"
                    },
                    beforeSend:function () {
                        obj_this_contentGrade.html(lang['loading'][language]);
                    },
                    success: function (response) {
                        obj_this_contentGrade.html(response);

                        $(function () {
                            obj_this_contentGrade.find('div.Otmetka').each(function () {
                                $(this).html(Decrypt($(this).html(), language));
                                smallText($(this));
                                var block = 0;
                                block = Available($(this));
                                switch (block) {
                                    case "available_grade":
                                        $(this).parent().addClass("available_grade");
                                        break;

                                    case "generated_account":
                                        $(this).parent().addClass("generated_account");
                                        break;

                                    case "payment_completed":
                                        $(this).parent().addClass("payment_completed").append("<div class='coin'><img src='scripts/images/coin.png'></div>");
                                        break;
                                }

                            });
                        });

                        $("#pay").click(function(){
                        	
                        	console.log("Сформировать запрос");
                            $(function(){
                                p_mas.length=0; l_mas.length=0; ex_mas.length=0; id_mas.length=0; //datLes_mas.length=0;
                                if($("div.selected").length==0){
                                    alert(lang['selectLessons'][language]);
                                }
                                else{
                                	$("#pay").attr('disabled', true);
                                    $("div.selected").each(function(){
                                        if($(this).attr('data-PL')==1){ //если это лк
                                            l_mas.push("1:"+$(this).find(".DataO").text());
                                        }
                                        else if($(this).attr('data-PL')==0){ //если это пз аттестация или коллоквиум
                                            if($(this).hasClass("Exm")){
                                                //ex_mas.push(countHoursAbs($(this).find(".Otmetka").text()));
                                                ex_mas.push("1");
                                            } 
                                            else{
                                                p_mas.push(countHoursAbs($(this).find(".Otmetka").text())+":"+$(this).find(".DataO").text());
                                            }
                                        }
                                        id_mas.push($(this).attr('data-Zapis'));
                                        //datLes_mas.push($(this).find(".DataO").text());
                                        
                                    });
                                    var verifyDialog, verifyForm;
                                    var p="0", l="0", ex="0";
                                    p=p_mas.join("|");
                                    l=l_mas.join("|");
                                    ex=ex_mas.join("|");
                                    idZapis=id_mas.join("|");
                                    //datLes=datLes_mas.join("|");
                                    //console.log(`l:${l} p:${p}`);
                                    if(p.length!=0 || l.length!=0 || ex.length!=0){
                                        verifyDialog = $("#verifyDialog").dialog({
                                        resizable: false,
                                        autoOpen: false,
                                        modal: true,
                                        buttons: [{
                                            text: lang['pay'][language],
                                            click: function () {
                                            	console.log("нажал кнопку 1");
                                                $.ajax({
                                                    type: 'get',
                                                    url: MainURL+'/pay/getData.php',
                                                    data: {
                                                        'idStudent': idStudent,
                                                        'status':"2",
                                                         'p': p,
                                                         'l': l,
                                                         'ex': ex,
                                                         'nameSubject': nameSub+""
                                                    },
                                                    beforeSend:function () {
                                                        $('body').append("<div class='modal'><img src='scripts/images/loading1.gif' class='loading_img'></div>");
                                                    },
                                                    success: function (response) {
                                                        $("div.modal").remove();
                                                        idOrder=response;
                                                        //var re = /^\d[0-9]+$/;
                                                        //console.log(response);
                                                        if (!isNaN(idOrder)) {
                                                            $.ajax({
                                                                type: 'get',
                                                                url: 'pay.php',
                                                                data: {
                                                                    'menuactiv': "generatePay",
                                                                    'idOrder': idOrder,
                                                                    'idStudent': idStudent,
                                                                    'status': "2",
                                                                    'price': price.replace(/[\,]/g,'.'),
                                                                    'idZap': idZapis,
                                                                    //'datLes': datLes,
                                                                    'idLessons': idSub
                                                                },
                                                                success: function (response) {
                                                                    if(response=="added"){
                                                                        var payIdDialog, payIdForm;
                                                                        payIdDialog = $("#payIdDialog").dialog({
                                                                            resizable: false,
                                                                            autoOpen: false,
                                                                            modal: true,
                                                                            buttons: {
                                                                                "OK": function () {
                                                                                    payIdDialog.dialog("close");
                                                                                    alert(lang['infoAboutPay'][language]);
                                                                                }
                                                                            }
                                                                        });
                                                                        payIdForm = payIdDialog.find("form").on("submit", function (event) {
                                                                            event.preventDefault();
                                                                        });
                                                                        payIdDialog.dialog("open");
                                                                        $("strong#idBepay").html(idOrder);
                                                                        //alert("Ваш платеж успешно сгенерирован. Можете оплачивать!");
                                                                        $(".fullTextClose").click();//закрыть и открыть блок для обновления данных 
                                                                    }
                                                                },
                                                                error: function () {
                                                                    alert(lang['errorToDB'][language]);
                                                                }
                                                            }); 
                                                        }
                                                        else{
                                                            alert(lang['errorFrom1cPayNumber'][language]);
                                                        }   
                                                    },
                                                    error: function () {
                                                        $("div.modal").remove();
                                                        alert(lang['errorTransfer'][language]);
                                                    }
                                                }); 
                                                verifyDialog.dialog("close");
                                            },

                                        }, {
                                            text: lang['cancel'][language],
                                            click: function () {
                                                verifyDialog.dialog("close");
                                                $("#pay").removeAttr('disabled');
                                            },
                                        }],
                                        
                                        close: function () {
                                            //verifyForm[0].reset();
                                        }
                                        });
                                        verifyForm = verifyDialog.find("form").on("submit", function (event) {
                                            event.preventDefault();
                                        }); 


                                        $.ajax({
                                            type: 'get',
                                            url: MainURL+'/pay/getData.php',

                                            data: {
                                                'idStudent': idStudent,
                                                'status':"1",
                                                 'p': p,
                                                 'l': l,
                                                 'ex': ex
                                            },
                                            beforeSend:function () {
                                                $('body').append("<div class='modal'><img src='scripts/images/loading1.gif' class='loading_img'></div>");
                                            },
                                            success: function (response) {
                                                price=response;
                                                $("div.modal").remove();
                                                if (!isNaN(price.replace(/[\,]/g,'.'))) {
                                                    verifyDialog.dialog("open");
                                                    $("strong#sumPay").html(response);
                                                }
                                                else{
                                                    alert(lang['errorFrom1cSum'][language]);
                                                    console.log(price);
                                                }
                                            },
                                            error: function () {
                                                $("div.modal").remove();
                                                alert(lang['errorTransferTo1C'][language]);
                                            }
                                        });
                                        }
                                        else{
                                            alert(lang['errorSelect'][language]);
                                        }
                                    }
                                })
                            });
                        },
                        error: function () {
                            alert(lang['notGr'][language]);
                        }
                    });
                $(this).find(".fullTextClose").css('display', 'block');
            });
        }
        // else if(obj_this_contentGrade.is(':visible')){
        //     $(this).find(".fullTextClose").css('display', 'none');
        //     obj_this_contentGrade.hide();
        //     $(this).find(".fullText").hide();
        //     $(this).find(".shortText").show();
        //     $(this).animate({width: subjDivWidth}, 400, function () {
        //         $(this).find(".CO").show();
        //     });
        // }


    });

    $('div').delegate(".fullTextClose", "click", function (event) {
        event.stopPropagation();
        event.preventDefault();
        $(this).parent().find(".content_grade").hide();
        $(this).hide();
        $(this).parent().find(".fullText").hide();
        $(this).parent().find(".shortText").show();
        $(this).parent().animate({width: subjDivWidth}, 400);
        $(this).parent().find(".CO").show();
        $(this).parent().css("cursor", "pointer");
        $(".available_grade").removeClass("selected");
    });


    $('div.cancelPay').click(function(){
        var billText=$(this).parent().find(".gen_idOrder").text();
        var priceText=$(this).parent().find(".gen_price").text();
        var dialog_confirm_delPay, form_confirm_delPay;
        //форма удаления записи платежа
        dialog_confirm_delPay = $("#confirm_delPay").dialog({
            resizable: false,
            autoOpen: false,
            modal: true,
            width: '300',

            buttons: [{
                text: lang['Delete'][language],
                click: function () {
                    $.ajax({
                        type: 'get',
                        url: MainURL+'/pay/getData.php',
                        data: {
                            'status':"5",
                            'idOrder': billText
                        },

                        beforeSend:function () {
                            $('body').append("<div class='modal'><img src='scripts/images/loading1.gif' class='loading_img'></div>");
                        },

                        success: function () {
                            $.ajax({
                                type: 'get',
                                url: 'pay.php',
                                data: {
                                    'menuactiv':"deletePay",
                                    'idOrder': billText
                                },
                                success: function () {
                                    //console.log('succsess deleted in DB');
                                    window.location.reload();
                                },
                                error: function () {
                                    alert(lang['errorDel'][language]);
                                }
                            });     
                                },
                        error: function () {
                            alert(lang['errorSendTo1c'][language]);
                        }
                    });    

                    dialog_confirm_delPay.dialog("close");                             
                },

            }, {
                text: lang['cancel'][language],
                click: function () {
                    dialog_confirm_delPay.dialog("close");    

                },
            }],
            
            close: function () {
                //form_confirm_delPay[0].reset();
            }
        });
        form_confirm_delPay = dialog_confirm_delPay.find("form").on("submit", function (event) {
            event.preventDefault();
        });

        dialog_confirm_delPay.dialog("open");
        $("#idDelPay").html(billText);
        $("#idDelPrice").html(priceText);
        //alert("Данная функция еще тестируется!");
    });


    $("div.content_grade").click(function (event) {
        event.stopPropagation();
        event.preventDefault();
        return false;
    });

    //Если клик по выделенной ячейке отменить выделение и наоборот
    $('div').delegate(".available_grade", "click", function () {
        if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
        }
        else {
            $(this).addClass("selected");
        }
    });

    $('div').delegate(".generated_account", "mouseover", function () {
        $(this).attr('title', lang['titleGenPay'][language]);
    });
    $('div').delegate(".payment_completed", "mouseover", function () {
        $(this).attr('title', lang['titleSucPay'][language]);
    });
});

function Available(grade) {
    var curStatus=grade.parent().attr("data-pStatus");
    switch (curStatus) {
        case '0':
            if(!grade.parent().hasClass("Exm"))
                 return "available_grade";
            break;

        case '1':
            return "generated_account";
            break;
        
        case '2':
            return "payment_completed";
            break;
        default:
            return "-1";
            break;
    }
    if (curStatus==0){
        return 1;
    } 
    else {
        return 0;
    }
}

function countHoursAbs(str){ //функция перевода Н3.5ч в 3.5
    var c_gr = str.split("/");
    var res=0;
    var arr={'Н1':'1', 'Н2':'2', 'Н3':'3', 'Н4':'4', 'Н5':'5', 'Н6':'6', 'Н7':'7', 'Н1.5':'1.5', 'Н2.5':'2.5', 'Н3.5':'3.5', 'Н4.5':'4.5', 'Н5.5':'5.5', 'Н6.5':'6.5', 'Н':'1',
'A1':'1', 'A2':'2', 'A3':'3', 'A4':'4', 'A5':'5', 'A6':'6', 'A7':'7', 'A1.5':'1.5', 'A2.5':'2.5', 'A3.5':'3.5', 'A4.5':'4.5', 'A5.5':'5.5', 'A6.5':'6.5', 'A':'1'}; 
    for (var i = 0; i < c_gr.length; i++) {
        if(absenteeisms.indexOf(c_gr[i])!=-1){
            res+=new Number(arr[c_gr[i]]);
        }
    }
    return res;
}
