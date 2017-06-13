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
?>
