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

        //$('select[multiple="multiple"], .chosen-select', $container).chosen(chosenOptions);
        $('select', $container).chosen(chosenOptions);

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

    $('.tooltip').each(function(){

        $(this).tooltip({
            content: $(this).attr('title'),
            position: {
                my: "center bottom-10",
                at: "center top"
            }
        });

    });

    $('.countdown').countDown();
    $('.request-form-container label').inFieldLabels();

    var $mediaLists = $('.media-list');
    $mediaLists.each(function(){

        var $mediaList = $(this);

        if($mediaList.hasClass('media-list-slider')){

            var mediaListItemsPerRow = $mediaList.data('items-per-row');
            var $mediaListItems = $mediaList.find('.media-list-item');

            if($mediaListItems.length > mediaListItemsPerRow){

                $mediaList.owlCarousel({
                    items : mediaListItemsPerRow,
                    navigation : true,
                    pagination : false,
                    rewindNav : false
                });

            }

        }

    });


    var $proposalsBlockList = $('.proposals-block .proposals-list');
    $proposalsBlockList.owlCarousel({
        items : $proposalsBlockList.data('items-amount') || 4,
        navigation : true,
        pagination : false
    });

    $('.reviews-list').owlCarousel({
        items : 4,
        navigation : true,
        pagination : false
    });

    $('.images-list').owlCarousel({
        items : 4,
        navigation : true,
        pagination : false
    });

    $(document).delegate('.request-form', 'submit', function(e){

        e.preventDefault();

        var $form = $(this),
            $customer_name = $form.find('.customer-name'),
            $customer_phone = $form.find('.customer-phone'),
            $customer_email = $form.find('.customer-email');

        var data = {
            customer_name : $customer_name.val(),
            customer_phone : $customer_phone.val(),
            customer_email : $customer_email.val()
        };

        if(data.customer_name && data.customer_phone){

            $.ajax({
                //@TODO paste process_request path
                //url : '{{ path('process_request') }}',
                data : data,
                success : function(response){
                    alert(response);
                    $customer_name.val([]).trigger('blur');
                    $customer_phone.val([]).trigger('blur');
                    $customer_email.val([]).trigger('blur');
                }
            });

        }

    });

    $(document).delegate('.callback-form', 'submit', function(e){

        e.preventDefault();

        var $form = $(this),
            $customer_name = $form.find('.customer-name'),
            $customer_phone = $form.find('.customer-phone'),
            $customer_email = $form.find('.customer-email'),
            $comment = $form.find('.comment');

        var data = {
            customer_name : $customer_name.val(),
            customer_phone : $customer_phone.val(),
            customer_email : $customer_email.val(),
            comment : $comment.val()
        };

        if(data.customer_name && data.customer_phone){

            $.ajax({
                //@TODO paste callback path
                //url : '{{ path('callback') }}',
                data : data,
                type : 'POST',
                success : function(response){
                    alert(response);
                    var $fancybox = $form.closest('.fancybox-wrap'),
                        $close = $fancybox.find('.fancybox-close');
                    $close.trigger('click');
                }
            });

        }

    });

    $(document).delegate('.order-proposal-form', 'submit', function(e){

        e.preventDefault();

        var $form = $(this),
            $price = $form.find('.price'),
            $name = $form.find('.name'),
            $customer_name = $form.find('.customer-name'),
            $customer_phone = $form.find('.customer-phone'),
            $customer_email = $form.find('.customer-email');

        var data = {
            price : $price.val(),
            name : $name.val(),
            customer_name : $customer_name.val(),
            customer_phone : $customer_phone.val(),
            customer_email : $customer_email.val()
        };

        if(data.customer_name && data.customer_phone){

            $.ajax({
                //@TODO paste order_proposal path
                //url : '{{ path('order_proposal') }}',
                data : data,
                type : 'POST',
                success : function(response){

                    alert(response);

                    var $fancybox = $form.closest('.fancybox-wrap'),
                        $close = $fancybox.find('.fancybox-close');

                    if($close.length > 0){
                        $close.trigger('click');
                    } else{
                        $customer_name.val([]).trigger('blur');
                        $customer_phone.val([]).trigger('blur');
                        $customer_email.val([]).trigger('blur');
                    }

                }
            });

        }

    });

    $body.trigger('DOMSubtreeModified');

});