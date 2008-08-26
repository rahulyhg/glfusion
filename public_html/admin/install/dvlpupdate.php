<?php
// +--------------------------------------------------------------------------+
// | glFusion CMS                                                             |
// +--------------------------------------------------------------------------+
// | dvlpupdate.php                                                           |
// |                                                                          |
// | glFusion Development SQL Updates                                         |
// +--------------------------------------------------------------------------+
// | $Id::                                                                   $|
// +--------------------------------------------------------------------------+
// | Copyright (C) 2002-2008 by the following authors:                        |
// |                                                                          |
// | Mark R. Evans          mark AT glfusion DOT org                          |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

require_once('../../lib-common.php');

// Only let admin users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the glFusion Development Code Upgrade Routine.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: " . $_SERVER['REMOTE_ADDR'],1);
    $display  = COM_siteHeader();
    $display .= COM_startBlock($LANG27[12]);
    $display .= $LANG27[12];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

COM_errorLog("glFusion: Running code update for glFusion v1.1.0svn");

$retval .= 'Performing database upgrades if necessary...<br />';

$_SQL = array();
$_SQL[] = "
CREATE TABLE {$_TABLES['commentedits']} (
  cid int(10) NOT NULL,
  uid mediumint(8) NOT NULL,
  time datetime NOT NULL,
  PRIMARY KEY (cid)
) TYPE=MYISAM
";
$_SQL[] = "ALTER TABLE {$_TABLES['comments']} ADD name varchar(32) default NULL AFTER indent";
$_SQL[] = "ALTER TABLE {$_TABLES['stories']} ADD comment_expire datetime NOT NULL default '0000-00-00 00:00:00' AFTER comments";
$_SQL[] = "REPLACE INTO {$_TABLES['vars']} (name, value) VALUES ('database_version', '1')";
$_SQL[] = "ALTER TABLE {$_TABLES['syndication']} CHANGE type type varchar(30) NOT NULL default 'article'";
$_SQL[] = "UPDATE {$_TABLES['syndication']} SET type = 'article' WHERE type = 'geeklog'";
$_SQL[] = "UPDATE {$_TABLES['syndication']} SET type = 'article' WHERE type = 'glfusion'";
$_SQL[] = "UPDATE {$_TABLES['configuration']} SET type='select',default_value='s:10:"US/Central";' WHERE name='timezone'";
$_SQL[] = "UPDATE {$_TABLES['configuration']} SET value='s:10:"US/Central";' WHERE name='timezone' AND value=''";
$_SQL[] = "REPLACE INTO {$_TABLES['vars']} (name, value) VALUES ('glfusion', '1.1.0svn')";

/* Execute SQL now to perform the upgrade */
for ($i = 1; $i <= count($_SQL); $i++) {
    COM_errorLOG("glFusion 1.1.0svn Development update: Executing SQL => " . current($_SQL));
    DB_query(current($_SQL),1);
    next($_SQL);
}

$c = config::get_instance();

$c->add('comment_code',0,'select',4,21,17,1670,TRUE);
$c->add('comment_edit',0,'select',4,21,0,1680,TRUE);
$c->add('comment_edittime',1800,'text',4,21,NULL,1690,TRUE);
$c->add('article_comment_close_days',30,'text',4,21,NULL,1700,TRUE);
$c->add('comment_close_rec_stories',0,'text',4,21,NULL,1710,TRUE);

$c->add('jhead_enabled',0,'select',5,22,0,1480,TRUE);
$c->add('path_to_jhead','','text',5,22,NULL,1490,TRUE);
$c->add('jpegtrans_enabled',0,'select',5,22,0,1500,TRUE);
$c->add('path_to_jpegtrans','','text',5,22,NULL,1510,TRUE);

$retval .= 'Development Code upgrades complete - see error.log for details<br>';

$display = COM_siteHeader();
$display .= $retval;
$display .= COM_siteFooter();
echo $display;
exit;
?>