$(function () {

    var console = new $.Console();

    var ProposalEstimator = $.Catalog.extend(function () {

        var estimator = this;
        estimator.popupSelector = '#comparisonPopup';

        /**
         * Create storage
         * @param storageKey
         * @param callback
         * @returns {$.CookieStorage}
         */
        estimator.createStorage = function (storageKey, callback) {
            return new $.CookieStorage(storageKey, callback);
        };

        estimator.updateStorage = function(callback){
            var data = estimator['super'].updateStorage(callback);
            estimator.updateProposalPricesAmount();
            return data;
        };

        /**
         * Update proposal prices amount badges
         * @returns {$.Catalog}
         */
        estimator.updateProposalPricesAmount = function(){

            var $comparisonBtn = $('.comparison-btn');
            var $comparisonBtnAmount = $('.amount', $comparisonBtn);

            var amount = estimator.getProposalPricesAmount();
            $comparisonBtnAmount.html(amount);

            if(amount == 0){
                $comparisonBtn.addClass('empty-comparison');
            } else {
                $comparisonBtn.removeClass('empty-comparison');
            }

            return estimator;

        };

        estimator.getPopup = function(){
            return $(estimator.popupSelector);
        };

        estimator.initPopup = function(){

            var $popup = estimator.getPopup();
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

            return estimator;

        };

        estimator.openPopup = function(_options){

            var _settings = {
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            var $body = $('body');
            var $mainContainer = $('.main-container');
            var $popup = estimator.getPopup();

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

                        estimator.initPopup();

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

            return estimator;

        };

        /**
         * Close popup
         * @returns {*}
         */
        estimator.closePopup = function(){

            var $popup = estimator.getPopup();
            $popup
                .dialog("destroy")
                .remove()
            ;

            return estimator;

        };

        estimator.refresh = function(_options){

            var _settings = {
                popup : false,
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            if(_settings.popup){

                var categories = estimator.getCategories();
                if(categories && $.isArray(categories) && categories.length > 0){
                    estimator.openPopup(_settings);
                } else {
                    estimator.closePopup();
                }

            } else {

                location.reload();

            }

            return estimator;

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

                console.log(categories);
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

    $(document).on('click', '.open-comparison', function(e){

        e.preventDefault();

        var $btn = $(this);

        var popupUrl = $btn.attr('href');
        $.proposalEstimator.openPopup({
            popupUrl: popupUrl
        });

    });

});
