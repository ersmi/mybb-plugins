<?php

$plugins->add_hook('newreply_start', 'newreplyhook');
$plugins->add_hook('newreply_do_newreply_end', 'switchpost');
$plugins->add_hook('showthread_start', 'quickreplyhook');

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}

function hiddenreplies_info()
{
	return array(
		"name"			=> "Hidden Replies",
		"description"	=> "Adds option for moderators to hide new posts",
        "author"        => "ersmi",
		"authorsite"	=> "https://www.github.com/ersmi",
		"version"		=> "0.0.0",
		"guid" 			=> "",
		"codename"		=> "ersmi_hidden-replies",
		"compatibility" => "1812"
	);
}

function hiddenreplies_install()
{
    global $db;
    
    $template1 = '<br /><label><input type="checkbox" class="checkbox" name="visible" value="3" />&nbsp;<strong>Hide Post:</strong> make this reply only visible to admins</label><br />';
    
    $template2 = '<label><input type="checkbox" class="checkbox" name="visible" value="3" />&nbsp;<strong>Hide Post</strong></label><br />';
    
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

    find_replace_templatesets(
        "newreply_modoptions",
        "#" . preg_quote('{$stickoption}') . "#i",
        '{$stickoption}{$hideoption}'
    );
    
    find_replace_templatesets(
        "showthread_quickreply",
        "#" . preg_quote('{$option_signature}') . "#i",
        '{$hideoption_quickreply}{$option_signature}'
    );
    
    $insert_array = array(
    'title' => 'hideoption',
    'template' => $db->escape_string($template1),
    'sid' => '-1',
    'version' => '',
    'dateline' => time()
    );
    
    $db->insert_query('templates', $insert_array);
    
    $insert_array = array(
    'title' => 'hideoption_quickreply',
    'template' => $db->escape_string($template2),
    'sid' => '-1',
    'version' => '',
    'dateline' => time()
    );
    
    $db->insert_query('templates', $insert_array);
}

function newreplyhook()
{
    global $mybb, $templates, $hideoption;
    $hideoption = stripslashes($templates->get('hideoption'));
}

function quickreplyhook()
{
    global $hideoption_quickreply, $templates;
    $hideoption_quickreply = stripslashes($templates->get('hideoption_quickreply'));
}

function switchpost()
{
    global $mybb, $db;
    $vis = $mybb->get_input('visible');
 
    if ($vis == "3")
    {
        # TODO: Update this to include check for `uid`
        $ret = $db->write_query("SELECT `pid` FROM `mybb_posts` WHERE `pid` = (SELECT MAX(`pid`) FROM `mybb_posts`)");
        $ret = $ret->fetch_row();
        $q =  "UPDATE `mybb_posts` SET `visible`=0 WHERE `pid`=".$ret[0];
        $db->write_query($q);
    }
    
    # TODO: ajax quick reply waits after successful post
    // if ($mybb->get_input('ajax', MyBB::INPUT_INT))
    // {
        
    // }
}

function hiddenreplies_is_installed()
{
    # TODO: Installation check
    global $db;
    if ($db->write_query("SELECT * FROM `mybb_templates` WHERE `title`='hideoption_quickreply'")->fetch_row())
    {
        return true;
    }
    return false;
}

function hiddenreplies_uninstall()
{
    global $db;
    
    $db->delete_query("templates", "title = 'hideoption'");
    $db->delete_query("templates", "title = 'hideoption_quickreply'");
    
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
    
    find_replace_templatesets(
        "newreply_modoptions",
        "#" . preg_quote('{$stickoption}{$hideoption}') . "#i",
        '{$stickoption}'
    );
    
    find_replace_templatesets(
        "showthread_quickreply",
        "#" . preg_quote('{$hideoption_quickreply}{$option_signature}') . "#i",
        '{$option_signature}'
    );
}

// function hiddenreplies_activate()
// {

// }

// function hiddenreplies_deactivate()
// {

// }
