<?
class validation {

    // recieves date as string 'YYYY-MM-DD' and returns bool if date is valid
    function date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    function mod11_personal($number) { // must take a string
        if(!preg_match('/^\d{11}$/', $number)) return false;

        $digit_10_weights = array(3, 7, 6, 1, 8, 9, 4, 5, 2);
        $digit_11_weights = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);

        $temp_sum = 0;
        for($i=0; $i<9; $i++) {
            $temp_sum += $number[$i] * $digit_10_weights[$i];
        }

        $left_overs = $temp_sum % 11;
        $check = 11 - $left_overs;
        if($left_overs == 0) $check = 0;
        if($number[9] != $check) return false;

        $temp_sum = 0;
        for($i=0; $i<10; $i++) {
            $temp_sum += $number[$i] * $digit_11_weights[$i];
        }

        $left_overs = $temp_sum % 11;
        $check = 11 - $left_overs;
        if($left_overs == 0) $check = 0;
        if($number[10] != $check) return false;

        return true;
    }

    // takes an array in form of ["society_number"=>number, "birth_date"=>date]
    function personal_number_birthday_match($args) {
        $society_number = $args['society_number'];
        $birth_date = $args['birth_date'];

        $day = substr($society_number, 0, 2);
        $month = substr($society_number, 2, 2);
        $year = substr($society_number, 4, 2);

        // in case that this was a society number for someone who is not NO citizen. They have 4 added to the first digit.
        if($day > 40) {
            $day -= 40;
            if($day < 10) $day = '0'.$day;
        }

        $regex = "/".$year."-".$month."-".$day."/";

        return preg_match($regex, $birth_date);
    }
}
?>