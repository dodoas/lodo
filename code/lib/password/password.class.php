<?
class password
{
    var $_password = '';
    var $_minLength = 8;
    var $_strict = false;

    var $_badWords = array(
        'god', 'password', 'bob', 'gud', 'passord',
        'pass', 'admin', 'login', 'jkl', 'asdf', 'qwerty',
        'root', 'administrator', 'foobar', 'guru', 'boss',
        'sjefen', 'dust', 'zxcvb', 'linux', 'virker',
        'john', 'ola', 'nordmann', 'windows', 'microsoft'
    );

    function password($args)
    {
        if($args['minLength'] > 0)
            $this->_minLength = $args['minLength'];

        if($args['strict'])
            $this->_strict == true;
    }

##############################################################

    function checkPassword($args)
    {
        $this->_password = strtolower($args['password']);

        if(strlen($this->_password) < $_minLength)
        {
            return array('value'=>false, 'message'=>"Password to short, must be $_minLength characters in length";
        }

        if(ctype_alpha($this->_password) or ctype_digit($this->_password))
        {
            return array('value'=>false, 'message'=>"Password must contain letters and numbers";
        }

        if($this->_strict)
        {
            if(preg_match('/(.).*\1.*\1/', $this->_password)
            {
                return array('value'=>false, 'message'=>"Password uses same character more than once");
            }

            if($this->CheckWords($this->_password))
            {
                return array('value'=>false, 'message'=>"Password contains bad words");
            }

            $nonum = preg_replace("/\d/", "", $this->_password);
            if($this->CheckWords($nonum);
            {
                return array('value'=>false, 'message'=>'Password contains bad words');
            }
        }

        return array('value'=>true, 'message'=>'Password is secure');
    }

    function CheckWords($stringToCheck)
    {
        foreach($this->_badWords as $word)
        {
            if(strpos($stringToCheck, $word) != false)
            {
                return 1;
            }
        }
        return 0;
    }

    //
    // Generates a random string with the specified length
    // Chars are chosen from the provided [optional] list
    //
    function simpleRandString($args)
    {
        $list="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        mt_srand((double)microtime()*1000000);
        $newstring="";

        if($this->_minLength > 0)
        {
            while(strlen($newstring) < $this->_minLength)
            {
                $newstring .= $list[mt_rand(0, strlen($list)-1)];
            }
        }
        return $newstring;
    }

    //
    // Generates a random string with the specified length
    // Includes: a-z, A-Z y 0-9
    //
    function randString($args)
    {
        mt_srand((double)microtime()*1000000);
        $newstring="";

        if($this->_minLength > 0){
            while(strlen($newstring) < $this->_minLength)
            {
                switch(mt_rand(1,3))
                {
                    case 1: $newstring .= chr(mt_rand(48,57)); break;  // 0-9
                    case 2: $newstring .= chr(mt_rand(65,90)); break;  // A-Z
                    case 3: $newstring .= chr(mt_rand(97,122)); break; // a-z
                }
            }
        }
       return $newstring;
    }
}
?>
