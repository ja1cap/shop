$(function(){

    var $imagesPreviewContainers = $(".images-preview-container");

    $imagesPreviewContainers.each(function(){

        var $imagesPreviewContainer = $(this);
        var $imagesPreview = $imagesPreviewContainer.children('.images-preview');
        var $wrap = $imagesPreviewContainer.parent();

        var $previewContainer = $imagesPreviewContainer.closest('.preview-container'),
            $imageContainer = $previewContainer.find('.inner-container').find('.image-container');

        $imagesPreviewContainer.sly({
            itemNav: 'basic',
            smart: 1,
            activateOn: 'click',
            mouseDragging: 1,
            touchDragging: 1,
            releaseSwing: 1,
            startAt: $imagesPreview.find('.main-image').index(),
            scrollBy: 1,
            speed: 300,
            elasticBounds: 1,
            easing: 'easeOutExpo',
            dragHandle: 1,
            dynamicHandle: 1,
            clickBar: 1,
            prev: $wrap.find('.prev'),
            next: $wrap.find('.next')
        }, {
            active : function(eventName, itemIndex){

                var $imagePreviewContainer = $imagesPreview.find('.image-preview-container').eq(itemIndex),
                    $image = $imageContainer.find('img'),
                    imageUrl = $image.attr('src'),
                    previewImageUrl = $imagePreviewContainer.data('image_url'),
                    isActive = (imageUrl == previewImageUrl);

                if(!isActive){

                    $image.attr('src', previewImageUrl);

                }

            }
        });

    });

});