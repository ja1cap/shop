$(function(){

    var selectors = {
        clickableBlock : '.clickable-block',
        clickableBlockLink : '.clickable-block-link'
    };

    $(document).on('click', selectors.clickableBlock, function(event){

        var $clickableBlock = $(this);
        var $clickableBlockLink = $clickableBlock.find(selectors.clickableBlockLink);

        if($clickableBlockLink.length > 0){

            event.preventDefault();
            document.location = $clickableBlockLink.attr('href');

        }

        return false;

    })

});