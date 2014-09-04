$(function () {

    var console = new $.Console();

    var ProposalEstimator = $.Catalog.extend(function () {

        var compare = this;
        compare.popupSelector = '#comparisonPopup';

        /**
         * Create storage
         * @param storageKey
         * @param callback
         * @returns {$.CookieStorage}
         */
        compare.createStorage = function (storageKey, callback) {
            return new $.CookieStorage(storageKey, callback);
        };

        compare.getPopup = function(){
            return $(compare.popupSelector);
        };

        compare.initPopup = function(){

            var $popup = compare.getPopup();
            $popup.dialog({
                dialogClass: 'ui-popup',
                autoOpen: true,
                modal: true,
                resizable: false,
                draggable: false,
                width: $popup.width(),
                close: function(){

                    $popup
                        .dialog("destroy")
                        .remove()
                    ;

                }
            });

            return compare;

        };

        compare.openPopup = function(_options){

            var _settings = {
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            var $body = $('body');
            var $mainContainer = $('.main-container');
            var $popup = compare.getPopup();

            if($popup.length == 0){

                $mainContainer.addLoading();

                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: _settings.popupUrl,
                    data: _settings.popupRequestData,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(html){

                        $body.append(html);
                        $mainContainer.removeLoading();

                        compare.initPopup();

                    },
                    error: function(){
                        $mainContainer.removeLoading();
                    }
                });

            } else {

                $popup
                    .parent()
                    .addLoading()
                ;

                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: _settings.popupUrl,
                    data: _settings.popupRequestData,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(html){

                        $popup.html($(html).html());

                        $popup
                            .parent()
                            .removeLoading()
                        ;

                    }
                });

            }

            return compare;

        };

        compare.refresh = function(_options){

            var _settings = {
                popup : false,
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            if(_settings.popup){

                compare.openPopup(_settings);

            } else {

                location.reload();

            }

            return compare;

        };

    });

    $.proposalEstimator = new ProposalEstimator('proposalEstimator');

    $(document).on('click', '.compare-btn', function (e) {

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('compare');
        if (proposalPriceData) {

            var categoryId = proposalPriceData.categoryId;

            $.proposalEstimator.addProposalPrice(proposalPriceData, function (categories) {

                var popupUrl = $btn.attr('href');
                $.proposalEstimator.refresh({
                    popup: (popupUrl != '#'),
                    popupUrl: popupUrl,
                    popupRequestData: {
                        categoryId: categoryId,
                        proposalEstimator : {
                            categories : categories
                        }
                    }
                });

            });

        }

        return false;

    });

    $(document).on('click', '.refresh-comparison', function(e){

        e.preventDefault();

        var $btn = $(this);

        var popupUrl = $btn.attr('href');
        $.proposalEstimator.refresh({
            popup: popupUrl != '#',
            popupUrl: popupUrl
        });

    });

    $(document).on('click', '.remove-from-comparison', function(e){

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('compare');
        if(proposalPriceData){

            var categoryId = proposalPriceData.categoryId;

            $.proposalEstimator.removeProposalPrice(proposalPriceData, function(categories){

                var popupUrl = $btn.attr('href');
                $.proposalEstimator.refresh({
                    popup: popupUrl != '#',
                    popupUrl: popupUrl,
                    popupRequestData: {
                        categoryId : categoryId,
                        proposalEstimator : {
                            categories : categories
                        }
                    }
                });

            });

        }

    });

});
