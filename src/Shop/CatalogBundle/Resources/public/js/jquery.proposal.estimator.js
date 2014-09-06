$(function () {

    var console = new $.Console();

    var ProposalEstimator = $.Catalog.extend(function () {

        var estimator = this;

        estimator.popupSelector = '#comparisonPopup';

        estimator.comparisonButtonDataAttributename = 'compare';

        estimator.compareButtonClass = 'compare-btn';
        estimator.compareButtonSelector = '.' + estimator.compareButtonClass;
        estimator.addToComparisonText = Translator.trans('shop.comparison.add.proposal', {}, 'ShopCatalogBundle');

        estimator.removeFromComparisonButtonSelector = '.remove-from-comparison';
        estimator.removeFromComparisonText = Translator.trans('shop.comparison.remove.proposal', {}, 'ShopCatalogBundle');

        estimator.inComparisonButtonClass = 'active';

        estimator.comparisonButtonSelector = '.comparison-btn';
        estimator.comparisonButtonAmountSelector = '.amount';
        estimator.emptyComparisonButtonClass = 'empty-comparison';

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

            var $comparisonButtons = $(estimator.comparisonButtonSelector);

            $comparisonButtons.each(function(){

                var $comparisonBtn = $(this);
                var $comparisonBtnAmount = $(estimator.comparisonButtonAmountSelector, $comparisonBtn);

                var amount = estimator.getProposalPricesAmount();
                $comparisonBtnAmount.html(amount);

                if(amount == 0){
                    $comparisonBtn.addClass(estimator.emptyComparisonButtonClass);
                } else {
                    $comparisonBtn.removeClass(estimator.emptyComparisonButtonClass);
                }

            });

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

        estimator.markComparisonButton = function($btn){

            var $wrapper = $btn.parent();

            if($wrapper.hasClass('btn')){

                var proposalPriceData = $btn.data($.proposalEstimator.comparisonButtonDataAttributename);
                var inComparison = estimator.hasProposalPrice(proposalPriceData);

                if(inComparison){

                    $wrapper.addClass($.proposalEstimator.inComparisonButtonClass);

                    if($.proposalEstimator.removeFromComparisonText){
                        $btn.text($.proposalEstimator.removeFromComparisonText);
                    }

                } else {

                    $wrapper.removeClass($.proposalEstimator.inComparisonButtonClass);

                    if($.proposalEstimator.addToComparisonText){
                        $btn.text($.proposalEstimator.addToComparisonText);
                    }

                }


            }

        };

        estimator.markComparisonButtons = function(){

            var $buttons = $($.proposalEstimator.compareButtonSelector);

            $buttons.each(function(){
                var $btn = $(this);
                estimator.markComparisonButton($btn);
            });

            return estimator;

        };

    });

    var methods = {
        init : function(){

            $.proposalEstimator = new ProposalEstimator('proposalEstimator');

            $.proposalEstimator.markComparisonButtons();

            $(document).on('click', $.proposalEstimator.compareButtonSelector, methods.$compareProposalPrice);

            $(document).on('click', $.proposalEstimator.removeFromComparisonButtonSelector, methods.$removeProposalPrice);

            $(document).on('click', '.refresh-comparison', methods.$refreshComparison);

            $(document).on('click', '.open-comparison', methods.$openComparisonPopup);

        },
        $compareProposalPrice : function(e){

            var $btn = $(this);
            var $wrapper = $btn.parent();

            var inComparison = $wrapper.hasClass($.proposalEstimator.inComparisonButtonClass);

            if(inComparison){

                methods.$removeProposalPrice.apply(this, [e]);;

            } else {

                methods.$addProposalPrice.apply(this, [e]);

            }

        },
        $addProposalPrice : function(e){

            e.preventDefault();

            var $btn = $(this);

            var proposalPriceData = $btn.data($.proposalEstimator.comparisonButtonDataAttributename);
            if (proposalPriceData) {

                var categoryId = proposalPriceData.categoryId;

                $.proposalEstimator.addProposalPrice(proposalPriceData, function (categories) {

                    $.proposalEstimator.markComparisonButton($btn);

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

        },
        $removeProposalPrice : function(e){

            e.preventDefault();

            var $btn = $(this);

            var proposalPriceData = $btn.data($.proposalEstimator.comparisonButtonDataAttributename);
            if(proposalPriceData){

                var categoryId = proposalPriceData.categoryId;

                $.proposalEstimator.removeProposalPrice(proposalPriceData, function(categories){

                    if($btn.hasClass($.proposalEstimator.compareButtonClass)){

                        $.proposalEstimator.markComparisonButton($btn);

                    } else {

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

                    }

                });

            }

        },
        $refreshComparison : function(e){

            e.preventDefault();

            var $btn = $(this);

            var popupUrl = $btn.attr('href');
            $.proposalEstimator.refresh({
                popup: popupUrl != '#',
                popupUrl: popupUrl
            });

        },
        $openComparisonPopup : function(e){

            e.preventDefault();

            var $btn = $(this);

            var popupUrl = $btn.attr('href');
            $.proposalEstimator.openPopup({
                popupUrl: popupUrl
            });

        }
    };

    methods.init();

});
