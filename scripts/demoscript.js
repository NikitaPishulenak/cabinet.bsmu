$(document).ready(function () {
    var idStudent=$("input#idStudent").val();
    var idSubject="";
    var subjDivWidth=$("div.DialogFakFak").css('width');
    var language = $.cookie('StudLang');

    $("div.DialogFakFak").click(function () {
        var obj_this_contentGrade=$(this).find(".content_grade");
        obj_this_contentGrade.html("");
        if(obj_this_contentGrade.is(':hidden')){

            if($(".content_grade").is(':visible')){
                $(".CO").not($(this).find(".CO")).show();
                $(".COAll").not($(this).find(".COAll")).show();
                $(".content_grade").not(obj_this_contentGrade).hide();
                $(".fullText").not($(this).find(".fullText")).hide();
                $(".shortText").not($(this).find(".shortText")).show();
                $(".fullTextClose").not($(this)).css('display', 'none');
                $(".DialogFakFak").animate({width: subjDivWidth}, 400);
            }
            idSubject=$(this).attr('data-idSubject');

            $(this).find(".CO").hide();
            $(this).find(".COAll").hide();
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
                                let isNn=$(this).attr('data-Nn');
                                if (isNn==1) {$(this).parent().append("<div class='bullDec'>&bull;</div>")};

                                smallText($(this));
                                var nLesObj=$(this).prev().find('.nLesson');
                                (nLesObj.text()=="0") ? nLesObj.hide() : "";
                            });

                        });
                    },
                    error: function () {
                        alert(lang['notGr'][language]);
                    }
                });
                $(this).find(".fullTextClose").css('display', 'block');
            });
        }
        else if(obj_this_contentGrade.is(':visible')){
            $(this).find(".fullTextClose").css('display', 'none');
            obj_this_contentGrade.hide();
            $(this).find(".fullText").hide();
            $(this).find(".shortText").show();
            $(this).animate({width: subjDivWidth}, 400, function () {
                $(this).find(".CO").show();
                $(this).find(".COAll").show();
            });
        }
    });

    $("div.content_grade").click(function (event) {
        event.stopPropagation();
        event.preventDefault();
        return false;
    });
});

