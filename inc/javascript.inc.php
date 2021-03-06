<?
if($VoucherDate) {
  $_this_date = $VoucherDate;
}
else {
  $_this_date = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
}
?>

<script language="javascript1.1">
sent = false;

/*
 * This function gets a number and returns a string with that number formated
 * as amount with specified number of decimal places. It can be called with
 * only one argument and then the number of decimal places defaults to 2.
 * Called in journal/edit view.
 * Example:
 * var amount = toAmountString(123456.45, 4);
 * // variable amount is now '123 456,4500'
 */
function toAmountString(num, decimal_places) {
  // c = number of decimal places
  // d = decimal separator character
  // t = thousand separator character
  Number.prototype.formatMoney = function(c, d, t){
    var n = this;
    var c = isNaN(c = Math.abs(c)) ? 2 : c; // no need to pass c param, 2 is default
    var d = d == undefined ? "," : d; // no need to pass d param, the ',' is default
    var t = t == undefined ? " " : t; // no need to pass t param, the ' ' is default
    var s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
    var j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
  };
  return num.formatMoney();
}

/*
 * This function does the reverse of toAmountString(). It gets a string and
 * returns the value as a number.
 * Called in journal/edit view.
 * Example:
 * var amount = toNumber('10 333,01');
 * // variable amount is now 10333.01
 */
function toNumber(str) {
  str = str.replace( new RegExp(" ", "g"), "");
  str = str.replace( new RegExp(",", "g"), ".");
  return Number(str);
}

function place_cursor(formname, fromfieldname) {
  /* document.getElementById('voucher.VoucherDate').focus(); */
  /* document.forms[document.forms.length-4].elements['voucher.VoucherDate'].focus(); */
  document.forms[formname].elements[fromfieldname].focus();
}

function update_reference(element, formname, fromfieldname, tofieldname){
    var journalID = element.value;
    if(!journalID)
    {
        // You have to type journal number
        alert("Du må taste inn Bilagsnr");
    }
    //alert(journalID);
    //alert(formname +" "+ fromfieldname +" "+ tofieldname);
    //alert(document.forms[formname].elements[tofieldname].selectedIndex);
    document.forms[formname].elements[tofieldname].value = journalID;
}

function update_period(element, formname, fromfieldname, tofieldname){
   var date = element.value;
   if(!date)
   {
     // You have to type journal date
     alert("Du må taste inn bilagsdato");
   }
   /* var pattern = new RegExp("/(\\d+)-(\\d+)-(\\d+)/"); */
   var pattern = /(\d{4})-(\d{2})-(\d{2})/; /* YYYY-MM-DD */
   var result  = date.match(pattern);

    if(result != null)
    {
      var period = result[1] + "-" + result[2];
      place_period(period, date, formname, fromfieldname, tofieldname);
    }
    else
    {
       var pattern = /(\d+)-(\d+)/; /* DD-MM */
       var result  = date.match(pattern);

       if(result != null)
       {
         var year     = '<? print $_lib['date']->get_this_year($_this_date); ?>';
         var period   = year + "-" + result[2];
         var date_out = period + "-" + result[1];
         place_period(period, date_out, formname, fromfieldname, tofieldname);
       }
       else
       {
         var pattern = /(\d+)/; /* DD */
         var result  = date.match(pattern);

         if(result != null)
         {
            var period   = '<? print $_lib['date']->get_this_period($_this_date); ?>';
            var date_out = period + "-" + result[1];
            place_period(period, date_out, formname, fromfieldname, tofieldname);
         }
         else
         {
           // Date is malformed, example on correct date is: YYYY-MM-DD (ISO) - 2004-12-04, DD-MM - 04-12 , DD - 04
           alert('Dato er feilformatert, eksempel på riktig dato er: YYYY-MM-DD (ISO) - 2004-12-04, DD-MM - 04-12 , DD - 04');
         }
       }

      /*
      We convert the date, so we can not update this automatically
      alert('Dato er feilformatert, eksempel på riktig dato er: YYYY-MM-DD, 2004-04-04'); */
    }
}

function place_period(period, date, formname, fromfieldname, tofieldname)
{
      /* Find index based on this hash */
      var pattern = /(\d{4})-(\d{2})-(\d{2})/; /* YYYY-MM-DD */
      var result  = date.match(pattern);
      if(result != null)
      {
        if(result[2] <= 0 || result[2] > 12) {
          // Month is invalid
          alert("Måned er ikke gyldig: " + result[2]);
          date = '';
        }
        if(result[3] <= 0 || result[3] > 31) {
          // Day is invalid
          alert("Dag er ikke gyldig: " + result[3]);
          date = '';
        }
      }
      else
      {
        // Date is not valid converted
        alert("Dato er ikke gyldig konvertert")
      }

      var periodhash = new Array();
      <?
        if($_lib['sess']->get_person('AccessLevel') > 2)
        {
          $query = "select Period from accountperiod where (Status=2 or Status=3) order by Period desc";
        }
        else
        {
          $query = "select Period from accountperiod where Status=2 order by Period desc";
        }
        $result = $_lib['db']->db_query($query);
        $j = 0;
        while($row = $_lib['db']->db_fetch_object($result))
        {
           print "periodhash['$row->Period'] = '$j';\n";
           $j++;
        }
      ?>
//alert(date);
//alert(periodhash[period]);
//alert(formname + tofieldname + fromfieldname);
//alert(document.forms[formname].elements[tofieldname].selectedIndex);

      //int tmpvalue = periodhash[period];
      //tmpvalue++;
      //alert (tmpvalue);

      /* Place menu */
      if(periodhash[period] >= 0)
      {
        document.forms[formname].elements[tofieldname].selectedIndex = periodhash[period];
      }
      else
      {
          // The date is not in a valid period
        alert("Datoen er ikke i en gyldig periode");
      }
      document.forms[formname].elements[fromfieldname].value = date;
}

function toggle(node)
{
    var target = document.getElementById(node);
    if (target.style.display == "none")
    {
       target.style.display = "block";
    }
    else
    {
       target.style.display = "none";
    }
}

function findDirectChildByName(parent, childName) {
    var wrapper_children = parent.childNodes;

    for(var i = 0; i < wrapper_children.length; i++)
    {
        if (typeof(wrapper_children[i].name) == 'undefined') {
            continue;
        }

        if (wrapper_children[i].name == childName) {
            return wrapper_children[i];
        }
    }

    return false;
}

window.currency_rates = new Object();

/*
 * OnChange action for currency select field.
 * The first argument is the select element and the second argument is a string
 * with which we show and hide the valuta related fields for a new voucher.
 * Used in journal/edit view.
 */
function onCurrencyChange(selObj) {
    var currency = selObj.value;
    var parent = $(selObj).parents("tr")[0];

    currency_fields = $(parent).find(".currency_field");
    if (!(currency == "")) currency_fields.show();
    else currency_fields.hide();

    if (currency == "") {
        rate = 0;
    } else {
        rate = window.currency_rates[currency];
    }

    var currency_rate_input = $(parent).find("input.currency_rate")[0];

    currency_rate_input.value = toAmountString(rate);
}

function disableEnterKey(e)
{
     var key;
     if(window.event)
          key = window.event.keyCode; //IE
     else
          key = e.which; //firefox

     return (key != 13);
}


function exchangeFindRate(btn)
{
    var wrapper = btn.parentNode;

    var wrapper_children = wrapper.childNodes;

    var currency_id_input = null;

    for(var i = 0; i < wrapper_children.length; i++)
    {
        if (typeof(wrapper_children[i].name) == 'undefined') {
            continue;
        }

        if (wrapper_children[i].name == "voucher.ForeignCurrencyID") {
            currency_id_input = wrapper_children[i];
            break;
        }
    }
    var currency = currency_id_input[currency_id_input.selectedIndex].value;
    if (currency == "") {
        // Choose a currency
        alert("Velg en valuta");
        return false;
    }

    var googleQuery = '100 NOK in ' + currency;
    var url = 'http://www.google.com/search?q=' + googleQuery;

    window.open(url,'_blank');

    return false;
}

/*
 * OnChange action for foreign amount fields for each voucher.
 * Calculate amount in domestic currency from specified foreign amount with the
 * current currency rate. Set other fields to 0 so we don't get a problem of
 * having both incoming and outgoung amount set.
 * The first argument is the input element we have changed and the second is a
 * boolean which indicates if it is an incoming or an outgoung field so we change
 * the correct domestic amount field.
 * Used in journal/edit view.
 */
function calculateFromForeignAmount(element, in_flag) {
  var in_amount   = element.parentElement.parentElement.parentElement.getElementsByTagName('input')[0];
  var out_amount  = element.parentElement.parentElement.parentElement.getElementsByTagName('input')[1];

  var f_in_amount  = element.parentElement.parentElement.parentElement.getElementsByTagName('input')[2];
  var f_out_amount = element.parentElement.parentElement.parentElement.getElementsByTagName('input')[3];

  var foreign_amount = toNumber(element.value);
  var conv_rate = 100.0/toNumber(document.getElementsByName('voucher.ForeignConvRate')[0].value);
  if (in_flag) { // incoming
    in_amount.value = toAmountString(Math.round(foreign_amount * conv_rate * 100)/100, 2);
    out_amount.value = toAmountString(0, 2);
    f_in_amount.value = toAmountString(toNumber(f_in_amount.value), 2);
    f_out_amount.value = toAmountString(0, 2);
  }
  else { // outgoing
    out_amount.value = toAmountString(Math.round(foreign_amount * conv_rate * 100)/100, 2);
    in_amount.value = toAmountString(0, 2);
    f_in_amount.value = toAmountString(0, 2);
    f_out_amount.value = toAmountString(toNumber(f_out_amount.value), 2);
  }
  return false;
}

/*
 * OnChange action for incoming and outgoing amount fields for each voucher.
 * When domestic amount field is changed we need to recalculate the currency
 * rate based on the currently set foreign amount(if it is 0, warn user, set
 * this field to 0). Set other to 0 so we dont get a problem of having both both
 * incoming and outgoing amounts set.
 * The first argument is the input element we have changed and the second is a
 * string which indicates if it is an incoming or an outgoung field so we can
 * set the other to 0.
 * Used in journal/edit view.
 */
function allowOnlyCreditOrDebit(element, credit_or_debit) {
  var tr_element = element.parentElement.parentElement;

  var in_amount    = tr_element.getElementsByTagName('input')[1];
  var out_amount   = tr_element.getElementsByTagName('input')[2];
  var f_in_amount  = tr_element.getElementsByClassName('currency_field')[0];
  var f_out_amount = tr_element.getElementsByClassName('currency_field')[1];

  if (credit_or_debit == 'credit') {
    out_amount.value = toAmountString(0, 2);
    if(f_out_amount) f_out_amount.value = toAmountString(0, 2);
    in_amount.value = toAmountString(toNumber(in_amount.value), 2);
  }
  else if (credit_or_debit == 'debit') {
    in_amount.value = toAmountString(0, 2);
    if(f_in_amount) f_in_amount.value = toAmountString(0, 2);
    out_amount.value = toAmountString(toNumber(out_amount.value), 2);
  }
}

/*
 * OnClick action for locking salaries.
 * Checks if all Altinn fields are punched in and warns user which ones aren't
 * with the message that is passed as a parameter.
 * Other params are inital values of fields on page load.
 * Used in salary/edit view.
 */
function checkIfAltinnFieldsSetAndConfirm(message, shift_type, work_time_scheme, type_of_employment, occupation_id, subcompany_id, altinn_date_set) {

  var errors = "";
  // if any of the values are not set add to the warning message
  if (shift_type == "") errors += "- Skifttype\n";
  if (work_time_scheme == "") errors += "- Arbeidstid\n";
  if (type_of_employment == "") errors += "- Ansettelsestype\n";
  if (occupation_id == 0) errors += "- Yrke\n";
  if (subcompany_id == 0) errors += "- Ansatt ved\n";
  if (!altinn_date_set) errors += "- Altinndato\n";
  if (errors != "") errors = "Ikke valgt:\n" + errors;
  message = errors + message;

  return confirm(message);
}

function changeMatchBy(obj) {
  //this just changed, so it really is whether the box wasn't checked beforehand.
  var isChecked = obj.checked;
  var cbs = obj.parentElement.parentElement.getElementsByClassName("match_checkbox");
  for (var i = 0; i < cbs.length; i++) {
    cbs[i].checked = false;
  }

  // if the original one wasn't checked, check it
  obj.checked = isChecked;
}

function validDate(date_string) {
  date             = new Date(date_string);
  parsed_day       = date.getDate();
  parsed_month     = date.getMonth()+1;
  parsed_year      = date.getFullYear();
  date_from_string = date_string.split('-');
  string_year      = date_from_string[0];
  string_month     = date_from_string[1];
  string_day       = date_from_string[2];
  return (parsed_year == string_year && parsed_month == string_month && parsed_day == string_day);
}

// Enable or disable element depending on the status
function enableOrDisable(status, element_id) {
  var element = document.getElementById(element_id);
  if (status) element.disabled = false;
  else element.disabled = true;
}

function setCookie(cookie_name, cookie_value, expires_in_seconds) {
  var d = new Date();
  d.setTime(d.getTime() + (expires_in_seconds*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cookie_name + "=" + cookie_value + "; " + expires;
}

function unsetCookie(cookie_name) {
  setCookie(cookie_name, '', -1);
}

function popCookie(cookie_name) {
  var value = getCookie(cookie_name);
  unsetCookie(cookie_name);
  return value;
}

function getCookie(cookie_name) {
  var name = cookie_name + "=";
  var cookie_array = document.cookie.split(';');
  for(var i = 0; i <cookie_array.length; i++) {
    var cookie = cookie_array[i];
    while (cookie.charAt(0) == ' ') {
      cookie = cookie.substring(1);
    }
    if (cookie.indexOf(name) == 0) {
      return cookie.substring(name.length, cookie.length);
    }
  }
  return "";
}

function get_postmotpost_matches_for_jounal(params) {
  $('div#postmotpostmatches').html("Henter data ...");

  $.post('<? print $_lib['sess']->dispatchs; ?>t=journal.ajax', params,
         function(data, status) {
          $('div#postmotpostmatches').html(data);
         });
  return false;
}

</script>
