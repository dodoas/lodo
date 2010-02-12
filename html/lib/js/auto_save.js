/*
# $Id: auto_save.js,v 1.7 2005/01/30 12:35:05 thomasek Exp $ auto_save.js,v 1.13 2001/11/18 15:38:27 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no/
*/


function Auto_Save(form_name, table_name, fielDName, where, type) {

    /* alert("form:" + form_name + " Table: " + table_name + " Field: " + fielDName + " Where: " + where + "Type: " + type); */

	if(!where) {
	  alert("Aborting. Where clause empty:" + where);
	}
	if(!fielDName) {
      alert("Aborting. Field name empty:" + fielDName);
	}

	if(type=="checkbox") {
		if(document.forms[form_name].elements[fielDName].checked == true) {
		  field_value = 1;
		} else {
	      field_value = 0;
		}
	} 
	else if(type=="radiobutton") {
	      field_value = getRadioVal(form_name, fielDName);
	}else {
	  field_value = document.forms[form_name].elements[fielDName].value;
	}
	parent.frames["log"].location.href = "index.php?t=lib.auto_save&table_name=" + table_name + "&field=" + fielDName + "&value=" +
field_value + "&where=" + where + "&type=" + type;
}

function alert_box(string) {

  return confirm(string);
}

function gotopage(form, string) {
  parent.frames["main"].location=string;
  form.selectedIndex = 0;
  return 1;
}

function getRadioVal(form_name, fielDName) { 
  var r = document.forms[form_name].elements[fielDName];
  for (var i=0; i<r.length; i++) { 
  if (!r[i].checked) continue; 
  else return r[i].value; 
  }
  return null; 
}


function SelectAllCheckbox(form)
{
    for(var i=0;i<document.form.elements.length;i++)
    {
        if(document.form.elements[i].type == "checkbox")
        {
            document.form.elements[i].checked = true;
        }
    }
}