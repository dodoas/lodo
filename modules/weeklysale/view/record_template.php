<?

if(isset($_POST['template_add_blank_entry'])) {
    $template->addBlankEntry();
}
else if(isset($_POST['template_delete_marked'])) {
    if(!$_POST["template_selected"]) {
        $_lib['message']->add(array('message' => 'Ingen slettet'));
    }
    else {
        $marked = $_POST["template_selected"];
        foreach($marked as $id) {
            $template->deleteEntry($id);
        }
    }
}
else if(isset($_POST['template_delete_marked_voucher'])) {
    if(!$_POST["template_selected"]) {
        $_lib['message']->add(array('message' => 'Ingen slettet'));
    }
    else {
        $marked = $_POST["template_selected"];
        foreach($marked as $id) {
            $template->deleteEntryVoucher($id);
        }
    }

    $template->reload();
}
else if(isset($_POST['template_save'])) {
    $template->updateFromPost();
}
else if(isset($_POST['template_import_file'])) {
    if(!strstr($_FILES['fileimport']['name'], "wsarr")) {
        $_lib['message']->add(array('message' => 'Filen ble ikke importert'));
    }
    else {
        $data = file_get_contents($_FILES['fileimport']['tmp_name']);
        $template->importSerialized($data);
    }
}
else if(isset($_POST['template_add_defaults'])) {

    $query_sale_conf  = sprintf(
        "select * from weeklysaleconf where WeeklySaleConfID = '%s'",
        $selected_config);
    $result_sale_conf = $_lib['db']->db_query($query_sale_conf);
    $sale_conf        = $_lib['db']->db_fetch_object($result_sale_conf);

    $d = strtotime($selected_year . "-01-01");

    // check if first day is sunday
    $first_day_of_year = date("N", $d);
    if($first_day_of_year != 1) {
        if(date("N", $d) != 7)
            $tmp_d = strtotime("next sunday", $d);
        else
            $tmp_d = $d;

        // add 0 week from last year only if it starts on an thu, fri, sat or sun
        if (date('N', $d) > 4) {
          $template->addEntry(
              $selected_year,
              '0',
              date("Y-m-d", $d),
              date("Y-m-d", $tmp_d),
              date("Y-m", $d),
              $sale_conf->VoucherType
              );

          $d = $tmp_d;
        }
    }

    $last_month = date("M", $d);

    for($d = strtotime("next sunday", $d);
        date("Y", $d) == $selected_year;
        $d = strtotime("next sunday", $d)
        ) {

        $this_month = date("M", $d);

        // If new month and sunday is not on the 7th
        if($this_month != $last_month && date("d", $d) != "07") {
            $tmp_d = strtotime($selected_year . "-" . $this_month . "-01");
            $tmp_d -= 60*60*24;

            if(date("N", $tmp_d) != 1)
                $monday_d = strtotime("last monday", $tmp_d);
            else
                $monday_d = $tmp_d;
            
            $template->addEntry(
                $selected_year,
                date('W', $tmp_d),
                date('Y-m-d', $monday_d),
                date('Y-m-d', $tmp_d),
                date('Y-m', $tmp_d), 
                $sale_conf->VoucherType
                );

            $template->addEntry(
                $selected_year, 
                date('W', $d),
                date('Y-m-d', $tmp_d + 60*60*24),
                date('Y-m-d', $d),
                date('Y-m', $d),
                $sale_conf->VoucherType
                );
        }
        else {
            $monday_d = strtotime("last monday", $d);
            $template->addEntry(
                $selected_year, 
                date('W', $d),
                date('Y-m-d', $monday_d),
                date('Y-m-d', $d),
                date('Y-m', $d),
                $sale_conf->VoucherType
                );
        }

        $last_month = $this_month;
    }

    // add week 53 if  next sunday is in next year, and not day no 7
    // if it is day no 07 , that means last week ended the 31st of dec
    if(date("Y", $d) != $selected_year && date('d', $d) != '07') {
        $last_day = strtotime($selected_year . "-12-31");
        if (date('N',$last_day) == '1') {
            $d = $last_day;
        } else {
           $d = strtotime("last monday", $last_day);
        }

        $template->addEntry(
            $selected_year,
            (date('W', $d) == '01' ? '53' : date('W', $d)),
            date('Y-m-d', $d),
            $selected_year . '-12-31',
            date('Y-m', $d),
            $sale_conf->VoucherType
            );
    }

    $template->reload();
}
else if(isset($_POST['template_create_weeklysales'])) {
    if(!$_POST["template_selected"]) {
        $_lib['message']->add(array('message' => 'Ingen opprettet'));
    }
    else {
        $marked = $_POST["template_selected"];
        foreach($marked as $id) {
            $template->create($id);
        }
    }

    $template->reload();
}