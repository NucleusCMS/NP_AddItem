<?php
# NP_AddItem
# Adds an AddLink skinvar that looks and
# behaves much like the builtin editlink
# Changelog:
# 1.4 - by PiyoPiyoNaku | http://mixi.jp/show_friend.pl?id=16761236 | 17 Mar 2007
#		- won't show the add item link if member is not logged in or not part of the current blog team
# 1.3 - by PiyoPiyoNaku | 11 Mar 2007
#		- Japanese-utf8 language supported
#		- skinvar parameter AddItem(nantoka nantoka) will display the link as nantoka nantoka
#		- skinvar parameter AddItem(,nopopup) will not open the link in popup
#		while AddItem(,extlink) will open the link in new window
# 1.2 - by PiyoPiyoNaku | http://renege.net | 4 Feb 2007
#		- fix the codes and will automatically get the url to bookmarklet.php
#		- plugin blog option deleted
# 1.1 - Use textarea for linktext blog option.
# 1.0 - Initial release.

class NP_AddItem extends NucleusPlugin {
	function getName() {
		return 'AddItem';
	}

	function getAuthor() {
		return 'Peter Hardy | PiyoPiyoNaku';
	}

	function getURL() {
		return 'http://www.renege.net';
	}

	function getVersion() {
		return '1.4';
	}

	function getDescription() {
		return _ADDITEM_DESC;
	}

	function init()
	{
		$language = ereg_replace( '[\\|/]', '', getLanguageName());
		if ($language == "japanese-utf8")
		{
			define(_ADDITEM_DESC, "新規投稿ページを直接開くためのリンクを自動作成するプラグイン。 スキンへの記述： &lt;%AddItem%&gt;");
			define(_ADDITEM_TEXT, "アイテムの追加");
		}
		else
		{
			define(_ADDITEM_DESC, "A plugin which creates the link to a bookmarklet page to add a new item. Skinvar: &lt;%AddItem%&gt;");
			define(_ADDITEM_TEXT, "Add New Item");
		}	
	}

	function doSkinVar($skinType, $text = '', $linkMode = '') {
		global $member, $CONF, $blog;

		$isTeam = $this->checkTeam();
		if (!$member->isLoggedIn() || !$isTeam)
		{
			return;
		}

		if (empty($text))
		{
			$text = _ADDITEM_TEXT;
		}

		switch ($linkMode) {
			case 'nopopup' :
				break;
			case 'extlink' :
				$linkModeCode = " target='_blank'";
				break;
			default :
				$linkModeCode = " onclick=\"if (event &amp;&amp; event.preventDefault) event.preventDefault();winbm=window.open(this.href,'nucleusbm','scrollbars=yes,width=600,height=500,left=10,top=10,status=yes,resizable=yes');winbm.focus();return false;\"";
				break;
		}

		$addItemLink = "<a href=\"" . $CONF['AdminURL'] . "bookmarklet.php?blogid=" . $blog->getID() . "\"" . $linkModeCode . ">" . $text . "</a>";
		echo $addItemLink;
	}

	function checkTeam() {
		global $member, $blog;
		
		if ($member->isAdmin())
		{
			$query = 'SELECT bnumber AS result FROM ' . sql_table('blog')
				   . ' WHERE bnumber =' . $blog->getID();
		}
		else
		{
			$query = 'SELECT tblog AS result FROM ' . sql_table('team')
				   . ' WHERE tmember=' . $member->getID()
				   . ' AND tblog=' . $blog->getID();
		}

		return quickQuery($query);
	}

	function supportsFeature($what) {
		switch ($what) {
		case 'SqlTablePrefix':
			return 1;
		default:
			return 0;
		}
	}
}
?> 