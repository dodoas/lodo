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
    $d = strtotime($selected_year . "-01-01");

    // check if first day is sunday
    $first_day_of_year = date("N", $d);
    if($first_day_of_year == 7) {
        $template->addEntry($selected_year, '1', date("Y-m-d", $d), date("Y-m", $d), 'O');
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

            $monday_d = strtotime("last monday", $tmp_d);
            
            $template->addEntry(
                $selected_year,
                date('W', $tmp_d),
                date('Y-m-d', $monday_d),
                date('Y-m-d', $tmp_d),
                date('Y-m', $tmp_d), 
                'O'
                );

            $template->addEntry(
                $selected_year, 
                date('W', $d),
                date('Y-m-d', $tmp_d + 60*60*24),
                date('Y-m-d', $d),
                date('Y-m', $d),
                'O'
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
                'O'
                );
        }

        $last_month = $this_month;


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