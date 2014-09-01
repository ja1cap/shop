$(function () {

    var console = new $.Console();

    var ProposalEstimator = $.Catalog.extend(function () {

        var compare = this;

        /**
         * Create storage
         * @param storageKey
         * @param callback
         * @returns {$.CookieStorage}
         */
        compare.createStorage = function (storageKey, callback) {
            return new $.CookieStorage(storageKey, callback);
        };

    });

    $.proposalEstimator = new ProposalEstimator('proposalEstimator');

    $(document).on('click', '.compare-btn', function (e) {

        e.preventDefault();

        var $btn = $(this);

        var proposalPriceData = $btn.data('compare');
        if (proposalPriceData) {

            $.proposalEstimator.addProposalPrice(proposalPriceData, function (categories) {

                console.log(categories);
                //var popupUrl = $btn.attr('href');
                //$.proposalEstimator.refreshCartSummary({
                //    popup: (popupUrl != '#'),
                //    popupUrl: popupUrl,
                //    popupRequestData: {
                //        shopCart : {
                //            categories : categories
                //        }
                //    }
                //});

            });

        }

        return false;

    });

});
