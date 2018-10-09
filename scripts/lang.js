$(document).ready(function(){
    var language = $.cookie('StudLang');
    var MainURL=location.origin;
    (language==1) ? $("#switch").attr('checked', true) : $("#switch").attr('checked', false);

    $("#switch").click(function(){
        if(language!=$(this).is(':checked')){
            SLang=$(this).is(':checked') ? 1 : 0;
            $.ajax({
                type: 'get',
                url: MainURL+'/lang.php',
                data: {
                    'SLang': SLang,
                },
                success: function (response) {
                    location.reload();
                },
                error: function () {
                    console.log('недошло');
                }
            });
        }
        
    });
});

function Decrypt(value, _lang) {
    var res = "";
    var mas = value.match(/.{2}/g);
    ((_lang!=0) && (_lang!=1)) ? _lang=0 : "";
    for (i = 0; i < mas.length; i++) {
        mas[i] = MatchDecrypt(mas[i], _lang);
    }
    res = mas.join('/');
    return res;
}


function MatchDecrypt(val, _lang) {
    if (val >= 10 && val < 20) {
        return Number(val) - 9;
    }
    else {
        switch (val) {
            case '20':
                return lang['Nu'][_lang];
                break;
            case '21':
                return lang['Nn'][_lang];
                break;
            case '22':
                return lang['Nbo'][_lang];
                break;
            case '23':
                return lang['Zach'][_lang];
                break;
            case '24':
                return lang['Nezach'][_lang];
                break;
            case '25':
                return lang['Nedop'][_lang];
                break;
            case '26':
                return lang['N'][_lang];
                break;
            case '27':
                return lang['Otr'][_lang];
                break;
            case '28':
                return lang['Dop'][_lang];
                break;

            case '31':
                return lang['N1'][_lang];
                break;
            case '32':
                return lang['N2'][_lang];
                break;
            case '33':
                return lang['N3'][_lang];
                break;
            case '34':
                return lang['N4'][_lang];
                break;
            case '35':
                return lang['N5'][_lang];
                break;
            case '36':
                return lang['N6'][_lang];
                break;
            case '37':
                return lang['N7'][_lang];
                break;

            case '40':
                return lang['N1.5'][_lang];
                break;
            case '41':
                return lang['N2.5'][_lang];
                break;
            case '42':
                return lang['N3.5'][_lang];
                break;
            case '43':
                return lang['N4.5'][_lang];
                break;
            case '44':
                return lang['N5.5'][_lang];
                break;
            case '45':
                return lang['N6.5'][_lang];
                break;
        }
    }
}


function smallText(object) {
    if((object.text().length>=6) && (object.text().length<10)){
        object.css("font-size", "0.85em");
    }
    else if(object.text().length>=10){
        object.css("font-size", "0.75em");
    }
}

lang = {
    "loading": ["Загрузка...", "Loading..."],
    "pay": ["ОПЛАТИТЬ", "PAY"],
    "cancel": ["Отмена", "Cancel"],
    "Delete": ["Удалить", "Delete"],
    
    "notGr": ["Не удалось отразить оценки!", "Failed to reflect the grades!"],
    "errorDel": ["Не удалось удалить из БД!", "Failed to send data to 1c."],    
    "errorToDB": ["Произошел сбой при передаче данных в БД!", "Failed to transfer the data to the database!"],
    "errorTransfer": ["Произошел сбой при передаче данных!", "The data transfer failed!"],
    "errorTransfer": ["Произошел сбой при передаче данных!", "The data transfer failed!"],
    
    "selectLessons": ["Выберите необходимые занятия!", "Choose the necessary classes!"],
    "payGenerated": ["Ваш платеж успешно сгенерирован. Можете оплачивать!", "translate"], //перевод

    "errorFrom1cPayNumber": ["Не удалось получить номер платежа от 1с.", "Failed to get payment number from 1c."],
    "errorFrom1cSum": ["Не удалось получить сумму платежа от 1с.", "Failed to get the payment amount from 1c."],
    "errorSendTo1c": ["Не удалось передать в 1с.", "Failed to send in 1c."],
    "errorSelect": ["Ошибка выбора!", "Selection error!"],
    "infoAboutPay": ["Внимание! \nОплату через ЕРИП необходимо производить в разделе «Академическая задолженность». Отсутствие выставленного платежа означает окончание срока действия счета (необходимо заново выписать отработку).",
                     "Внимание! \nОплату через ЕРИП необходимо производить в разделе «Академическая задолженность». Отсутствие выставленного платежа означает окончание срока действия счета (необходимо заново выписать отработку)."],

    
    "testing": ["Данная функция еще тестируется!", "This function is still being tested!"],
    "titleGenPay": ["Для данного занятия уже сгенерирован платеж в банке!", "For this lesson, a payment has already been generated in the bank!"],
    "titleSucPay": ["Данное занятие оплачено!", "This lesson is paid!"],
    "Nn": ["Н<sub>н</sub>", "A<sub>ng</sub>"],
    "Nu": ["Н<sub>у</sub>", "A<sub>g</sub>"],
    "Nbo": ["Н<sub>б.о</sub>", "A<sub>n.m</sub>"],
    "Zach": ["Зач", "Pass"],
    "Nezach": ["Незач", "Fail"],
    "Nedop": ["Недоп", "N/Al"],
    "Otr": ["Отр", "M"],
    "Dop": ["Доп", "Al"],
    "N": ["Н", "A"],
    "N1": ["Н<sub>1</sub>", "A<sub>1</sub>"],
    "N2": ["Н<sub>2</sub>", "A<sub>2</sub>"],
    "N3": ["Н<sub>3</sub>", "A<sub>3</sub>"],
    "N4": ["Н<sub>4</sub>", "A<sub>4</sub>"],
    "N5": ["Н<sub>5</sub>", "A<sub>5</sub>"],
    "N6": ["Н<sub>6</sub>", "A<sub>6</sub>"],
    "N7": ["Н<sub>7</sub>", "A<sub>7</sub>"],
    "N1.5": ["Н<sub>1.5</sub>", "A<sub>1.5</sub>"],
    "N2.5": ["Н<sub>2.5</sub>", "A<sub>2.5</sub>"],
    "N3.5": ["Н<sub>3.5</sub>", "A<sub>3.5</sub>"],
    "N4.5": ["Н<sub>4.5</sub>", "A<sub>4.5</sub>"],
    "N5.5": ["Н<sub>5.5</sub>", "A<sub>5.5</sub>"],
    "N6.5": ["Н<sub>6.5</sub>", "A<sub>6.5</sub>"]
};