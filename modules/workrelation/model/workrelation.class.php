<?
/* Work relation class
 */

// include validation class
includecodelib('validation/validation');

class work_relation {

  public static function validate_furloughs($furloughs) {
    $errors = array();

    foreach ($furloughs as $furlough) {
      $id = $furlough->FurloughID;
      # Leave and layoff
      $error_prefix = "Permisjon og permittering ". $id .": ";

      if(self::empty_field($furlough->Text)) {
        # Text can not be blank
        $errors[] = $error_prefix . "Tekst kan ikke v&aelig;re blank.";
      }

      if(self::empty_date($furlough->Start) || !self::valid_date($furlough->Start)) {
        # Starting date may not be blank and must be a valid date
        $errors[] = $error_prefix . "Start dato kan ikke v&aelig;re blank og m&aring; v&aelig;re en gyldig dato.";
      }

      if(!self::empty_date($furlough->Stop) && !self::valid_date($furlough->Stop)) {
        # Ending date must be a valid date
        $errors[] = $error_prefix . "Slutt dato m&aring; v&aelig;re en gyldig dato.";
      }

      if(!self::empty_date($furlough->Start) && !self::empty_date($furlough->Stop) && $furlough->Stop < $furlough->Start) {
        # Ending date may not be before the start date
        $errors[] = $error_prefix . "Slutt dato kan ikke v&aelig;re f&oslash;r start dato.";
      }

      if(self::empty_percent($furlough->Percent) || !self::valid_furlough_percent($furlough->Percent)) {
        # Percent can not be empty and must be between 0 and 100
        $errors[] = $error_prefix . "Prosent kan ikke v&aelig;re tom og m&aring; v&aelig;re mellom 0 og 100.";
      }

      if(self::empty_field($furlough->Description)) {
        # Type con not be blank
        $errors[] = $error_prefix . "Type kan ikke v&aelig;re blank.";
      }

    }

    return $errors;
  }
  public static function validate_work_relations($work_relations) {
    $errors = array();

    foreach ($work_relations as $work_relation) {
      $id = $work_relation->WorkRelationID;
      $error_prefix = "Arbeidsforhold ". $id .": ";

      if(self::empty_field($work_relation->SubcompanyID)) {
        $errors[] = $error_prefix ."Virksomhet kan ikke v&aelig;re blank.";
      }

      if(self::empty_date($work_relation->WorkStart)) {
        $errors[] = $error_prefix ."Start dato kan ikke v&aelig;re blank.";
      }

      if(!self::empty_date($work_relation->WorkStop) && $work_relation->WorkStop < $work_relation->WorkStart) {
        $errors[] = $error_prefix ."Slutt dato kan ikke v&aelig;re f&oring;r start dato.";
      }

      if(self::empty_date($work_relation->InCurrentPositionSince)) {
        $errors[] = $error_prefix ."Samme posisjon siden dato kan ikke v&aelig;re blank.";
      } else {
        if($work_relation->InCurrentPositionSince < $work_relation->WorkStart) {
          $errors[] = $error_prefix ."Samme posisjon siden dato kan ikke v&aelig;re f&oring;r start dato.";
        }
        if($work_relation->InCurrentPositionSince > $work_relation->WorkStop && !self::empty_date($work_relation->WorkStop)) {
          $errors[] = $error_prefix ."Samme posisjon siden dato kan ikke v&aelig;re etter slutt dato.";
        }
      }

      // check for overlaps with other work relations
      foreach ($work_relations as $other_work_relation) {
        // Do not check with itself
        if($work_relation->WorkRelationID == $other_work_relation->WorkRelationID) continue;
        // Do not check if SubcompanyIDs are different
        if($work_relation->SubcompanyID != $other_work_relation->SubcompanyID) continue;

        $overlaps = false;
        if(!self::empty_date($work_relation->WorkStart) && !self::empty_date($other_work_relation->WorkStart)) {
          if(!self::empty_date($work_relation->WorkStop) && !self::empty_date($other_work_relation->WorkStop)) {
            // For two ranges(first: [A,B] second: [C,D]) not to overlap
            // it must be either A<B<C<D or C<D<A<B otherwise they do
            if(!($work_relation->WorkStart > $other_work_relation->WorkStop || $work_relation->WorkStop < $other_work_relation->WorkStart)) $overlaps = true;
          } else {
            if(self::empty_date($work_relation->WorkStop) && self::empty_date($other_work_relation->WorkStop))                              $overlaps = true;
            else if(!self::empty_date($work_relation->WorkStop) && $work_relation->WorkStop > $other_work_relation->WorkStart)              $overlaps = true;
            else if(!self::empty_date($other_work_relation->WorkStop) && $other_work_relation->WorkStop > $work_relation->WorkStart)        $overlaps = true;
          }
        }
        if($overlaps) $errors[] = $error_prefix ."Dato overlaper i arbeidsforhold ". $other_work_relation->WorkRelationID .".";
      }

      if(self::empty_field($work_relation->OccupationID)) {
        $errors[] = $error_prefix ."Yrke kan ikke v&aelig;re blank.";
      }

      if(self::empty_field($work_relation->WorkTimeScheme)) {
        $errors[] = $error_prefix ."Arbeidstid kan ikke v&aelig;re blank.";
      }

      if(self::empty_field($work_relation->ShiftType)) {
        $errors[] = $error_prefix ."Skifttype kan ikke v&aelig;re blank.";
      }

      if(self::empty_field($work_relation->TypeOfEmployment)) {
        $errors[] = $error_prefix ."Ansettelsestype kan ikke v&aelig;re blank.";
      }

      if(empty($work_relation->WorkPercent) || $work_relation->WorkPercent <= 0) {
        $errors[] = $error_prefix ."Stillingsprosent m&aring; v&aelig;re st&oslash;rre enn 0.";
      }

      if(empty($work_relation->WorkMeasurement) || $work_relation->WorkMeasurement <= 0) {
        $errors[] = $error_prefix ."Timer hver uke ved full stilling m&aring; v&aelig;re st&oslash;rre enn 0.";
      }
    }

    return $errors;
  }

  public static function empty_date($date) {
    return empty($date) || $date == '0000-00-00';
  }

  public static function empty_field($value) {
    return empty($value) || $value == "0";
  }

  public static function empty_percent($value) {
    return empty($value);
  }

  public static function valid_furlough_percent($value) {
    return is_numeric($value) && $value > 0 && $value <= 100;
  }

  public static function valid_date($value) {
    return validation::date($value);
  }

}

?>
