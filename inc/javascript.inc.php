<?
if($VoucherDate) {
  $_this_date = $VoucherDate;
}
else {
  $_this_date = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'));
}
?>

<script language="javascript1.1">

function place_cursor(formname, fromfieldname) {
  /* document.getElementById('voucher.VoucherDate').focus(); */
  /* document.forms[document.forms.length-4].elements['voucher.VoucherDate'].focus(); */
  document.forms[formname].elements[fromfieldname].focus();
}

function update_voucher(location)
{
    //(location, type, JournalID, VoucherID, VoucherPeriod, VoucherType, VoucherDate, AccountPlanID, OldAccountPlanID, AmountIn, AmountOut, DueDate, KID, InvoiceID, DescriptionID, Description)
    //alert(location);
    //window.location="'https://www.lodo.no/" + location + "'";
}

function update_reference(element, formname, fromfieldname, tofieldname){
    var journalID = element.value;
    if(!journalID)
    {
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
           alert('Dato er feilformatert, eksempel på riktig dato er: YYYY-MM-DD (ISO) - 2004-12-04, DD-MM - 04-12 , DD - 04');
         }
       }

      /*
      We convert the date, so we can not update this automatically
      alert('Dato er feilformatert, eksempel pŒ riktig dato er: YYYY-MM-DD, 2004-04-04'); */
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
          alert("Måned er ikke gyldig: " + result[2]);
          date = '';
        }
        if(result[3] <= 0 || result[3] > 31) {
          alert("Dag er ikke gyldig: " + result[3]);
          date = '';
        }
      }
      else
      {
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

function onCurrencyChange(selObj, voucher_id) {
    var currency = selObj.value;
    var parent = document.getElementById('voucher_currency_div_' + voucher_id);

    if (currency == "") {
        rate = 0;
    } else {
        rate = window.currency_rates[voucher_id][currency];
    }

    var currency_rate_input = findDirectChildByName(parent, "voucher.ForeignConvRate");

    currency_rate_input.value = rate;
    currency_rate_input.display = "none";
}

function voucherCurrencyChange(btn, action_url)
{
    var wrapper = btn.parentNode;

    var wrapper_children = wrapper.childNodes;

    var currency_id_input = null;
    var currency_id_selected_input = null;
    var currency_amount_input = null;
    var currency_rate_input = null;

    for(var i = 0; i < wrapper_children.length; i++)
    {
        if (typeof(wrapper_children[i].name) == 'undefined') {
            continue;
        }

        if (wrapper_children[i].name == "voucher.ForeignCurrencyID") {
            currency_id_input = wrapper_children[i];
        } else if (wrapper_children[i].name == "voucher.ForeignCurrencyIDSelection") {
            currency_id_selected_input = wrapper_children[i];            
        } else if (wrapper_children[i].name == "voucher.ForeignAmount") {
            currency_amount_input = wrapper_children[i];            
        } else if (wrapper_children[i].name == "voucher.ForeignConvRate") {
            currency_rate_input = wrapper_children[i];            
        }
    }
    var currency = currency_id_input[currency_id_input.selectedIndex].value;
    if (currency == "") {
	    alert("Velg en valuta");
	    return false;
    }

    if (currency_amount_input.value == 0) {
	    alert("Velg en verdi");
	    return false;
    }

    if (currency_rate_input.value == 0) {
	    alert("Velg en vekslingsrate");
	    return false;
    }

    currency_id_selected_input.value = currency;

    var currencyform = document.createElement("form");
    currencyform.method = "post";
    currencyform.action = action_url;
    currencyform.appendChild(wrapper.cloneNode(true));
    currencyform.style.display='none';
    document.body.appendChild(currencyform);
    currencyform.submit();
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
	    alert("Velg en valuta");
	    return false;
    }

    var googleQuery = '100 NOK in ' + currency;
    var url = 'http://www.google.com/search?q=' + googleQuery;

    window.open(url,'_blank');    

    return false;
} 

</script>