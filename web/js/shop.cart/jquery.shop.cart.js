$(function(){

    var ShopCart = function (){

        var cart = this;
        var storageKey = 'shopCart';

        var categories = {};

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

        };

        /**
         * Update storage
         * @returns {{categories: Object}}
         */
        cart.updateStorage = function(){

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

//            console.log('Update cart storage');
//            console.log(data);

            $.cookie(storageKey, data, {
                path: '/'
            });

            return data;

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
         * @param categoryId
         * @param proposalId
         * @param priceId
         * @returns {ShopCart}
         */
        cart.addProposalPrice = function(categoryId, proposalId, priceId){

            if(!cart.hasProposalPrice(categoryId, priceId)){

                cart.getOrAddCategory(categoryId).proposalPrices.push({
                    id : priceId,
                    proposalId : proposalId,
                    categoryId : categoryId,
                    amount : 1
                });

                cart.updateStorage();

            }

            return cart;

        };

        /**
         * Remove proposal price from cart
         * @param categoryId
         * @param priceId
         * @returns {ShopCart}
         */
        cart.removeProposalPrice = function(categoryId, priceId){

            if(cart.hasProposalPrice(categoryId, priceId)){

                var category = cart.getCategory(categoryId);

                category.proposalPrices = $.grep(category.proposalPrices, function(price){
                    return (price && price.id != priceId);
                });

                cart.updateStorage();

            }

            return cart;

        };

        /**
         * Remove all prices of category in cart
         * @param categoryId
         * @returns {ShopCart}
         */
        cart.removeCategoryPrices = function(categoryId){

            var category = cart.getCategory(categoryId);
            if(category){

                category.proposalPrices = [];
                cart.updateStorage();

            }
            return cart;

        };

        cart.refreshCartSummary = function(){
            location.reload();
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

            $.shopCart.addProposalPrice(categoryId, proposalId, priceId);
            $.shopCart.refreshCartSummary();

        }

        return false;

    });

    $(document).on('click', '.remove-from-cart', function(){

        var $btn = $(this);

        var proposalCartData = $btn.data('cart');
        if(proposalCartData){

            var categoryId = proposalCartData['categoryId'],
                priceId = proposalCartData['priceId'];

            $.shopCart.removeProposalPrice(categoryId, priceId);
            $.shopCart.refreshCartSummary();

        }

        return false;

    });

});