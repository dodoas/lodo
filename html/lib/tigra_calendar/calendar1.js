// Title: Tigra Calendar
// URL: http://www.softcomplex.com/products/tigra_calendar/
// Version: 3.2 (European date format)
// Date: 10/14/2002 (mm/dd/yyyy)
// Note: Permission given to use this script in ANY kind of applications if
//    header lines are left unchanged.
// Note: Script consists of two files: calendar?.js and calendar.html

// if two digit year input dates after this year considered 20 century.
var NUM_CENTYEAR = 30;
// is time input control required by default
var BUL_TIMECOMPONENT = false;
// are year scrolling buttons required by default
var BUL_YEARSCROLL = true;

var calendars = [];
var RE_NUM = /^\-?\d+$/;

function calendar1(obj_target) {

    // assigning methods
    this.gen_date = cal_gen_date1;
    this.gen_time = cal_gen_time1;
    this.gen_tsmp = cal_gen_tsmp1;
    this.prs_date = cal_prs_date1;
    this.prs_time = cal_prs_time1;
    this.prs_tsmp = cal_prs_tsmp1;
    this.popup    = cal_popup1;
    this.dateconv = cal_date_convert1;

    // validate input parameters
    if (!obj_target)
        return cal_error("Error calling the calendar: no target control specified");
    if (obj_target.value == null)
        return cal_error("Error calling the calendar: parameter specified is not valid target control");
    this.target = obj_target;
    this.time_comp = BUL_TIMECOMPONENT;
    this.year_scroll = BUL_YEARSCROLL;

    // register in global collections
    this.id = calendars.length;
    calendars[this.id] = this;
}

function cal_popup1 (str_datetime)
{
    if(!str_datetime)
        str_datetime = this.dateconv(this.target.value);

    this.dt_current = this.prs_tsmp(str_datetime); //str_datetime ? str_datetime : this.target.value
    if (!this.dt_current) return;

    var obj_calwindow = window.open(
        '/lib/tigra_calendar/calendar.html?datetime=' + this.dt_current.valueOf()+ '&id=' + this.id,
        'Calendar', 'width=500,height=280'+
        ',status=no,resizable=no,top=200,left=200,dependent=yes,alwaysRaised=yes'
    );
    obj_calwindow.opener = window;
    obj_calwindow.focus();
}

function cal_date_convert1 (str_datetime)
{
    now = new Date();

    // dd
    var pattern = /^(\d{2})$/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        //alert(result[1] + "-" + '01' + "-" + now.getFullYear());
        var datestamp = result[1] + "-" + '01' + "-" + now.getFullYear();
        return datestamp;
    }

    // ddmm
    var pattern = /^(\d{2})(\d{2})$/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        //alert(result[1] + "-" + result[2] + "-" + now.getFullYear());
        var datestamp = result[1] + "-" + result[2] + "-" + now.getFullYear();
        return datestamp;
    }

    // ddmmyy
    var pattern = /^(\d{2})(\d{2})(\d{2})$/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        //alert(result[1] + "-" + result[2] + "-20" + result[3];
        var datestamp = result[1] + "-" + result[2] + "-20" + result[3];
        return datestamp;
    }

    // yyyymmdd
    var pattern = /^(\d{4})(\d{2})(\d{2})$/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var datestamp = result[3] + "-" + result[2] + "-" + result[1];
        return datestamp;
    }

    // dd-mm-yyyy
    var pattern = /(\d{2})-(\d{2})-(\d{4})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        return result[1] + "-" + result[2] + "-" + result[3];
    }

    // dd mm yyyy
    var pattern = /(\d{2}) (\d{2}) (\d{4})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        return result[1] + "-" + result[2] + "-" + result[3];
    }

    // yyyy-mm-dd
    var pattern = /(\d{4})-(\d{2})-(\d{2})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var datestamp = result[3] + "-" + result[2] + "-" + result[1];
        return datestamp;
    }

    // yyyy mm dd
    var pattern = /(\d{4}) (\d{2}) (\d{2})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var datestamp = result[3] + "-" + result[2] + "-" + result[1];
        return datestamp;
    }

    // dd-MM-yyyy
    var pattern = /(\d{2})-(\d{1,})-(\d{4})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[1] + "-" + month + "-" + result[3];
        return datestamp;
    }

    // dd MM yyyy
    var pattern = /(\d{2}) (\w{1,}) (\d{4})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[1] + "-" + month + "-" + result[3];
        return datestamp;
    }

    // yyyy-MM-dd
    var pattern = /(\d{4})-(\d{1,})-(\d{2})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[3] + "-" + month + "-" + result[1];
        return datestamp;
    }

    // yyyy MM dd
    var pattern = /(\d{4}) (\w{1,}) (\d{2})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[3] + "-" + month + "-" + result[1];
        return datestamp;
    }

    // yyyyMMdd
    var pattern = /(\d{4})(\w{1,})(\d{2})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[3] + "-" + month + "-" + result[1];
        return datestamp;
    }

    // ddMMyyyy
    var pattern = /(\d{2})(\w{1,})(\d{4})/;
    var result  = str_datetime.match(pattern);
    if(result != null)
    {
        var month = datestr_toint(result[2]);
        var datestamp = result[1] + "-" + month + "-" + result[3];
        return datestamp;
    }

    tmp = now.valueOf();
    return tmp;
}

function datestr_toint(date_string)
{
    switch(date_string.toLowerCase())
    {
        case 'januar':
            return '01';
        case 'februar':
            return '02';
        case 'mars':
            return '03';
        case 'april':
            return '04';
        case 'mai':
            return '05';
        case 'juni':
            return '06';
        case 'juli':
            return '07';
        case 'august':
            return '08';
        case 'september':
            return '09';
        case 'oktober':
            return '10';
        case 'november':
            return '11';
        case 'desember':
            return '12';
    }
}

// timestamp generating function
function cal_gen_tsmp1 (dt_datetime, isodate)
{
alert("1");
    return(this.gen_date(dt_datetime, isodate) + ' ' + this.gen_time(dt_datetime));
}

// date generating function
function cal_gen_date1 (dt_datetime, isodate)
{
    if(isodate == "1")
    {
        return (
            dt_datetime.getFullYear() + "-"
            + (dt_datetime.getMonth() < 9 ? '0' : '') + (dt_datetime.getMonth() + 1) + "-"
            + (dt_datetime.getDate() < 10 ? '0' : '') + dt_datetime.getDate()
        );
    }
    else
    {
        return (
            (dt_datetime.getDate() < 10 ? '0' : '') + dt_datetime.getDate() + "-"
            + (dt_datetime.getMonth() < 9 ? '0' : '') + (dt_datetime.getMonth() + 1) + "-"
            + dt_datetime.getFullYear()
        );
    }
}
// time generating function
function cal_gen_time1 (dt_datetime)
{
    return (
        (dt_datetime.getHours() < 10 ? '0' : '') + dt_datetime.getHours() + ":"
        + (dt_datetime.getMinutes() < 10 ? '0' : '') + (dt_datetime.getMinutes()) + ":"
        + (dt_datetime.getSeconds() < 10 ? '0' : '') + (dt_datetime.getSeconds())
    );
}

// timestamp parsing function
function cal_prs_tsmp1 (str_datetime)
{
    // if no parameter specified return current timestamp
    if (!str_datetime)
        return (new Date());

    // if positive integer treat as milliseconds from epoch
    if (RE_NUM.exec(str_datetime))
        return new Date(str_datetime);

    // else treat as date in string format
    var arr_datetime = str_datetime.split(' ');
    return this.prs_time(arr_datetime[1], this.prs_date(arr_datetime[0]));
}

// date parsing function
function cal_prs_date1 (str_date)
{
    var arr_date = str_date.split('-');
    var current_date = new Date();

    if (arr_date.length != 3) return cal_error ("Invalid date format: '" + str_date + "'.\nFormat accepted is dd-mm-yyyy.");
    if (!arr_date[0]) return cal_error ("Invalid date format: '" + str_date + "'.\nNo day of month value can be found.");
    if (!RE_NUM.exec(arr_date[0])) return cal_error ("Invalid day of month value: '" + arr_date[0] + "'.\nAllowed values are unsigned integers.");
    if (!arr_date[1]) return cal_error ("Invalid date format: '" + str_date + "'.\nNo month value can be found.");
    if (!RE_NUM.exec(arr_date[1])) return cal_error ("Invalid month value: '" + arr_date[1] + "'.\nAllowed values are unsigned integers.");
    if (!arr_date[2]) return cal_error ("Invalid date format: '" + str_date + "'.\nNo year value can be found.");
    if (!RE_NUM.exec(arr_date[2])) return cal_error ("Invalid year value: '" + arr_date[2] + "'.\nAllowed values are unsigned integers.");

    var dt_date = new Date();
    dt_date.setDate(1);

    if (arr_date[1] == 0 || arr_date[1] == '00') arr_date[1] = current_date.getMonth() + 1;
    if (arr_date[1] < 1 || arr_date[1] > 12) return cal_error ("Invalid month value: '" + arr_date[1] + "'.\nAllowed range is 01-12.");
    dt_date.setMonth(arr_date[1]-1);

    if (arr_date[2] == 0 || arr_date[2] == '0000') arr_date[2] = current_date.getFullYear();
    else if (arr_date[2] < 100) arr_date[2] = Number(arr_date[2]) + (arr_date[2] < NUM_CENTYEAR ? 2000 : 1900);
    dt_date.setFullYear(arr_date[2]);

    var dt_numdays = new Date(arr_date[2], arr_date[1], 0);
    if (arr_date[0] == 0 || arr_date[0] == '00') arr_date[0] = current_date.getDate();
    dt_date.setDate(arr_date[0]);
    if (dt_date.getMonth() != (arr_date[1]-1)) return cal_error ("Invalid day of month value: '" + arr_date[0] + "'.\nAllowed range is 01-"+dt_numdays.getDate()+".");

    return (dt_date)
}

// time parsing function
function cal_prs_time1 (str_time, dt_date)
{
    if (!dt_date) return null;
    var arr_time = String(str_time ? str_time : '').split(':');

    if (!arr_time[0]) dt_date.setHours(0);
    else if (RE_NUM.exec(arr_time[0]))
        if (arr_time[0] < 24) dt_date.setHours(arr_time[0]);
        else return cal_error ("Invalid hours value: '" + arr_time[0] + "'.\nAllowed range is 00-23.");
    else return cal_error ("Invalid hours value: '" + arr_time[0] + "'.\nAllowed values are unsigned integers.");

    if (!arr_time[1]) dt_date.setMinutes(0);
    else if (RE_NUM.exec(arr_time[1]))
        if (arr_time[1] < 60) dt_date.setMinutes(arr_time[1]);
        else return cal_error ("Invalid minutes value: '" + arr_time[1] + "'.\nAllowed range is 00-59.");
    else return cal_error ("Invalid minutes value: '" + arr_time[1] + "'.\nAllowed values are unsigned integers.");

    if (!arr_time[2]) dt_date.setSeconds(0);
    else if (RE_NUM.exec(arr_time[2]))
        if (arr_time[2] < 60) dt_date.setSeconds(arr_time[2]);
        else return cal_error ("Invalid seconds value: '" + arr_time[2] + "'.\nAllowed range is 00-59.");
    else return cal_error ("Invalid seconds value: '" + arr_time[2] + "'.\nAllowed values are unsigned integers.");

    dt_date.setMilliseconds(0);
    return dt_date;
}

function cal_error (str_message)
{
    alert (str_message);
    return null;
}
