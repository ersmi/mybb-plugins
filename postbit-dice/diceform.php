<?php

# myBB overhead required to properly import $db
define("IN_MYBB", 1);
define('THIS_SCRIPT', 'diceform.php');

require_once "./global.php";
global $db, $mybb;

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

# Post method
if ($_SERVER['REQUEST_METHOD'] === 'POST' and $mybb->usergroup['cancp'] == 1)
{   
    # Grab, save, remove tid/pid/uid? from post params.
    $tid = intval($_POST['tid']);
    $pid = intval($_POST['pid']);
    unset($_POST['tid']);
    unset($_POST['pid']);
    
    # Initialize ret and temp arrays
    $a = array();
    $ret = array();
    $k = 0;
    foreach($_POST as $key => $value)
    {
        # Validate number of rolls
        if (($key[0] == 'n')&&($value <= 0))
        {
            exit("ERROR: Negative or zero number of rolls, should be >= 1.");
        }

        $a[] = $value;
    }
    for ($i = 0; $i < count($a); $i += 3)
    {
        # Roll [$i] dice from [$i+1] to [$i+2]
        for ($j = 0; $j < $a[$i]; $j++)
        {
            # Validate lower bound < upper for each roll.
            if ($a[$i+1] >= $a[$i+2])
            {
                exit("ERROR: Upper bound for dice roll " . ($k + 1) . " is less than the lower, did you mean to roll from " . $a[$i+2] . " to " . $a[$i+1] . "?");
            }
            $r = rand($a[$i+1], $a[$i+2]);
            $k++;
            $ret[] = "Dice Roll " . $k . ": ". $a[$i+1] . " - " . $a[$i+2] . " => " . $r;
        }
    }
    $ret =  implode("\n",$ret);
    
    # Insert the dice roll posts into db.
    $q = ("INSERT INTO `mybb_posts` (`pid`, `tid`, `replyto`, `fid`, `subject`, `icon`, `uid`, `username`, `dateline`, `message`, `ipaddress`, `includesig`, `smilieoff`, `edituid`, `edittime`, `editreason`, `visible`) VALUES (NULL, ".$tid.", '0', '0', '', '0', '999999999', '', '".time()."', '".$ret."', 0x0000000000000001, '0', '0', '0', '0', '', '0');");
    $db->write_query($q);
    
    # Create redirect url
    $e = (explode("/", $_SERVER['REQUEST_URI']));
    array_pop($e);
    $returl =  "http://" . $_SERVER[HTTP_HOST] . implode("/",$e) . "/showthread.php?tid=" . $tid;
    
    # Change http header to redirect back to thread.
    header('Location: ' . $returl);
}
else # print the entire html page, TODO: inc. into templates.
{
    print '
<!DOCTYPE html>
<html>
<script>
    var pid = ' . $_GET['pid'] . ';
    var tid = ' . $_GET['tid'] . ';
    var v = 1
    
    function init()
    {
        document.getElementById("tid").value = tid;
        document.getElementById("pid").value = pid;
    }
    
    function addroll()
    {
        var d = document.getElementById("rolls").innerHTML;
        v = v + 1;
        d = d + \'<input type="text" name="n\'+v+\'" value="# of rolls"> <input type="text" name="l\'+v+\'" value="Lower bound"> <input type="text" name="u\'+v+\'" value="Upper bound">\';
        document.getElementById("rolls").innerHTML = d
    }

</script>
<body onload="init()">
    <button onclick="addroll()">+</button> 
    <form action="./diceform.php" method="post">
        tid: <input type="text" name="tid" id="tid"><br>
        pid: <input type="text" name="pid" id="pid"><br>
        Rolls: <br> 
        <div id="rolls"> 
            <input type="text" name="n1" value="# of rolls">
            <input type="text" name="l1" value="Lower bound">
            <input type="text" name="u1" value="Upper bound">
        </div>
        <input type="submit" value="Submit">
            
    </form>
</body>
</html>';
}
?>
