$(function(){

    var selectors = {
        clickableBlock : '.clickable-block',
        clickableBlockLink : '.clickable-block-link'
    };

    $(document).on('click', selectors.clickableBlock, function(event){

        var $clickableBlock = $(this),
            url = null;

        if($clickableBlock.data('url')){

            url = $clickableBlock.data('url');

        } else {

            var $clickableBlockLink = $clickableBlock.find(selectors.clickableBlockLink);
            if($clickableBlockLink.length > 0){

                url = $clickableBlockLink.attr('href');

            }

        }

        if(url){

            event.preventDefault();
            document.location = url;

        }

        return false;

    })

});