$(function(){

    var ShopCart = function (){

        var cart = this;
        var storageKey = 'shopCart';

        var categories = {};

        cart.popupSelector = '#shopCartPopup';

        /**
         * Init cart storage
         */
        cart.initStorage = function(){

            $.cookie.json = true;

            var storageData = $.cookie(storageKey);
            if(storageData){

                if($.isPlainObject(storageData['categories'])){
                    categories = storageData['categories'];
                }

            }

            cart.updateStorage();

        };

        /**
         * Update storage
         * @returns {{categories: Object}}
         */
        cart.updateStorage = function(callback){

            var usedCategories = {};

            $.each(categories, function(categoryId, category){

                var isUsed = cart.checkCategoryUsage(category);
                if(isUsed){
                    usedCategories[categoryId] = category;
                }

            });

            categories = usedCategories;

            var data = {
                categories : categories
            };

            $.cookie(storageKey, data, {
                path: '/'
            });

            if(callback && typeof callback == 'function'){
                callback(categories);
            }

            cart.updateProposalPricesAmount();

            return data;

        };

        /**
         * Update proposal prices amount badges
         * @returns {ShopCart}
         */
        cart.updateProposalPricesAmount = function(){

            var $headerShopCart = $('.header-shop-cart');
            var $headerShopCartAmount = $('.amount', $headerShopCart);

            var amount = cart.getProposalPricesAmount();
            $headerShopCartAmount.html(amount);

            if(amount == 0){
                $headerShopCartAmount.hide();
            } else {
                $headerShopCartAmount.show();
            }

            return cart;

        };

        /**
         * Get shop cart proposal prices total amount
         * @returns {number}
         */
        cart.getProposalPricesAmount = function(){

            var amount = 0;

            $.each(categories, function(categoryId, category){
                $.each(category.proposalPrices, function(i, proposalPrice){
                    amount += proposalPrice.amount;
                });
            });

            return amount;

        };

        /**
         * Get cart categories
         * @returns {{}}
         */
        cart.getCategories = function(){
            return categories;
        };

        /**
         * Create cart category
         * @param categoryId
         * @returns {{id: *, proposalPrices: Array}}
         */
        cart.createCategory = function(categoryId){

            return {
                id : categoryId,
                proposalPrices : []
            };

        };

        /**
         * Get cart category
         * @param categoryId
         * @returns {*}
         */
        cart.getCategory = function(categoryId){
            return categories[categoryId];
        };

        /**
         * Add category to cart
         * @param category
         * @returns {ShopCart}
         */
        cart.addCategory = function(category){
            var categoryId = category.id;
            if(categoryId){
                categories[categoryId] = category;
            }
            return cart;
        };

        /**
         * Get category if it exists or add new category to cart
         * @param categoryId
         * @returns {*}
         */
        cart.getOrAddCategory = function(categoryId){

            var category = cart.getCategory(categoryId);

            if(!category){
                category = cart.createCategory(categoryId);
                cart.addCategory(category);
            }

            return category;

        };

        /**
         * Remove category from cart
         * @param categoryId
         * @returns {ShopCart}
         */
        cart.removeCategory = function(categoryId){

            var category = categories[categoryId];
            if(category){
                delete categories[categoryId];
            }

            cart.updateStorage();

            return cart;

        };

        /**
         * Check if category is used, if not used remove from cart
         * @param category
         * @returns boolean
         */
        cart.checkCategoryUsage = function(category){

            var isUsed = false;

            if(category && ($.isArray(category.proposalPrices) && category.proposalPrices.length > 0)){
                isUsed = true;
            }

            return isUsed;

        };

        /**
         * Check if proposal price exists in cart
         * @param categoryId
         * @param priceId
         * @returns {boolean}
         */
        cart.hasProposalPrice = function(categoryId, priceId){

            var exists = false;

            var category = categories[categoryId];
            if(category && $.isArray(category.proposalPrices)){

                $.each(category.proposalPrices, function(i, price){

                    if(price && price.id == priceId){
                        exists = true;
                        return false;
                    }

                    return true;

                });

            }

            return exists;

        };

        /**
         * Add proposal price to cart
         */
        cart.addProposalPrice = function(categoryId, proposalId, priceId, updateStorageCallback){

            if(!cart.hasProposalPrice(categoryId, priceId)){

                cart.getOrAddCategory(categoryId).proposalPrices.push({
                    id : priceId,
                    proposalId : proposalId,
                    categoryId : categoryId,
                    amount : 1
                });

            }

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        /**
         * Increment proposal price amount
         */
        cart.incrementProposalPriceAmount = function(categoryId, proposalId, priceId, updateStorageCallback){

            if(!cart.hasProposalPrice(categoryId, priceId)){

                cart.getOrAddCategory(categoryId).proposalPrices.push({
                    id : priceId,
                    proposalId : proposalId,
                    categoryId : categoryId,
                    amount : 1
                });

            } else {

                $.each(cart.getCategory(categoryId).proposalPrices, function(i, proposalPrice){

                    if(proposalPrice.id == priceId){
                        proposalPrice.amount++;
                    }

                });

            }

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        /**
         * Decrease proposal price amount
         */
        cart.decreaseProposalPriceAmount = function(categoryId, priceId, updateStorageCallback){

            $.each(cart.getCategory(categoryId).proposalPrices, function(i, proposalPrice){

                if(proposalPrice.id == priceId){

                    if(proposalPrice.amount <= 1){

                        cart.removeProposalPrice(categoryId, priceId);

                    } else {

                        proposalPrice.amount--;

                    }

                }

            });

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        /**
         * Remove proposal price from cart
         */
        cart.removeProposalPrice = function(categoryId, priceId, updateStorageCallback){

            if(cart.hasProposalPrice(categoryId, priceId)){

                var category = cart.getCategory(categoryId);

                category.proposalPrices = $.grep(category.proposalPrices, function(price){
                    return (price && price.id != priceId);
                });

            }

            cart.updateStorage(updateStorageCallback);

            return cart;

        };

        /**
         * Remove all prices of category in cart
         */
        cart.removeCategoryPrices = function(categoryId, updateStorageCallback){

            var category = cart.getCategory(categoryId);
            if(category){

                category.proposalPrices = [];

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

        cart.initStorage();

    };

    $.shopCart = (function(){
        return new ShopCart();
    })();

    $(document).on('click', '.add-to-cart', function(){

        var $btn = $(this);

        var proposalCartData = $btn.data('cart');
        if(proposalCartData){

            var categoryId = proposalCartData['categoryId'],
                proposalId = proposalCartData['proposalId'],
                priceId = proposalCartData['priceId'];

            if($btn.hasClass('remove-other')){
                $.shopCart.removeCategoryPrices(categoryId);
            }

            $.shopCart.addProposalPrice(categoryId, proposalId, priceId, function(categories){

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

        return false;

    });

    $(document).on('click', '.remove-from-cart', function(){

        var $btn = $(this);

        var proposalCartData = $btn.data('cart');
        if(proposalCartData){

            var categoryId = proposalCartData['categoryId'],
                priceId = proposalCartData['priceId'];

            $.shopCart.removeProposalPrice(categoryId, priceId, function(categories){

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

        return false;

    });

    $(document).on('click', '.increment-amount-in-cart', function(){

        var $btn = $(this);

        var proposalCartData = $btn.data('cart');
        if(proposalCartData){

            var categoryId = proposalCartData['categoryId'],
                proposalId = proposalCartData['proposalId'],
                priceId = proposalCartData['priceId'];

            $.shopCart.incrementProposalPriceAmount(categoryId, proposalId, priceId, function(categories){

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

        return false;

    });

    $(document).on('click', '.open-shop-cart', function(){

        var $btn = $(this);

        var popupUrl = $btn.attr('href');
        $.shopCart.openPopup({
            popupUrl: popupUrl
        });

        return false;

    });

    $(document).on('click', '.decrease-amount-in-cart', function(){

        var $btn = $(this);

        var proposalCartData = $btn.data('cart');
        if(proposalCartData){

            var categoryId = proposalCartData['categoryId'],
                priceId = proposalCartData['priceId'];

            $.shopCart.decreaseProposalPriceAmount(categoryId, priceId, function(categories){

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

        return false;

    });

});