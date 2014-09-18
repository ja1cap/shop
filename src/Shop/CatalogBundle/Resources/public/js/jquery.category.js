$(function(){

    var selectors = {
        tabsContainer : '#category-tabs-container',
        tabButton : '.category-tab-btn',
        tabFilter : '.category-tab-filter',
        resetFiltersBtn : '.reset-filters-btn',
        filtersForm : '#filtersForm',
        filtersContainer : '.category-filters-container',
        proposalsContainer : '.category-proposals-container',
        priceRangeSlider : '#prices-range-slider',
        priceRangeMinPriceField: '#minPrice',
        priceRangeMaxPriceField: '#maxPrice'
    };

    var CatalogCategory = function(categoryUrl, $categoryContainer){

        var category = this;

        category.url = categoryUrl;
        category.$categoryContainer = $categoryContainer;

        var methods = {
            initElements: function(){


                category.$tabsContainer = $(selectors.tabsContainer, $categoryContainer);
                category.$tabButton = $(selectors.tabButton, $categoryContainer);
                category.$tabFilter = $(selectors.tabFilter, $categoryContainer);
                category.$resetFiltersBtn = $(selectors.resetFiltersBtn, $categoryContainer);
                category.$filtersForm = $(selectors.filtersForm, $categoryContainer);
                category.$filtersContainer = $(selectors.filtersContainer, $categoryContainer);
                category.$proposalsContainer = $(selectors.proposalsContainer, $categoryContainer);
                category.priceRange = {
                    $minPriceField : $(selectors.priceRangeMinPriceField, $categoryContainer),
                    $maxPriceField : $(selectors.priceRangeMaxPriceField, $categoryContainer),
                    $priceFields : $([selectors.priceRangeMinPriceField, selectors.priceRangeMaxPriceField].join(','), $categoryContainer),
                    $slider : $(selectors.priceRangeSlider, $categoryContainer)
                };

            },
            updatePriceRangeFieldValues : function(minPrice, maxPrice){

                category.priceRange.$minPriceField.val(minPrice);
                category.priceRange.$maxPriceField.val(maxPrice);

                category.priceRange.$priceFields.currency({
                    region: 'BYR',
                    thousands: ' ',
                    decimal: '.',
                    decimals: 0,
                    hidePrefix: true,
                    hidePostfix: true
                });

            },
            initPriceRangeSlider : function(){

                if(category.priceRange.$slider.length > 0){

                    var sliderOptions = $.extend(category.priceRange.$slider.data('slider-options'), {
                        range: true,
                        slide: function(event, ui) {

                            methods.updatePriceRangeFieldValues(ui.values[0], ui.values[1]);

                        },
                        change: function() {

                            category.priceRange.$minPriceField.trigger('change');

                        }
                    });

                    category.priceRange.$slider.slider(sliderOptions);
                    methods.updatePriceRangeFieldValues(category.priceRange.$slider.slider("values", 0), category.priceRange.$slider.slider("values", 1));

                }

            },
            updateCategoryTabs : function(){

                var $activeTabButtons = [];
                var $allTabButton = null;

                category.$tabButton.each(function(){

                    var $tabButton = $(this);

                    if($tabButton.hasClass('all')){

                        $allTabButton = $tabButton;

                    } else {

                        var filterId = $tabButton.data('filter');
                        var $tabFilter = $('#' + filterId);

                        if($tabFilter.val()){
                            $activeTabButtons.push($tabButton);
                        }

                    }

                });

                category.$tabButton.removeClass('active');

                if($activeTabButtons.length == 0){

                    if($allTabButton){
                        $allTabButton.addClass('active');
                    }

                } else {

                    if($allTabButton){
                        $allTabButton.removeClass('active');
                    }

                    $.each($activeTabButtons, function(i, $tabButton){

                        $tabButton.addClass('active');

                    });

                }

            },
            initFilters: function(){

                methods.initPriceRangeSlider();

                methods.updateCategoryTabs();
                category.$tabButton.click(function(e){

                    e.preventDefault();

                    var $tabButton = $(this);
                    var filterId = $tabButton.data('filter');

                    category.$tabFilter.val([]);

                    switch (filterId){
                        case 'all':

                            category.$tabFilter.eq(0).trigger('change');
                            break;

                        default :

                            var $tabFilter = $('#' + filterId);

                            $tabFilter
                                .val(1)
                                .trigger('change')
                            ;

                    }

                });

                $(':input', category.$filtersForm).change(function(){

                    var data = category.$filtersForm.serializeArray();
                    category.loadPage(data);

                });

                category.$resetFiltersBtn.click(function(event){

                    event.preventDefault();

                    var data = {
                        reset_filters: 1
                    };
                    category.loadPage(data);

                    return false;

                });

            }
        };

        category.loadPageRequest = null;
        category.loadPage = function(data){

            if(category.loadPageRequest){
                category.loadPageRequest.abort();
            }

            if($.isArray(data)){

                data.push({
                    name: 'format',
                    value: 'json'
                });

            } else {

                data.format = 'json';

            }

            category.loadPageRequest = $.ajax({
                url: categoryUrl,
                data: data,
                beforeSend: function(){
                    category.$proposalsContainer.addLoading();
                },
                success: function(data){

                    category.$tabsContainer
                        .html(data['tabsHtml'])
                    ;

                    category.$proposalsContainer
                        .html(data['proposalsHtml'])
                    ;

                    category.$filtersContainer
                        .html(data['filtersHtml'])
                        .applyChosen()
                    ;

                    category.loadPageRequest = null;

                    category.init();
                    category.$proposalsContainer.removeLoading();

                    if($.proposalEstimator){
                        $.proposalEstimator.markComparisonButtons();
                    }

                    //@TODO restart images preview plugin

                }
            });

        };

        category.init = function(){

            methods.initElements();
            methods.initFilters();

        };

    };

    $.fn.catalogCategory = function(categoryUrl){

        var $categoryContainer = $(this);
        var catalogCategory = $categoryContainer.data('categoryCategory');

        if(!catalogCategory){
            catalogCategory = new CatalogCategory(categoryUrl, $categoryContainer);
        }

        return catalogCategory;

    };

});