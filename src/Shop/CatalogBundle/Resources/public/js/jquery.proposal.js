$(function(){

    //var $filtersForm = $('#filtersForm, #extraFiltersForm');
    //
    //var proposalUrl = '{{ shop_catalog_proposal_url(proposal) }}';
    //
    //$(':input', $filtersForm).change(function(){
    //
    //    document.location = proposalUrl + '?' +$filtersForm.serialize();
    //
    //});
    //
    //$('.proposal-actions').owlCarousel({
    //    autoPlay: 5000,
    //    navigation : true,
    //    pagination : false,
    //    items : 3
    //});

    var $proposalsContainer = $('#proposal-container');

    if($proposalsContainer.length > 0){

        var $activeImageContainer = $('.active-image-container', $proposalsContainer);
        var $imagesContainer = $('.images-container', $proposalsContainer);

        var $images = $imagesContainer.children('.images');
        var $prev = $activeImageContainer.find('.prev');
        var $next = $activeImageContainer.find('.next');

        if($images.length == 0){
            $prev.hide();
            $next.hide();
        }

        $activeImageContainer.disableSelection();

        //@TODO add scrollbar
        $imagesContainer.sly({
            horizontal: 1,
            itemNav: 'basic',
            smart: 1,
            activateOn: 'click',
            mouseDragging: 1,
            touchDragging: 1,
            releaseSwing: 1,
            startAt: $images.find('.main-image').index(),
            scrollBy: 1,
            speed: 300,
            elasticBounds: 1,
            easing: 'easeOutExpo',
            dragHandle: 1,
            dynamicHandle: 1,
            clickBar: 1,
            prev: $prev,
            next: $next
        }, {
            active : function(eventName, itemIndex){

                var $image = $images.find('.image').eq(itemIndex),
                    $img = $activeImageContainer.find('img'),
                    $fancyBoxLink = $activeImageContainer.find('.fancybox'),
                    activeImgUrl = $img.attr('src'),
                    imgUrl = $image.data('image_url'),
                    referenceImgUrl = $image.data('reference_image_url'),
                    isActive = (activeImgUrl == imgUrl);

                if(!isActive){

                    $img.attr('src', imgUrl);

                    if($fancyBoxLink.length > 0 && referenceImgUrl){
                        $fancyBoxLink.attr('href', referenceImgUrl);
                    }

                }

            }
        });

    }

});