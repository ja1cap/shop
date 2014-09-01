$(function(){

    $.BaseStorage = Class.extend(function(){

        var storage = this;
        storage.key = null;
        storage.data = {};

        storage.constructor = function(storageKey, callback){

            storage.key = storageKey;

            if(callback && typeof callback == 'function'){
                callback(storage);
            }

        };

        storage.getData = function(){
            return storage.data;
        };

        storage.setData = function(data){
            storage.data = data;
            return storage;
        };

        storage.save = function(options, callback){

            if(callback && typeof callback == 'function'){
                callback(storage.data);
            }

            return storage;

        };

        return storage;

    });

    $.CookieStorage = $.BaseStorage.extend(function(){

        var storage = this;

        storage.constructor = function(storageKey, callback){

            $.cookie.json = true;
            storage.data = $.cookie(storageKey);

            storage.super(storageKey, callback);

        };

        storage.save = function(options, callback){

            $.cookie(storage.key, storage.data, {
                path: '/'
            });

            storage.super.save(options, callback);

        };

        return storage;

    });

});