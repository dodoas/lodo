<?

function CheckIfWorkRelationsOverlap($Args, $FirstID, $SecondID) {
  // check only if WR params are sent
  if (isset($Args['workrelation_SubcompanyID_'.$FirstID]) && isset($Args['workrelation_SubcompanyID_'.$SecondID])) {
    // check further only if the subcompany for both WRs is the same
    if ($Args['workrelation_SubcompanyID_'.$FirstID] == $Args['workrelation_SubcompanyID_'.$SecondID]) {
      $a = strtotime($Args['workrelation_WorkStart_'.$FirstID]);
      $b = strtotime($Args['workrelation_WorkStop_'.$FirstID]);
      $c = strtotime($Args['workrelation_WorkStart_'.$SecondID]);
      $d = strtotime($Args['workrelation_WorkStop_'.$SecondID]);
      // For two ranges(first: [A,B] second: [C,D]) not to overlap
      // it must be either A<B<C<D or C<D<A<B otherwise they do
      if (!($b < $c || $a > $d)) return true;
    } else return false;
  } else return false;
}

?>
