$(function(){

    var ShopCartProposalPrice = $.ProposalPrice.extend(function(){

        var proposalPrice = this;

        proposalPrice.constructor = function(data){

            proposalPrice.data.amount = 1;
            proposalPrice['super'](data);

        };

        proposalPrice.incrementAmount = function(){
            proposalPrice.data.amount++;
            return proposalPrice.data.amount;
        };

        proposalPrice.decrementAmount = function(){
            proposalPrice.data.amount--;
            return proposalPrice.data.amount;
        };

        return proposalPrice;

    });

    var ShopCartCategory = $.Category.extend(function(){

        var category = this;

        /**
         * Create ShopCartProposalPrice instance
         * @param proposalPriceData
         * @returns {ShopCartProposalPrice}
         */
        category.createProposalPrice = function(proposalPriceData){
            return proposalPriceData ? new ShopCartProposalPrice(proposalPriceData) : null;
        };

        /**
         * Get category proposal prices amount
         * @returns {Number}
         */
        category.getProposalPricesAmount = function(){

            var amount = 0;

            $.each(category.data.proposalPrices, function(i, proposalPrice){
                amount += proposalPrice.data.amount;
            });

            return amount;

        };

    });

    var ShopCart = $.Catalog.extend(function(){

        var cart = this;
        cart.popupSelector = '#shopCartPopup';

        /**
         * Create storage
         * @param storageKey
         * @param callback
         * @returns {$.CookieStorage}
         */
        cart.createStorage = function(storageKey, callback){
            return new $.CookieStorage(storageKey, callback);
        };

        cart.updateStorage = function(callback){
            var data = cart['super'].updateStorage(callback);
            cart.updateProposalPricesAmount();
            return data;
        };

        /**
         * Create cart category
         * @returns {ShopCartCategory}
         */
        cart.createCategory = function(categoryData){
            return (categoryData ? new ShopCartCategory(categoryData) : null);
        };

        /**
         * Check if category is used
         * @param category
         * @returns boolean
         */
        cart.checkCategoryUsage = function(category){

            var isUsed = false;

            if((category instanceof ShopCartCategory) && category.getProposalPricesAmount() > 0){
                isUsed = true;
            }

            return isUsed;

        };

        /**
         * Update proposal prices amount badges
         * @returns {$.Catalog}
         */
        cart.updateProposalPricesAmount = function(){

            var $shopCartBtn = $('.shop-cart-btn');
            var $shopCartBtnAmount = $('.amount', $shopCartBtn);

            var amount = cart.getProposalPricesAmount();
            $shopCartBtnAmount.html(amount);

            if(amount == 0){
                $shopCartBtn.addClass('empty-catalog');
            } else {
                $shopCartBtn.removeClass('empty-catalog');
            }

            return cart;

        };

        /**
         * Get shop cart proposal prices total amount
         * @returns {number}
         */
        cart.getProposalPricesAmount = function(){

            var amount = 0;
            var categories = cart.getCategories();

            $.each(categories, function(categoryId, category){
                amount += category.getProposalPricesAmount();
            });

            return amount;

        };

        /**
         * Increment proposal price amount
         */
        cart.incrementProposalPriceAmount = function(proposalPriceData, updateStorageCallback){

            var category = cart.getOrAddCategory(proposalPriceData['categoryId']);

            if(category instanceof ShopCartCategory){
                var proposalPrice = category.getProposalPrice(proposalPriceData['priceId']);
                if(proposalPrice){
                    proposalPrice.incrementAmount();
                }
            }

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        /**
         * Decrement proposal price amount
         */
        cart.decrementProposalPriceAmount = function(proposalPriceData, updateStorageCallback){

            var category = cart.getOrAddCategory(proposalPriceData['categoryId']);

            if(category instanceof ShopCartCategory){
                var proposalPrice = category.getProposalPrice(proposalPriceData['priceId']);
                if(proposalPrice){
                    proposalPrice.decrementAmount();
                }
            }

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        cart.getPopup = function(){

            return $(cart.popupSelector);

        };

        cart.initPopup = function(){

            var $shopCartPopup = cart.getPopup();
            $shopCartPopup.dialog({
                dialogClass: 'shop-cart-popup-container',
                autoOpen: true,
                modal: true,
                resizable: false,
                draggable: false,
                width: $shopCartPopup.width(),
                close: function(){

                    $shopCartPopup
                        .dialog("destroy")
                        .remove()
                    ;

                }
            });

            return cart;

        };

        cart.openPopup = function(_options){

            var _settings = {
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            var $body = $('body');
            var $mainContainer = $('.main-container');
            var $shopCartPopup = cart.getPopup();

            if($shopCartPopup.length == 0){

                $mainContainer.addLoading();

                $.ajax({
                    cache: false,
                    url: _settings.popupUrl,
                    data: _settings.popupRequestData,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(html){

                        $body.append(html);
                        $mainContainer.removeLoading();

                        cart.initPopup();

                    }
                });

            } else {

                $shopCartPopup
                    .parent()
                    .addLoading()
                ;

                $.ajax({
                    cache: false,
                    url: _settings.popupUrl,
                    data: _settings.popupRequestData,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(html){

                        $shopCartPopup.html($(html).html());

                        $shopCartPopup
                            .parent()
                            .removeLoading()
                        ;

                    }
                });

            }

            return cart;

        };

        cart.refreshCartSummary = function(_options){

            var _settings = {
                popup : false,
                popupUrl : null,
                popupRequestData : {}
            };

            _settings = $.extend(_settings, _options, true);

            if(_settings.popup){

                cart.openPopup(_settings);

            } else {

                location.reload();

            }

            return cart;

        };

    });

    $.shopCart = new ShopCart('shopCart');

    $(document).on('click', '.add-to-cart', function(e){

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('cart');
        if(proposalPriceData){

            if($btn.hasClass('remove-other') && proposalPriceData.categoryId){
                $.shopCart.removeAllCategoryProposalPrices(proposalPriceData.categoryId);
            }

            $.shopCart.addProposalPrice(proposalPriceData, function(categories){

                var popupUrl = $btn.attr('href');
                $.shopCart.refreshCartSummary({
                    popup: (popupUrl != '#'),
                    popupUrl: popupUrl,
                    popupRequestData: {
                        shopCart : {
                            categories : categories
                        }
                    }
                });

            });

        }

        return false;

    });

    $(document).on('click', '.remove-from-cart', function(e){

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('cart');
        if(proposalPriceData){

            $.shopCart.removeProposalPrice(proposalPriceData, function(categories){

                var popupUrl = $btn.attr('href');
                $.shopCart.refreshCartSummary({
                    popup: popupUrl != '#',
                    popupUrl: popupUrl,
                    popupRequestData: {
                        shopCart : {
                            categories : categories
                        }
                    }
                });

            });

        }

    });

    $(document).on('click', '.increment-amount-in-cart', function(e){

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('cart');
        if(proposalPriceData){

            $.shopCart.incrementProposalPriceAmount(proposalPriceData, function(categories){

                var popupUrl = $btn.attr('href');
                $.shopCart.refreshCartSummary({
                    popup: (popupUrl != '#'),
                    popupUrl: popupUrl,
                    popupRequestData: {
                        shopCart : {
                            categories : categories
                        }
                    }
                });

            });

        }

    });

    $(document).on('click', '.decrease-amount-in-cart', function(e){

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('cart');
        if(proposalPriceData){

            $.shopCart.decrementProposalPriceAmount(proposalPriceData, function(categories){

                var popupUrl = $btn.attr('href');
                $.shopCart.refreshCartSummary({
                    popup: (popupUrl != '#'),
                    popupUrl: popupUrl,
                    popupRequestData: {
                        shopCart : {
                            categories : categories
                        }
                    }
                });

            });

        }

    });

    $(document).on('click', '.open-shop-cart', function(e){

        e.preventDefault();

        var $btn = $(this);

        var popupUrl = $btn.attr('href');
        $.shopCart.openPopup({
            popupUrl: popupUrl
        });

    });

});