<? print $_lib['sess']->doctype ?>
<head>
  <title>Empatix - Logging</title>
   <? includeinc('head') ?>
</head>

<style>
body
{
    text-align: center;
}
.row {
 width: 400px;
 margin: 0px auto;
 padding: 10px;
     margin-bottom: 5px;
 -moz-border-radius: 15px;
  border-radius: 15px;
}
.row h1 {
    font: 20px "helvetica";
 margin: 0;
 }
.row h2 {
 font: 25px "helvetica";
 margin: 0;
 color: black;
 }
a { color: black; }
</style>

<body>

<div id="body">

</div>

<script>

var data = <? include('log.json'); ?>;

var max_entries = 20;
var colors = [ "F22", "A33", "844", "655", "666", "565", "484", "3A3", "2F2" ];

function parse_ts(TS) {
    var s = TS.split(/(-| |:)/);
    var a = [];
    for(var i = 0; i < s.length; i += 2) {
        a[i/2] = s[i];
    }

    var date = new Date(a[0], a[1] - 1, a[2], a[3]);
    return date.getTime();
}

function rating(index)
{
    var array = data[index];

    var date = new Date();
    var now = date.getTime();

    var entries = array.length;

    if(entries <= 0)
        return 0;

    var first = parse_ts(array[entries - 1]['TS']);
    var diff = (now - first) / (60*60*24*1000);

    var rating = Math.min( (7 * entries) / (4 * diff ), 1.0 );
    var percent = Math.round( rating * 100 );
    
    return percent;
}

function asc_sort(a, b)
{
    return rating(b) - rating(a);
}

var keys = []
for(k in data)
{
    keys.push(k);
}

var data_keys = keys.sort(asc_sort);

$(document).ready(
    function() {
        for(d in data_keys) {
            (function(index) {

                var array = data[index];

                //entries = 20;
                //diff = 70;

                // (n / (60*60*24* d )) * 151200 = (7*n) / (4*d)
                var percent = rating(index);
                
                var color = colors[ Math.round( (colors.length - 1) * percent / 100) ];

                var row = $('<div>')
                    .addClass('row')
                    .css({'backgroundColor': '#' + color})
                    .html('<h1>' + index + '</h1><h2>'+percent+'%</h2>');


                var toggle = false;
                var hide_button = $('<a>')
                    .text('hide')
                    .attr({'href': '#'})
                    .click(function(){
                            if(confirm('Are you sure you want to hide this item?'))
                            {
                                row.remove();
                            }
                        });
                var inner_row = $('<div>')
                    .text('more info')
                    .css({'cursor': 'pointer', 'margin': '5px'})
                    .click(function() {
                            if(toggle == false)
                            {
                                var str = ''
                                for(l in array)
                                {
                                    str += array[l]['TS'] + '<br />';
                                }
                                inner_row.html(str + '<br />hide info');
                            }
                            else
                            {
                                inner_row.text('more info');
                            }

                            toggle = !toggle;
                        });
                
                row
                    .append(hide_button)
                    .append(inner_row);
                    
                
                $('body').append(row);
            })(data_keys[d]);
        }
    }
);

</script>

<!-- 
<div style="background-color: #F22; width: 100px; height: 20px;"></div>
<div style="background-color: #A33; width: 100px; height: 20px;"></div>
<div style="background-color: #844; width: 100px; height: 20px;"></div>
<div style="background-color: #655; width: 100px; height: 20px;"></div>
<div style="background-color: #666; width: 100px; height: 20px;"></div>
<div style="background-color: #565; width: 100px; height: 20px;"></div>
<div style="background-color: #484; width: 100px; height: 20px;"></div>
<div style="background-color: #3A3; width: 100px; height: 20px;"></div>
<div style="background-color: #2F2; width: 100px; height: 20px;"></div>

-->




</body>
