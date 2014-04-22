var weasty_geonames_city = null;

$(function(){

    var cityCookie = $.cookie('weasty_geonames_city');

    if(cityCookie){
        weasty_geonames_city = $.parseJSON(JSON.parse(cityCookie));
    }

    if(!weasty_geonames_city && ymaps && weasty_geonames_city_locator_url){

        ymaps.ready(function(){

            if(ymaps.geolocation){

                $.ajax(weasty_geonames_city_locator_url, {
                    data : ymaps.geolocation,
                    success : function(city){

                        if(city){

                            weasty_geonames_city = city;
                            $.cookie('weasty_geonames_city', JSON.stringify(weasty_geonames_city), {
                                path: '/'
                            });

                        }

                    }
                });

            }

        });

    }

});