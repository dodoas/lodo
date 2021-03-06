<? print $_lib['sess']->doctype ?>
<head>
  <title>Empatix - Logging</title>
  <?php
    includeinc('head');
    includelogic('log/log');

    $logger = new model_log_log();
    $logger->iter_all_db();
  ?>
</head>

<style>
body
{
    text-align: center;
}
.row {
 width: 500px;
 margin: 0px auto;
 padding: 10px;
     margin-bottom: 5px;
 -moz-border-radius: 15px;
  border-radius: 15px;
  text-align: left;
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

var colors = [ "F22", "A33", "844", "655", "666", "565", "484", "3A3", "2F2" ];

// parses timestamp string and returns it in miliseconds after EPOCH
// ignoring the minutes and seconds
function parse_ts(TS) {
    var s = TS.split(/(-| |:)/);
    var a = [];
    for(var i = 0; i < s.length; i += 2) {
        a[i/2] = s[i];
    }

    // month(the second param) is 0 for january and 11 for december, that is why
    // there is a '- 1' there
    var date = new Date(a[0], a[1] - 1, a[2], a[3]);
    return date.getTime();
}

// calculates a percentage of usage(?) by the following algorithm
// min(1.0, (7*entries)/(5*diff))
// entries = number of entries fetched from logusage table of a db, max 20
// diff = a diff in days since oldest entry in list
// example: today is 2016-02-17, we get a list of 20 last logins of which the
// oldest is 2016-01-09 at 23:00, diff in days is ~38.42 days,
// so the rating is calculated as 0.3643... or 36%
function rating(index)
{
    var array = data[index];

    var date = new Date();
    var now = date.getTime();

    // limited to 20 max by query in log class
    var entries = array.length;

    if(entries <= 0)
        return 0;

    var first = parse_ts(array[entries - 1]['TS']);
    // diff in days between the current day and first login in the list
    // timestamps are in miliseconds so 60*60*24*1000 = 1 day
    var diff = (now - first) / (60*60*24*1000);

    var rating = Math.min( (7 * entries) / (5 * diff ), 1.0 );
    var percent = Math.round( rating * 100 );

    return percent;
}

function desc_sort(a, b)
{
    if(data[a].length > 0 && data[b].length > 0) {
        var aDate = Date.parse(data[a][0]['TS'].replace(/-/g, '/'));
        var bDate = Date.parse(data[b][0]['TS'].replace(/-/g, '/'));
        return bDate - aDate;
    } else if(data[a].length <= 0 && data[b].length > 0) {
        return 1;
    } else if(data[a].length > 0 && data[b].length <= 0) {
        return -1;
    } else {
        return 0;
    }
    //return rating(b) - rating(a);
}

var keys = []
for(k in data)
{
    keys.push(k);
}

var data_keys = keys.sort(desc_sort);
var toggle = false;

$(document).ready(
    function() {
        for(d in data_keys) {
            (function(index) {

                var array = data[index];

                //entries = 20;
                //diff = 70;

                // (n / (60*60*24* d )) * 151200 = (7*n) / (5*d)
                var percent = rating(index);

                var color = colors[ Math.round( (colors.length - 1) * percent / 100) ];
                var row_text = '<b>' + index + '</b> - ' + data[index][0]['TS'] + ' - ' + data[index][0]['Email'] + ' - ' + data[index][0]['IPAdress'] + ' - ' + data[index][0]['SessionID'] + '<br />';

                var hide_button = $('<a>')
                    .text('skjul')
                    .attr({'href': '#'})
                    .click(function(){
                            if(confirm('Er du sikker på at du vil skjule dette?'))
                            {
                                row.remove();
                            }
                        });

                var row = $('<div>')
                    .addClass('row')
                    .css({'backgroundColor': '#' + color, 'cursor': 'pointer'})
                    .html(row_text)
                    .click(function() {
                            if(toggle == false)
                            {
                                var text = '<b>' + index + '</b><br />';
                                for(l in array)
                                {
                                    text += array[l]['TS'] + ' - ' + array[l]['Email'] + ' - ' + array[l]['IPAdress'] + ' - ' + array[l]['SessionID'] + '<br />';
                                }
                                row.html(text);
                                row.append(hide_button);
                            }
                            else
                            {
                                row.html(row_text);
                            }

                            toggle = !toggle;
                        });

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
