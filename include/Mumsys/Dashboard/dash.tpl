{literal}
<style type="text/css">
  * { box-sizing: border-box; }

  body {
    font-family: sans-serif;
  }

  /* ---- button ---- */

  .button {
    display: inline-block;
    padding: 0.5em 1.0em;
    margin-bottom: 10px;
    background: #EEE;
    border: none;
    border-radius: 7px;
    background-image: linear-gradient( to bottom, hsla(0, 0%, 0%, 0), hsla(0, 0%, 0%, 0.2) );
    color: #222;
    font-family: sans-serif;
    font-size: 16px;
    cursor: pointer;
  }

  .button:hover {
    background-color: #8CF;
    color: #222;
  }

  .button:active,
  .button.is-checked {
    background-color: #28F;
  }

  .button.is-checked {
    color: white;
  }

  .button:active {
    box-shadow: inset 0 1px 10px hsla(0, 0%, 0%, 0.8);
  }

  /* ---- button-group ---- */

  .button-group:after {
    content: '';
    display: block;
    clear: both;
  }

  .button-group .button {
    float: left;
    border-radius: 0;
    margin-left: 0;
    margin-right: 1px;
  }

  .button-group .button:first-child { border-radius: 0.5em 0 0 0.5em; }
  .button-group .button:last-child { border-radius: 0 0.5em 0.5em 0; }

  /* ---- grid ---- */

  .grid {
    /*border: 1px solid #333;*/
    max-width: 100%;
  }

  /* clear fix */
  .grid:after {
    content: '';
    display: block;
    clear: both;
  }

  /* ---- .bus-data ---- */

  .bus-data {
    position: relative;
    float: left;
    width: 150px;
    height: 120px;
    margin: 5px;
    padding: 10px;
    background: #888;
    color: #262524;
    cursor: pointer;
     -moz-user-select: -moz-none;
    -khtml-user-select: none;
    -webkit-user-select: none;
    -o-user-select: none;
    user-select: none;
  }

  .bus-data > * {
    margin: 0;
    padding: 0;
  }

  .bus-data .symbol {
    position: absolute;
    right: 5px;
    top: 65px;
    text-transform: none;
    letter-spacing: 0;
    font-size: 12px;
    font-weight: normal;
    color: white;
  }

  .bus-data .locate{
    color: white;
    position: absolute;
    top: 45px;
    right: 5px;
    text-transform: none;
    letter-spacing: 0;
    font-size: 12px;
    font-weight: bold;
  }

  .bus-data .name {
    position: absolute;
    left: 5px;
    top: 0px;
    font-size: 42px;
    font-weight: bold;
    color: white;
  }

  .bus-data .number {
    position: absolute;
    right: 5px;
    top: 5px;
  }

  .bus-data .alertinfo {
    position: absolute;
    left: 5px;
    top:46px;
    font-size: 10px;
    color: white;
     text-shadow: 1px 1px 1px #000;
  }

  .crit{ background: #F00; background: hsl(   0, 100%, 50%); }/*red*/
  .safe{ background: #88b917;}/*green*/
  .warn{ background-color: gold; }/*yellow*/
  .warn2{ background: #F80; background: hsl(  36, 100%, 50%); }/*orange*/
  .type{font-size: 0;}
  .number{
    background-color: white;
    padding:2px 5px 2px 5px;
    border-radius:20px;
  }
  .now{
    background-color: black;
    padding: 0px 4px;
    border-radius:20px;
  }
  .prev{
    background-color: yellow;
    padding: 0px 4px;
    border-radius:20px;
    color:black;
  }
  .changed{
    font-size: 0;
  }
</style>

<script type="text/javascript" src="include/common/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="include/common/javascript/ajaxreq.js"></script>
<script type="text/javascript" src="http://momentjs.com/downloads/moment.min.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function(){

// init Isotope
var $grid = jQuery('#grid').isotope({
  itemSelector: '.bus-data',
  layoutMode: 'fitRows',
  getSortData: {
    name: '.name',
    symbol: '.symbol',
    type: '.type',
    changed: '.changed parseInt'/*,
    category: '[data-category]',
    weight: function( itemElem ) {
      var weight = jQuery( itemElem ).find('.weight').text();
      return parseFloat( weight.replace( /[\(\)]/g, '') );
    }*/
  },sortBy:['type', 'changed'],
});

var contains = function(needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if(!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                var item = this[i];

                if((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle) > -1;
};

function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);

    if (interval > 1) {
        return interval + " years";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " months";
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + " days";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " hours";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " minutes";
    }
    return Math.floor(seconds) + " seconds";
}



function update(datetime) {
    var theevent = new Date(datetime);
    now = new Date();
    var sec_num = (theevent-now) / 1000;
    var days    = Math.floor(sec_num / (3600 * 24));
    var hours   = Math.floor((sec_num - (days * (3600 * 24)))/3600);
    var minutes = Math.floor((sec_num - (days * (3600 * 24)) - (hours * 3600)) / 60);
    var seconds = Math.floor(sec_num - (days * (3600 * 24)) - (hours * 3600) - (minutes * 60));

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}

    return  hours+':'+minutes+':'+seconds;
}


 function renderElement(ajaxResponse,init) {

    // turn string response to JSON array
    var responseArray = JSON.parse(ajaxResponse);
    var elems = $grid.isotope('getItemElements');
     
    // make sure there is a response
    if (responseArray.length > 0) {
          var container = document.getElementById("grid");
          if(init==1){
                // get container
                // iterate over each response
                for (var i = 0; i < responseArray.length; i += 1) {
                  var $item =jQuery('<div id="'+responseArray[i].name+'" class="bus-data '+responseArray[i].name+' '+responseArray[i].type+' "></div>');
                   $item.append('<h3 class="name">'+responseArray[i].name+'</h3>');
                   $item.append('<p class="symbol">'+responseArray[i].duration+'</p>');
                   $item.append('<p class="number">'+responseArray[i].alertcnt+'</p>');
                   $item.append('<p class="locate">'+responseArray[i].description+'</p>');
                   $item.append('<p class="type">'+responseArray[i].type+'</p>');
                   $item.append('<p class="changed">'+responseArray[i].changed+'</p>');
                   $item.append('<p><table class="alertinfo"><td>Record :</td><td class="prev">'+responseArray[i].prevrecord+'</td><td><span class="now">'+responseArray[i].record+'</span></td><tr><td>Nvs-off :</td><td class="prev">'+responseArray[i].prevnvs+'</td><td><span class="now">'+responseArray[i].nvs+'</span></td><tr><td>SSD :</td><td class="prev">'+responseArray[i].prevssd+'</td><td><span class="now">'+responseArray[i].ssd+'</span><td><tr><td>Pfttemp :</td><td class="prev">'+responseArray[i].prevtemp+'</td><td><span class="now">'+responseArray[i].pfttemp+'</span></td><tr><td>Pftlux :</td><td class="prev">'+responseArray[i].prevlux+'</td><td><span class="now">'+responseArray[i].pftlux+'</span></td></table></p>');
                   $grid.append($item).isotope( 'appended', $item );
                 // console.log(responseArray[i].name);
                /*
                        // create the elems needed
                        var element = document.createElement("div");
                        element.className = 'bus-data '+responseArray[i].name+' '+responseArray[i].prior+'" data-category="'+responseArray[i].cat+'"';
            
                        var name = document.createElement("h3");
                        name.className = "name";
                        name.innerHTML = responseArray[i].name;

                        var symbol = document.createElement("p");
                        symbol.className = "symbol";
                        symbol.innerHTML = responseArray[i].symbol;

                        var number= document.createElement("p");
                        number.className = "number";
                        number.innerHTML = responseArray[i].number;

                        var weight = document.createElement("p");
                        weight.className = "weight";
                        weight.innerHTML = responseArray[i].weight;
            
                        // append them all to player wrapper
                        element.appendChild(name);
                        element.appendChild(symbol);
                        element.appendChild(number);
                        element.appendChild(weight);

                        // append player to container
                        container.appendChild(element);*/
                }
          }else{

                  var localbus = [];
                  jQuery.each(elems, function(index, value) {
                    var idattrib = value.getAttribute('id');
                    localbus.push(idattrib);
                  });
                  var remotebus=[];
                  for (var i = 0; i < responseArray.length; i += 1) {
                    remotebus.push(responseArray[i].name);
                  }
                   for (var i = 0; i < localbus.length; i += 1) {
                      localchk = contains.call(remotebus, localbus[i]);
                      if(localchk==false){
                      var $removeItem = jQuery('.'+localbus[i]);
                        $grid.isotope('remove',$removeItem).isotope('layout');
                      }
                   }
                      //check is data server exist in local
                      for (var i = 0; i < responseArray.length; i += 1) {
                      index = contains.call(localbus, responseArray[i].name);
                      if(index==true){
                                  var $items = $grid.find('.bus-data');
                                    $items.each( function() {
                                      moment.createFromInputFallback = function(config) {
                                        config._d = new Date(config._i);
                                      };
                                      datenow = moment(responseArray[i].curtime).format("YYYY/MM/DD HH:mm:ss");
                                      lastcheck = moment(responseArray[i].lastchange).format("YYYY/MM/DD HH:mm:ss");
                                      var diffTime = moment(datenow).diff(lastcheck);
                                      var duration = moment.duration(diffTime);
                                      var years = duration.years(),
                                          days = duration.days(),
                                          hrs = duration.hours(),
                                        mins = duration.minutes(),
                                        secs = duration.seconds();
                                      jQuery('.'+responseArray[i].name).find('.symbol').text(responseArray[i].duration);
                                    });
                                    // delay a bit, just to see new numbers
                                    setTimeout( function() {
                                      // update sort data and re-sort
                                      $grid.isotope('updateSortData').isotope();
                                    }, 500 );
                        //  var $removeItem = jQuery('.'+responseArray[i].name);
                       //  $grid.isotope('remove',$removeItem).isotope('layout');
                       //  var $item =jQuery('<div id="'+responseArray[i].name+'" class="bus-data '+responseArray[i].name+'"></div>');
                       //  $item.append('<h3 class="name">'+responseArray[i].name+'</h3>');
                       //  $grid.append($item).isotope( 'appended', $item );
                      }else{
                        var $item =jQuery('<div id="'+responseArray[i].name+'" class="bus-data '+responseArray[i].name+' '+responseArray[i].type+' "></div>');
                       $item.append('<h3 class="name">'+responseArray[i].name+'</h3>');
                       $item.append('<p class="symbol">'+responseArray[i].duration+'</p>');
                       $item.append('<p class="number">'+responseArray[i].alertcnt+'</p>');
                       $item.append('<p class="locate">'+responseArray[i].description+'</p>');
                       $item.append('<p class="type">'+responseArray[i].type+'</p>');
                       $item.append('<p class="changed">'+responseArray[i].changed+'</p>');
                       $item.append('<p><table class="alertinfo"><td>Record :</td><td class="prev">'+responseArray[i].prevrecord+'</td><td><span class="now">'+responseArray[i].record+'</span></td><tr><td>Nvs-off :</td><td class="prev">'+responseArray[i].prevnvs+'</td><td><span class="now">'+responseArray[i].nvs+'</span></td><tr><td>SSD :</td><td class="prev">'+responseArray[i].prevssd+'</td><td><span class="now">'+responseArray[i].ssd+'</span><td><tr><td>Pfttemp :</td><td class="prev">'+responseArray[i].prevtemp+'</td><td><span class="now">'+responseArray[i].pfttemp+'</span></td><tr><td>Pftlux :</td><td class="prev">'+responseArray[i].prevlux+'</td><td><span class="now">'+responseArray[i].pftlux+'</span></td></table></p>');
                        $grid.append($item).isotope( 'appended', $item );
                      }
                    }
          }
    }else{

      var localbus = [];
      jQuery.each(elems, function(index, value) {
        var idattrib = value.getAttribute('id');
        localbus.push(idattrib);
      });
      for (var i = 0; i < localbus.length; i += 1) {
        var $removeItem = jQuery('.'+localbus[i]);
          $grid.isotope('remove',$removeItem).isotope('layout');
      }
      console.log("zonk "+localbus.length);

    }
}


// bind sort button click
jQuery('.sort-by-button-group').on( 'click', 'button', function() {
  var sortValue = jQuery(this).attr('data-sort-value');
  $grid.isotope({ sortBy: sortValue });
});

// change is-checked class on buttons
jQuery('.button-group').each( function( i, buttonGroup ) {
  var $buttonGroup = jQuery( buttonGroup );
  $buttonGroup.on( 'click', 'button', function() {
    $buttonGroup.find('.is-checked').removeClass('is-checked');
    jQuery( this ).addClass('is-checked');
  });
});


$grid.on( 'removeComplete', function( event, laidOutItems ) {
  console.log( 'removeComplete with ' + laidOutItems.length + ' items' );
});

jQuery('.append-button').on( 'click', function() {
  // create new item elements
  //var $items = getItemElement().add( getItemElement() ).add( getItemElement() );
  // append elements to container
  //$grid.append( $items ).isotope( 'appended', $items );
  var weson='[{"name":"3320","clean":"nNVS_1_nok,nNVS_2_nok,nnNVS_4_no","cnt":"3","lastcheck":"2016-10-06 11:00:25","lastchange":"2016-10-06 11:00:25","lasthard":"2016-10-06 05:13:45"},{"name":"5396","clean":"cam103_failed-conn2srv_nok","cnt":"1","lastcheck":"2016-10-06 11:00:34","lastchange":"2016-10-06 10:58:40","lasthard":"2016-10-06 07:01:24"},{"name":"5873","clean":"cam104_failed-conn2srv_nok,ipcam_104_nok","cnt":"2","lastcheck":"2016-10-06 11:01:04","lastchange":"2016-10-06 09:43:35","lasthard":"2016-10-06 09:43:35"},{"name":"3333","clean":"cam104_failed-conn2srv_nok,ipcam_104_nok","cnt":"2","lastcheck":"2016-10-06 11:01:04","lastchange":"2016-10-06 09:43:35","lasthard":"2016-10-06 09:43:35"}]';
  renderElement(weson,0);
  });


// make <div class="grid-item grid-item--width# grid-item--height#" />
function getItemElement() {
  var $item =jQuery('<div class="bus-data "></div>');
  // add width and height class
  var wRand = Math.random();
  var hRand = Math.random();
  var widthClass ='';
  $item.addClass(widthClass);
  $item.append('<h3 class="name">'+wRand+'</h3>');
  return $item;
}

$grid.on( 'click', '.bus-data', function() {
  window.location.href = "http://{/literal}{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}{literal}&busid="+this.getAttribute('id');
  //console.log("http://{/literal}{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}{literal}&busid="+this.getAttribute('id'));
   //console.log(jQuery('.5123'));
          // remove clicked element
         // $grid.isotope( 'remove', this )
          // layout remaining item elements
        //  .isotope('layout');
       //var $removeItem = jQuery('.5123');
        //$grid.isotope('remove',jQuery('.grid')).isotope('layout');
       //  console.log(jQuery('.grid').isotope());
        });

//initial load
 jQuery.ajax({
                 url:"/centreon/lib/jsondashboard.php",
                 success:function(json){
                  renderElement(json,1);
                 },
                 error:function(){
                     //alert("Error");
                 }      
            });

//interval
setInterval(function () {
            jQuery.ajax({
                 url:"/centreon/lib/jsondashboard.php",
                 success:function(json){
                  renderElement(json,0);
                 },
                 error:function(){
                  //   alert("Error");
                 }      
            });
}, 4000);
//});


/*


setInterval(function () {
$grid.isotope({ sortBy: 'name' });
  setInterval(function () {
  $grid.isotope({ sortBy: 'weight' });
  }, 2000);
}, 5000);


*/
});



</script>
{/literal}


<h1><center>Today Bus Alert on {$datenow}</center></h1>
{*
<p><button class="append-button">Append items</button></p>

<h1>Isotope - sorting</h1>

<div class="button-group sort-by-button-group">
  <button class="button is-checked" data-sort-value="original-order">original order</button>
  <button class="button" data-sort-value="name">name</button>
  <button class="button" data-sort-value="symbol">symbol</button>
  <button class="button" data-sort-value="number">number</button>
  <button class="button" data-sort-value="weight">weight</button>
  <button class="button" data-sort-value="category">category</button>
</div>
*}
<div id="grid" class="grid">
</div>

