<?php

$plugins->add_hook('postbit', 'dicebutton');

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

function postbitdice_info()
{
	return array(
		"name"			=> "Postbit Dice",
		"description"	=> "Diceroll buttons for user posts",
		"author"		=> "ersmi",
		"authorsite"	=> "https://www.github.com/ersmi",
		"version"		=> "0.0.1",
		"guid" 			=> "",
		"codename"		=> "ersmi_postbit-dice",
		"compatibility" => "1812"
	);
}

function postbitdice_install()
{
    global $db;
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $pass = '';
    for ($i = 0; $i < 35; $i++)
    {
        $pass .= $chars[rand(0,strlen($chars))];
    }
    
    $db->write_query("INSERT INTO `mybb_users` (`uid`, `username`, `password`, `salt`, `loginkey`, `email`, `postnum`, `threadnum`, `avatar`, `avatardimensions`, `avatartype`, `usergroup`, `additionalgroups`, `displaygroup`, `usertitle`, `regdate`, `lastactive`, `lastvisit`, `lastpost`, `website`, `icq`, `aim`, `yahoo`, `skype`, `google`, `birthday`, `birthdayprivacy`, `signature`, `allownotices`, `hideemail`, `subscriptionmethod`, `invisible`, `receivepms`, `receivefrombuddy`, `pmnotice`, `pmnotify`, `buddyrequestspm`, `buddyrequestsauto`, `threadmode`, `showimages`, `showvideos`, `showsigs`, `showavatars`, `showquickreply`, `showredirect`, `ppp`, `tpp`, `daysprune`, `dateformat`, `timeformat`, `timezone`, `dst`, `dstcorrection`, `buddylist`, `ignorelist`, `style`, `away`, `awaydate`, `returndate`, `awayreason`, `pmfolders`, `notepad`, `referrer`, `referrals`, `reputation`, `regip`, `lastip`, `language`, `timeonline`, `showcodebuttons`, `totalpms`, `unreadpms`, `warningpoints`, `moderateposts`, `moderationtime`, `suspendposting`, `suspensiontime`, `suspendsignature`, `suspendsigtime`, `coppauser`, `classicpostbit`, `loginattempts`, `usernotes`, `sourceeditor`) VALUES ('999999999', 'The Dice Man™', '".$pass."', '', '', '', '0', '0', '', '', '0', '0', '', '0', '', '0', '0', '0', '0', '', '', '', '', '', '', '', 'all', '', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '0', '0', '', '', '0', '0', '0', '', '', '', '', '0', '0', '0', '', '', '', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', 'Account used for dice rolls, do not modify!', '0')");
    
    # Update postbit to include dice roll button
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    find_replace_templatesets(
        "postbit",
        "#" . preg_quote('{$post[\'button_edit\']}') . "#i",
        '{$post[\'dice_button\']}{$post[\'button_edit\']}'
    );
}

function dicebutton($post)
{
    global $mybb;
    if ($mybb->usergroup['cancp'] == 1) # Only admins roll
    {
        $post['dice_button'] = '
    <style>
    .dicemodalc {
        display: none; 
        position: fixed; 
        z-index: 1;
        padding-top: 30%;
        padding-left: 10%;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
    }
    .dicemodalform {
        background-color: #FFFFFF;
        top: 25%;
        left: 28%;
        width: 600px;
        position:absolute;
        padding-left: 10px;
        padding-top: 10px;
        padding-right: 10px;
        padding-bottom: 10px;
        border-spacing: 25px;
        border-style: solid;
        border-width: 3px;
        border-radius: 10px;
    }
    .closebutton {
        background-color: #FFFFFF;
        position: absolute;
        right: 20px;
        top: 5px;
    }
    .diceformbody {
        background-color: $FFFFFF;
        border-spacing: 25px;
        padding-left: 10px;
        padding-top: 10px;
    }
    </style>
    <script>
    function rollDice(pid, tid)
	{
        init(pid,tid);
        // console.log("PLEASE");
        // $(document).ready(function(){
		// });
		// window.location = ("/diceform.php?&pid="+pid+"&tid="+tid);
        //MyBB.popupWindow("/diceform.php?&pid="+pid+"&tid="+tid);
        var dicemodal = document.getElementById("dicemodal");
        dicemodal.style.display = "block";
	}
    function closeModal()
    {
        var m = document.getElementById("dicemodal");
        m.style.display = "none";
    }
    var v = 1
    
    console.log(tid);
    console.log(pid);
    
    function init(pid, tid)
    {
        document.getElementById("tid").value = tid;
        document.getElementById("pid").value = pid;
    }
    
    function addroll()
    {
        console.log("MADE IT")
        var d = document.getElementById("rolls").innerHTML;
        console.log(d);
        v = v + 1;
        d = d + \'<input type="text" name="n\'+v+\'" value="# of rolls"> <input type="text" name="l\'+v+\'" value="Lower bound"> <input type="text" name="u\'+v+\'" value="Upper bound">\';
        console.log(d);
        document.getElementById("rolls").innerHTML = d
    }
    </script>

    <div id="dicemodal" class="dicemodalc">
        <div id="dicemodalformdiv" class="dicemodalform">
            <span id="closebutton" class="closebutton" onclick="closeModal()">X</span>
        <body class="diceformbody">
        <form action="./diceform.php" method="post">
        tid: <input type="text" name="tid" id="tid"><br>
        pid: <input type="text" name="pid" id="pid"> (You probably don\'t want to change these...)<br>
        Rolls: <button type="button" onclick="addroll()">+</button> <br> 
        <div id="rolls"> 
            <input type="text" name="n1" value="# of rolls">
            <input type="text" name="l1" value="Lower bound">
            <input type="text" name="u1" value="Upper bound">
        </div>
        <input type="submit" value="Submit">
        </form>
        </body>
        </div>
    </div>

    <a href="javascript:void(0)" onclick="rollDice('.$post['pid'].','.$post['tid'].');" title="Roll dice" class="postbit_dice"><span>Roll Dice</span></a>';
    }
    return $post;
}

function postbitdice_is_installed()
{
    # If dice acc. is in db => true
    global $db;
    $q = $db->write_query("SELECT * FROM `mybb_users` WHERE `uid`=999999999 AND `username`='The Dice Man™'");
    if ($q->fetch_row())
    {
        return true;
    }
    return false;
}

function postbitdice_uninstall()
{
    global $db;
    # Remove dice acc from db
    $db->write_query("DELETE FROM `mybb_users` WHERE `uid`=999999999 AND `username`='The Dice Man™'");
    
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
    
    # Remove dice button from postbit template
    find_replace_templatesets(
        "postbit",
        "#" . preg_quote('{$post[\'dice_button\']}{$post[\'button_edit\']}') . "#i",
        '{$post[\'button_edit\']}'
    );
}

// function dice_activate()
// {

// }

// function dice_deactivate()
// {

// }
