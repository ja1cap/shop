var weasty_geonames_city = null;

$(function(){

    var cityCookie = $.cookie(weasty_geonames_city_locator_cookie_name);

    if(cityCookie){

        if (cityCookie.indexOf('"') === 0) {
            // This is a quoted cookie as according to RFC2068, unescape...
            cityCookie = cityCookie.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }

        try {

            // Replace server-side written pluses with spaces.
            // If we can't decode the cookie, ignore it, it's unusable.
            // If we can't parse the cookie, ignore it, it's unusable.
            weasty_geonames_city = decodeURIComponent(cityCookie.replace(/\+/g, ' '));

            return JSON.parse(weasty_geonames_city);

        } catch(e) {}

    }

    if(!weasty_geonames_city && ymaps && weasty_geonames_city_locator_url){

        ymaps.ready(function(){

            if(ymaps.geolocation){

                $.ajax(weasty_geonames_city_locator_url, {
                    data : ymaps.geolocation,
                    success : function(city){

                        if(city){

                            weasty_geonames_city = city;
                            $.cookie(weasty_geonames_city_locator_cookie_name, JSON.stringify(weasty_geonames_city), {
                                path: '/'
                            });

                        }

                    }
                });

            }

        });

    }

});