$(function(){

    var $body = $('body');

    $(document).on('click', '.dialog-close-btn', function(e){
        e.preventDefault();
        $(this).closest('.ui-dialog-content').dialog('close');
    });

    $.fn.applyChosen = function(){

        var $container = $(this);
        var chosenOptions = {};
        chosenOptions.placeholder_text_multiple = "Выберите значения";
        chosenOptions.placeholder_text_single = "Выберите значение";
        chosenOptions.no_result_text = "Значений не найдено";

        $('select[multiple="multiple"], .chosen-select', $container).chosen(chosenOptions);

    };

    $body.applyChosen();

    $('.nailthumb-container').each(function(){

        var data = $(this).data(),
            nailthumbOptions = {};

        $.each(data, function(key, value){

            if(key.indexOf('nailthumb') !== false){

                var optionName = key.replace('nailthumb', '');
                optionName = (optionName.substring(0, 1).toLowerCase() + optionName.substring(1));

                nailthumbOptions[optionName] = value;

            }

        });

        $(this).nailthumb($.extend({
            method : 'crop'
        }, nailthumbOptions));

    });

    $(".fancybox").fancybox({
        openEffect	: 'none',
        closeEffect	: 'none',
        padding: 0,
        loop : false,
        helpers: {
            overlay: {
                locked: false
            }
        }
    });

    $('.fancybox-media').fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        padding: 0,
        loop : false,
        helpers : {
            media : true,
            overlay: {
                locked: false
            }
        }
    });

    $(".callback-fancy-box").fancybox({
        type: 'ajax',
        autoCenter : false,
        fitToView : false,
        scrolling : 'visible',
        wrapCSS : 'callback-fancybox dark-fancybox',
        openEffect	: 'none',
        closeEffect	: 'none',
        padding: 0,
        loop : false,
        width : 645,
        minWidth : 645,
        maxWidth : 645,
        helpers: {
            overlay: {
                locked: false
            }
        }
    });

    $body.trigger('DOMSubtreeModified');

});