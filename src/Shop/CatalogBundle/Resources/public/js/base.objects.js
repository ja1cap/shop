$(function(){

    $.ProposalPrice = Class.extend(function(){

        var proposalPrice = this;

        proposalPrice.category = null;

        proposalPrice.data = {
            id : null,
            priceId : null,
            proposalId : null,
            categoryId : null
        };

        proposalPrice.constructor = function(data){

            proposalPrice.data.id = (data.hasOwnProperty('priceId') ? data['priceId'] : data['id']);
            proposalPrice.data = $.extend(proposalPrice.data, data, true);

        };

        proposalPrice.setCategory = function(category){
            proposalPrice.category = category;
        };

        proposalPrice.getCategory = function(){
            return proposalPrice.category;
        };

        proposalPrice.toJSON = function () {
            return proposalPrice.data;
        };

        return proposalPrice;

    });

    $.Category = Class.extend(function(){

        var category = this;

        category.data = {
            id : null,
            proposalPrices : []
        };

        category.constructor = function(data){

            if(data){

                category.data = $.extend(category.data, data, true);
                category.data.proposalPrices = [];

                if(data['proposalPrices'] && $.isArray(data['proposalPrices'])){

                    $.each(data['proposalPrices'], function(i, proposalPrice){

                        category.addProposalPrice(proposalPrice);

                    });

                }

            }

        };

        /**
         * Get category proposal price
         * @param priceId
         * @returns {$.ProposalPrice|null}
         */
        category.getProposalPrice = function(priceId){

            var proposalPrice = null;

            if($.isArray(category.data.proposalPrices)){

                $.each(category.data.proposalPrices, function(i, price){

                    if(price && price.data.id == priceId){
                        proposalPrice = price;
                        return false;
                    }

                    return true;

                });

            }

            return proposalPrice;

        };

        /**
         *
         * @returns {*|.data.proposalPrices|Array}
         */
        category.getProposalPrices = function(){
            return category.data.proposalPrices;
        };

        /**
         * Get proposal price index
         * @param priceId
         * @returns {Number}
         */
        category.getProposalPriceIndex = function(priceId){

            var index = -1;

            if($.isArray(category.data.proposalPrices)){

                $.each(category.data.proposalPrices, function(i, price){

                    if(price && price.data.id == priceId){
                        index = i;
                        return false;
                    }

                    return true;

                });

            }

            return index;

        };

        /**
         * Check if proposal price exists in category
         * @param priceId
         * @returns {boolean}
         */
        category.hasProposalPrice = function(priceId){
            return (category.getProposalPriceIndex(priceId) >= 0);
        };

        /**
         * Create ShopCartProposalPrice instance
         * @param proposalPriceData
         * @returns {$.ProposalPrice}
         */
        category.createProposalPrice = function(proposalPriceData){
            return proposalPriceData ? new $.ProposalPrice(proposalPriceData) : null;
        };

        /**
         * Add proposal price to category
         * @param shopCartProposalPrice
         * @returns {number}
         */
        category.addProposalPrice = function(shopCartProposalPrice){

            var index = -1;

            if(!(shopCartProposalPrice instanceof $.ProposalPrice)){
                shopCartProposalPrice = category.createProposalPrice(shopCartProposalPrice);
            }

            if(shopCartProposalPrice instanceof $.ProposalPrice && $.isArray(category.data.proposalPrices)){

                var currentIndex = category.getProposalPriceIndex(shopCartProposalPrice.data.id);
                if(currentIndex >= 0){

                    index = currentIndex;

                } else {

                    shopCartProposalPrice.setCategory(category);

                    var proposalPricesAmount = category.data.proposalPrices.push(shopCartProposalPrice);
                    index = (proposalPricesAmount - 1);

                }

            }

            return index;

        };

        /**
         * Remove proposal price from category
         * @param priceId
         * @returns {$.ProposalPrice}
         */
        category.removeProposalPrice = function(priceId){

            category.data.proposalPrices = $.grep(category.data.proposalPrices, function(price){
                return (price && price.data.id != priceId);
            });

            return category;

        };

        /**
         * Remove all proposal price from category
         * @returns {$.ProposalPrice}
         */
        category.removeAllProposalPrices = function(){
            category.data.proposalPrices = [];
            return category;
        };

        /**
         * Get category proposal prices amount
         * @returns {Number}
         */
        category.getProposalPricesAmount = function(){
            return category.data.proposalPrices.length;
        };

        category.toJSON = function () {

            var proposalPricesJSON = $.map(category.data.proposalPrices, function(proposalPrice){
                return proposalPrice.toJSON();
            });

            return $.extend(
                {},
                category.data,
                {
                    proposalPrices : proposalPricesJSON
                },
                true
            );

        };

        return category;

    });

    $.Catalog = Class.extend(function(){

        var catalog = this;
        var storage = null;
        var categories = [];

        catalog.constructor = function(storageKey){
            catalog.initStorage(storageKey);
        };

        /**
         * Create storage
         * @param storageKey
         * @param callback
         * @returns {$.BaseStorage}
         */
        catalog.createStorage = function(storageKey, callback){
            return new $.BaseStorage(storageKey, callback);
        };

        /**
         * Init catalog storage
         */
        catalog.initStorage = function(storageKey){

            catalog.createStorage(storageKey, function(_storage){

                storage = _storage;

                var storageData = storage.getData();
                if(storageData){

                    if($.isArray(storageData['categories'])){

                        var categoriesData = storageData['categories'];

                        $.each(categoriesData, function(i, categoryData){

                            var category = catalog.createCategory(categoryData);
                            if(category instanceof $.Category){
                                catalog.addCategory(category);
                            }

                        });

                    }

                }

                catalog.updateStorage();

            });

        };

        /**
         * Update storage
         * @returns {{categories: Object}}
         */
        catalog.updateStorage = function(callback){

            var usedCategories = [];

            $.each(categories, function(i, category){

                var isUsed = catalog.checkCategoryUsage(category);
                if(isUsed){
                    usedCategories.push(category);
                }

            });

            categories = usedCategories;

            var data = {
                categories : []
            };

            $.each(categories, function(i, category){
                data.categories.push(category.toJSON());
            });

            if(storage){

                storage
                    .setData(data)
                    .save({}, function(data){

                        if(callback && typeof callback == 'function'){
                            callback(data.categories);
                        }

                    })
                ;

            } else {

                if(callback && typeof callback == 'function'){
                    callback(data.categories);
                }

            }

            return data;

        };

        /**
         * Get catalog categories
         * @returns {[]}
         */
        catalog.getCategories = function(){
            return categories;
        };

        /**
         * Create catalog category
         * @returns {$.Category}
         */
        catalog.createCategory = function(categoryData){
            return (categoryData ? new $.Category(categoryData) : null);
        };

        /**
         * Get catalog category
         * @param categoryId
         * @returns {$.Category|null}
         */
        catalog.getCategory = function(categoryId){

            var category = null;

            $.each(categories, function(i, _category){
                if(_category.data.id == categoryId){
                    category = _category;
                    return false;
                }
                return true;
            });

            return category;

        };

        catalog._moveCategoryToEnd = function(categoryId){

            $.each(categories, function(i, _category){
                if(_category.data.id == categoryId){
                    categories.splice(i, 1);
                    categories.push(_category);
                    return false;
                }
                return true;
            });

            return catalog;

        };

        /**
         * Add category to catalog
         * @param category
         * @returns {$.Catalog}
         */
        catalog.addCategory = function(category){
            if(category instanceof $.Category){
                categories.push(category);
            }
            return catalog;
        };

        /**
         * Get category if it exists or add new category to catalog
         * @param categoryId
         * @returns {*}
         */
        catalog.getOrAddCategory = function(categoryId){

            var category = catalog.getCategory(categoryId);

            if(!(category instanceof $.Category)){

                category = catalog.createCategory({
                    id : categoryId
                });

                if(category instanceof $.Category){
                    catalog.addCategory(category);
                }

            }

            return category;

        };

        /**
         * Remove category from catalog
         * @param categoryId
         * @returns {$.Catalog}
         */
        catalog.removeCategory = function(categoryId){

            $.each(categories, function(i, _category){

                if(_category.data.id == categoryId){

                    categories.splice(i, 1);
                    return false;

                }

                return true;

            });

            catalog.updateStorage();

            return catalog;

        };

        /**
         * Check if category is used, if not used remove from catalog
         * @param category
         * @returns boolean
         */
        catalog.checkCategoryUsage = function(category){

            var isUsed = false;

            if((category instanceof $.Category) && category.getProposalPrices().length > 0){
                isUsed = true;
            }

            return isUsed;

        };

        /**
         * Check if proposal price exists
         * @param proposalPriceData
         * @returns {boolean}
         */
        catalog.hasProposalPrice = function(proposalPriceData){

            var hasProposalPrice = false;

            var categoryId = proposalPriceData['categoryId'];
            var category = catalog.getCategory(categoryId);

            if(category instanceof $.Category){
                hasProposalPrice = (category.getProposalPrice(proposalPriceData['priceId']) != null);
            }

            return hasProposalPrice;

        };

        /**
         * Add proposal price to catalog
         */
        catalog.addProposalPrice = function(proposalPriceData, updateStorageCallback){

            var categoryId = proposalPriceData['categoryId'];
            var category = catalog.getOrAddCategory(categoryId);

            if(category instanceof $.Category){

                category.addProposalPrice(proposalPriceData);
                catalog._moveCategoryToEnd(categoryId);

            }

            catalog.updateStorage(updateStorageCallback);

            return catalog;

        };

        /**
         * Remove proposal price from catalog
         */
        catalog.removeProposalPrice = function(proposalPriceData, updateStorageCallback){

            var category = catalog.getCategory(proposalPriceData['categoryId']);

            if(category instanceof $.Category){
                category.removeProposalPrice(proposalPriceData['priceId']);
            }

            catalog.updateStorage(updateStorageCallback);

            return catalog;

        };

        /**
         * Remove all prices of category
         */
        catalog.removeAllCategoryProposalPrices = function(categoryId, updateStorageCallback){

            var category = catalog.getCategory(categoryId);

            if(category instanceof $.Category){
                category.removeAllProposalPrices();
            }

            catalog.updateStorage(updateStorageCallback);

            return catalog;

        };

        /**
         * Get catalog proposal prices total amount
         * @returns {number}
         */
        catalog.getProposalPricesAmount = function(){

            var amount = 0;
            var categories = catalog.getCategories();

            $.each(categories, function(i, category){
                amount += category.getProposalPricesAmount();
            });

            return amount;

        };

    });

});