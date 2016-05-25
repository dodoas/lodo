<?
class validation {
    function mod11_personal($number) { // must take a string
        if(!preg_match('/^\d{11}$/', $number)) return false;

        $digit_10_weights = array(3, 7, 6, 1, 8, 9, 4, 5, 2);
        $digit_11_weights = array(5, 4, 3, 2, 7, 6, 5, 4, 3, 2);

        $temp_sum = 0;
        for($i=0; $i<9; $i++) {
            $temp_sum += $number[$i] * $digit_10_weights[$i];
        }
        if($number[9] != (-$temp_sum)%11 + 11) return false;

        $temp_sum = 0;
        for($i=0; $i<10; $i++) {
            $temp_sum += $number[$i] * $digit_11_weights[$i];
        }
        if($number[10] != (-$temp_sum)%11 + 11) return false;

        return true;
    }
}
?>