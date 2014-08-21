$(function(){

    $.fn.extend({
        addLoading: function()
        {

            if($(this).find(".loading").length == 0){

                var loadingBlock = $('<div/>', {
                    'class' : 'loading block'
                });

                $(this).append(loadingBlock);

                return true;

            }

            return false;

        },
        removeLoading: function(options)
        {

            var settings = {
                delay:     0
            };

            if(options)
                settings = $.extend({}, settings, options);


            $(this).find(".loading.block").delay(settings.delay).fadeOut("slow").queue(function(){$(this).remove()});

        }
    })

});