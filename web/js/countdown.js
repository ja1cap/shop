
function CountdownTimer(elm,tl,mes){
 this.initialize.apply(this,arguments);
}

CountdownTimer.prototype={

 initialize:function(elm,tl,mes) {

  this.elem = elm;
  this.tl = tl;
  this.mes = mes;

 },countDown:function(){

    var tid;
  var timer='';
  var today=new Date();
  var day=Math.floor((this.tl-today)/(24*60*60*1000));
  var hour=Math.floor(((this.tl-today)%(24*60*60*1000))/(60*60*1000));
  var min=Math.floor(((this.tl-today)%(24*60*60*1000))/(60*1000))%60;
  var sec=Math.floor(((this.tl-today)%(24*60*60*1000))/1000)%60%60;
  var me=this;

  if( ( this.tl - today ) > 0 ){

   timer += '<span class="number-wrapper"><div class="caption">дней</div><div class="line"></div><span class="number day">'+this.addZero(day)+'</span><span class="delimiter">:</span></span>';
   timer += '<span class="number-wrapper"><div class="caption">часов</div><div class="line"></div><span class="number hour">'+this.addZero(hour)+'</span><span class="delimiter">:</span></span>';
   timer += '<span class="number-wrapper"><div class="caption">минут</div><div class="line"></div><span class="number min">'+this.addZero(min)+'</span><span class="delimiter">:</span></span><span class="number-wrapper"><div class="caption">секунд</div><div class="line"></div><span class="number sec">'+this.addZero(sec)+'</span></span>';
   this.elem.html(timer);
   tid = setTimeout( function(){me.countDown();},10 );

  }else{

   this.elem.html(this.mes);
   return;

  }

 },addZero:function(num){ return ('0'+num).slice(-2); }

};

$(function(){


    $.fn.countDown = function(){

        this.each(function(){

            var date = new Date($(this).data('date'));

            var timer = new CountdownTimer($(this), date);

            timer.countDown();

        });

    }

});

/*
var i = 0;
var widthListItems = $('.Shirina').find('li').not('.firstLi');
var length = [190, 195, 200];
var prices = [];

function parsePrice(){

    var widthListItem = widthListItems.eq(i);

    if(widthListItem.length > 0){

        widthListItem.find('a').trigger('click').trigger('click');

        setTimeout(function(){

            var price = parseFloat($('.product_block_text').find('h3 span:eq(0) b').text().split(' ').join(''));
            if(price && price != 'NaN' && price != undefined){

                var width = parseFloat(widthListItem.find('a').text().replace('Ширина ', '').replace(' см', ''));

                if(width != 158){

                    $.each(length, function(i, value){

                        prices.push({
                            size : width + 'x' + value,
                            value : price
                        });

                    });

                }

            }

            i++;
            parsePrice();

        }, 300);

    } else {

        console.log(JSON.stringify(prices));

    }

}

parsePrice();
*/

/*
var option_i = 0;
var m_size = $('#size');
var prices = [];

function parsePrice(){

    var option = m_size.find('option').eq(option_i);
    if(option.length > 0){

        var option_value = option.attr('value');
        var size = option.text().replace(' на ', 'x').replace(' см', '');

        m_size.val(option_value).trigger('change');

        setTimeout(function(){

            var sell_price = $('#actual_cost').text();

            if(sell_price != 'Размер отсутствует'){

                var price = parseFloat(sell_price.split(' ').join(''));

                prices.push({
                    size :size,
                    value : price
                });

            }

            option_i++;
            parsePrice();

        }, 100);

    } else {

        console.log(JSON.stringify(prices));

    }

}

parsePrice();
*/
