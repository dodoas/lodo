<?

function validate_work_relations($array) {
  $errors = array();

  foreach ($array as $work_relation) {
    $id = $work_relation->WorkRelationID;
    $error_prefix = "Work relation ". $id .": ";

    if(empty($work_relation->SubcompanyID) || $work_relation->SubcompanyID == "0") {
      $errors[] = $error_prefix ."Subcompany must be selected.";
    }

    if(empty_date($work_relation->WorkStart)) {
      $errors[] = $error_prefix ."Start date must be picked.";
    }

    if(!empty_date($work_relation->WorkStop) && $work_relation->WorkStop < $work_relation->WorkStart) {
      $errors[] = $error_prefix ."End date cannot be before start date.";
    }

    if(empty_date($work_relation->InCurrentPositionSince)) {
      $errors[] = $error_prefix ."Current position starting date must be picked."; 
    } else {
      if($work_relation->InCurrentPositionSince < $work_relation->WorkStart) {
        $errors[] = $error_prefix ."Current position starting date cannot not be before start date.";
      }
      if($work_relation->InCurrentPositionSince > $work_relation->WorkStop && !empty_date($work_relation->WorkStop)) {
        $errors[] = $error_prefix ."Current position starting date cannot not be after end date.";
      }
    }

    // check for overlaps with other work relations
    foreach ($array as $other_work_relation) {
      if($work_relation->WorkRelationID == $other_work_relation->WorkRelationID) continue; // do not check with itself

      if($work_relation->SubcompanyID == $other_work_relation->SubcompanyID) {
        $overlaps = false;

        if(!empty_date($work_relation->WorkStart) && !empty_date($other_work_relation->WorkStart)) {
          if(!empty_date($work_relation->WorkStop) && !empty_date($other_work_relation->WorkStop)) {
            // For two ranges(first: [A,B] second: [C,D]) not to overlap
            // it must be either A<B<C<D or C<D<A<B otherwise they do
            if(!($work_relation->WorkStart > $other_work_relation->WorkStop || $work_relation->WorkStop < $other_work_relation->WorkStart)) $overlaps = true;
          } else {
            if(empty_date($work_relation->WorkStop) && empty_date($other_work_relation->WorkStop))                                          $overlaps = true;
            else if(!empty_date($work_relation->WorkStop) && $work_relation->WorkStop > $other_work_relation->WorkStart)                    $overlaps = true;
            else if(!empty_date($other_work_relation->WorkStop) && $other_work_relation->WorkStop > $work_relation->WorkStart)              $overlaps = true;
          }
        }
        if($overlaps) $errors[] = $error_prefix ."Dates overlap with work relation ". $other_work_relation->WorkRelationID .".";
      }
    }

    if(empty($work_relation->OccupationID) || $work_relation->OccupationID == "0") {
      $errors[] = $error_prefix ."Occupation must be selected.";
    }

    if(empty($work_relation->KommuneID) || $work_relation->KommuneID == "0") {
      $errors[] = $error_prefix ."Municipality must be selected.";
    }

    if(empty($work_relation->WorkTimeScheme) || $work_relation->WorkTimeScheme == "0") {
      $errors[] = $error_prefix ."Work time scheme must be selected.";
    }

    if(empty($work_relation->ShiftType) || $work_relation->ShiftType == "0") {
      $errors[] = $error_prefix ."Shift type must be selected.";
    }

    if(empty($work_relation->TypeOfEmployment) || $work_relation->TypeOfEmployment == "0") {
      $errors[] = $error_prefix ."Type of employment must be selected.";
    }

    if(empty($work_relation->WorkPercent) || $work_relation->WorkPercent <= 0) {
      $errors[] = $error_prefix ."Work percent must be greater than zero.";
    }

    if(empty($work_relation->WorkMeasurement) || $work_relation->WorkMeasurement <= 0) {
      $errors[] = $error_prefix ."Working hours must be greater than zero.";
    }
  }
  
  return $errors;
}

function empty_date($date) {
  return empty($date) || $date == '0000-00-00';
}

function empty_field($value) {
  return empty($value) || $value == "0";
}

?>
