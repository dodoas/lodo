    <div id="kunder">
        <h3>Logg inn</h3>
      <form action="<? print $_lib['sess']->dispatch ?>t=lib.login&amp;interf=" method="post">
          <p>
           Dato:<br />
           <? print $_lib['form3']->text(array('name' => 'LoginFormDate', 'value' => $_lib['sess']->get_session('Date'))) ?><br />
           Før regnskap for:<br />
           <? print $_lib['form3']->text(array('name' => 'DB_NAME_LOGIN', 'value' => $_SESSION['DB_NAME'] . $_REQUEST['DB_NAME_LOGIN'])) ?><br />
           <? if(($_lib['message']->get())) { print("<font color=\"#FF0000\">" . $_lib['message']->get()) . "</font><br />"; } ?>
           Brukernavn (e-post):<br />
           <? print $_lib['form3']->text(array('name' => 'username', 'value' => $_SESSION['login_username'] . $_REQUEST['username'])) ?><br />
           Passord:<br />
           <? print $_lib['form3']->password(array('name' => 'password', 'value'=>'')) ?><br /><br />
           <? print $_lib['form3']->submit(array('name' => 'submit_login', 'accesskey' => 'L', 'value' => 'Logg inn (L)')); ?>
          <br />
           <br />
      </form>
    </div>
