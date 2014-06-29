$(function(){

    var chosenOptions = {};
    chosenOptions.placeholder_text_multiple = "Выберите значения";
    chosenOptions.placeholder_text_single = "Выберите значение";
    chosenOptions.no_result_text = "Значений не найдено";

    $('select[multiple="multiple"], .chosen-select').chosen(chosenOptions);

});