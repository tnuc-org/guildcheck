<?php
error_reporting(0);

class Gcheck
{
	private static $guild = "";
	private static $server = "";
	private static $style = 0; // 0 = classic, 1 = vip
	private static $invitees = false;
	private static $multi = false;
	private static $valid = false;
	private static $set = false;
	private static $ocount = 0;
	
	private static $output = "";

	private static $a_olist = 'http://www.tibia.com/community/?subtopic=worlds&world=';
	//private static $a_olist = 'http://www.tibia.com/community/?subtopic=whoisonline&world=';
	private static $a_guild = 'http://www.tibia.com/community/?subtopic=guilds&page=view&GuildName=';
	private static $a_guild_esc = 'http://www.tibia.com/community/?subtopic=guilds&amp;page=view&amp;GuildName=';
	private static $a_char = 'http://www.tibia.com/community/?subtopic=characters&name=';
	private static $a_char_esc = 'http://www.tibia.com/community/?subtopic=characters&amp;name=';
	private static $servlist = array('Aldora','Amera','Antica','Arcania','Askara','Astera','Aurea','Aurera', 'Aurora', 'Azura',
									'Balera','Berylia','Calmera','Candia','Celesta','Chimera','Danera','Danubia',
									'Dolera','Elera','Elysia','Empera','Eternia','Fidera','Fortera','Furora','Galana',
									'Grimera','Guardia','Harmonia','Hiberna','Honera','Inferna','Iridia','Isara',
									'Jamera','Julera','Keltera','Kyra','Libera','Lucera','Luminera','Lunara','Magera','Malvera',
									'Menera','Morgana','Mythera','Nebula','Neptera','Nerana','Nova','Obsidia','Ocera',
									'Olympa','Pacera','Pandoria','Premia','Pythera','Refugia','Rubera','Samera','Saphira',
									'Secura','Selena','Shanera','Shivera','Silvera','Solera','Tenebra','Thoria',
									'Titania','Trimera','Unitera','Valoria','Vinera','Xantera','Xerena','Zanera');
	
	private static function append_header() {
		if(self::$multi) {
			$t = !self::$valid ? "tibia guildcheck" : "tibia guildcheck - " . strtolower(implode(", ",self::$guild));
		} else {
			$t = !self::$valid ? "tibia guildcheck" : "tibia guildcheck - " . strtolower(self::$guild);
		}
		$css = self::$style === 0 ? 'style.css' : 'stylev.css';
		self::$output = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>$t</title>
<link rel="stylesheet" type="text/css" href="./$css" />
<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
<link rel="icon" href="favicon.ico" type="image/vnd.microsoft.icon" />
<meta name="description" content="Tibia Guildcheck, see who is online from one or more guilds of your choice." />
<meta name="author" content="Flo" />
<meta name="keywords" content="tnuc,tibia,guildcheck,guild check,guild,check,flo,whoisonline,mavina,najko" />
<script type="text/javascript" src="./misc.js"></script>
</head>
<body>
<div align="center">
<div id="wrap">
<h1>Guildcheck 2.1</h1>
<br />
EOD;
if(!self::$multi) {
	self::$output .= <<< EOSINGLE
	
classic | <a href="./?mode=multi">multi</a><br /><br />
<form action="./" name="gcheck" method="get">

<tr><td class="r">Server: </td><td class="l"><select size="1" class="txt fwid" name="server"><option value="AUTO">automatic *
EOSINGLE;
	foreach(self::$servlist as $s) self::$output .= self::$server === $s ? "<option value=\"$s\" SELECTED>$s" : "<option value=\"$s\">$s";
	$g = self::$guild;
	self::$output .= <<<EOSINGLE
</select><br />
<input class="txt" type="text" name="guild" value="$g" size="30" maxlength="30" />
<br /><br />
<input class="btn" type="submit" value=" search " />
</form>
<br /><br />
EOSINGLE;
	}
	else {
	$sstyle = array('','');
	$sstyle[self::$style] = ' SELECTED';
	$sinv = array('','');
	$sinv[(int)self::$invitees] = ' SELECTED';
	self::$output .= <<< EOMULTI

<a href="./">classic</a> | multi<br /><br />
<form action="./" name="gcheck" method="get">
<input type="hidden" name="mode" value="multi">
<table border="0" cellpadding="0" cellspacing="0">
<tr><td class="r">Invitees: </td><td class="l"><select size="1" class="txt fwid" name="invitees"><option value="0"{$sinv[0]}>ignore<option value="1"{$sinv[1]}>include</select></td></tr>
<tr><td class="r">Style: </td><td class="l"><select size="1" class="txt fwid" name="style"><option value="c"{$sstyle[0]}>classic<option value="v"{$sstyle[1]}>vip</select></td></tr>
<tr><td class="r">Server: </td><td class="l"><select size="1" class="txt fwid" name="server"><option value="AUTO">automatic *
EOMULTI;
	foreach(self::$servlist as $s) self::$output .= self::$server === $s ? "<option value=\"$s\" SELECTED>$s" : "<option value=\"$s\">$s";
	self::$output .= <<<EOMULTI
</select></td></tr></table>
EOMULTI;
	if(!self::$valid) self::$guild = array("","");
	self::$output .= "<div id=\"list\">";
	$xi = 0;
	foreach(self::$guild as $v)
	{
		$xi++;
		self::$output .= "<input id=\"g$xi\" class=\"blk txt\" type=\"text\" name=\"g$xi\" value=\"$v\" size=\"30\" maxlength=\"30\" />";
	}
	self::$output .= "</div>";
	self::$output .= <<<EOMULTI
<br />
<script type="text/javascript">var state = $xi;</script>
<input class="btn" type="button" value=" more " onclick="gmore()" /> 
<input class="btn" type="button" value=" less " onclick="gless()" /> 
<input class="btn" type="submit" value=" search " />
</form>
<br /><br />
EOMULTI;
	}
	}
	
	private static function append_footer() {
		self::$output .= <<<EOD
		
<br /><br />
by Flo - <a href="http://tnuc.org">tnuC.org</a><br />
</div>
</body>
</html>
EOD;
	}
	
	private static function getvars() {
		self::$multi = isset($_GET['mode']) ? true : false; 
		self::$server = isset($_GET['server']) && in_array($_GET['server'],self::$servlist) ? $_GET['server'] : '';
		self::$style = isset($_GET['style']) && $_GET['style'] === 'v' ? 1 : 0;
		self::$invitees = isset($_GET['invitees']) && $_GET['invitees'] == '1' ? true : false;
		if(!self::$multi) {
			self::$guild = isset($_GET['guild']) ? (get_magic_quotes_gpc() ? trim(stripslashes(urldecode($_GET['guild']))) : trim(urldecode($_GET['guild']))) : "";
		} else { 
			self::$guild = array();
			for($i=1;$i<=6;$i++) {
				if(isset($_GET["g$i"]))
					self::$guild []= get_magic_quotes_gpc() ? trim(stripslashes(urldecode($_GET["g$i"]))) : trim(urldecode($_GET["g$i"]));
			}
		}
		if(self::$guild !== "" && !(is_array(self::$guild) && count(self::$guild) === 0)) self::$set=true;
	}
	
	private static function checknames() {
		if(!self::$multi) {
			return (bool) preg_match("~^[A-Za-z][A-Za-z\\-'\\s]*[A-Za-z]$~",self::$guild);
		} else {
			$xvalid = 0;
			foreach(self::$guild as $k => $g) {
				if($g === '' || !preg_match("~^[A-Za-z][A-Za-z\\-'\\s]*[A-Za-z]$~",$g)) {
					unset(self::$guild[$k]);
				} else {
					$xvalid+=1;
				}
			}
			return $xvalid > 0 ? true : false;
		}
	}
	
	private static function curlget(&$html,$serv = false) {
		$ch = curl_init();
		$url = $serv ? self::$a_olist . self::$server : self::$a_guild . urlencode(self::$guild);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 7);
		$html = curl_exec($ch);
		curl_close($ch);
		return $dest !== null ? true : false;
	}
	
	private static function multiget(&$html,$incserv = false) {
		$ch = array();
		$html = array();
		$mh = curl_multi_init();
		if($incserv) {
			$ch['--SERVER--'] = curl_init();
		}
		if(is_array(self::$guild)) {
			foreach(self::$guild as $k => $v) {
				$ch[$v] = curl_init();
			}
		} else {
			$ch[self::$guild] = curl_init();
		}
		foreach($ch as $k => $c) {
			$url = $k === '--SERVER--' ? self::$a_olist . self::$server : self::$a_guild . urlencode($k);
			curl_setopt($c,CURLOPT_URL, $url);
			curl_setopt($c,CURLOPT_USERAGENT, "Mozilla/5.0");
			curl_setopt($c,CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($c,CURLOPT_AUTOREFERER, true);
			curl_setopt($c,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($c,CURLOPT_TIMEOUT, 7);
			curl_multi_add_handle($mh, $ch[$k]);
		}
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		} while($running > 0);
		foreach($ch as $k => $c) {
			$html[$k] = curl_multi_getcontent($c);
			if($html[$k] === null) $html[$k] = "";
			curl_multi_remove_handle($mh, $c);
		}
		curl_multi_close($mh);
		return null;
	}
	
	private static function parse_guild(&$html,&$gname,&$gmatches) {
		if(!is_array($gmatches)) $gmatches = array();
		
		if(!preg_match('~<BR>\nThe guild was founded on ([A-Za-z]*) on [A-Za-z0-9&#;]*\.<BR>\nIt is currently active~',$html,$temp))
			throw new Exception("Guild $gname does not exist or Tibia.com is being gay.");
		if(self::$server === "") self::$server = $temp[1];
		$temp = null;
		$html = str_replace(array("&nbsp;","&#160;","&#39;"),array(" "," ","'"),$html);
		if(self::$invitees === false) {
			$pLeft = strpos($html,'Guild Members</B>');
			$pRight = strpos($html,'<B>Invited Characters');
			$html = substr($html,$pLeft,$pRight-$pLeft);
		}
		if(!preg_match_all('~subtopic=characters\&name=[^\"]*\">([^<]*)</A>~',$html,$temp)) {
			throw new Exception("Guild $gname does not exist or Tibia.com is being gay.");
		} else {
			for($i=0;$i<sizeof($temp[1]);$i++) {
				if(!in_array($temp[1][$i],$gmatches)) {
					$gmatches[$temp[1][$i]]=$gname;
				}
			}
			$temp = null;
		}
		$html = null;
		return;
	}
	
	private static function parse_match_online(&$html,&$omatches,&$ovocs,&$gmatches,&$allon) {
		$omatches = array('Druids' => array(),'Sorcerers' => array(),'Paladins' => array(),'Knights' => array(),'Rookies' => array());
		$allon = array();
		//if(strpos($html,'<B>Server Status<\/B><\/TD><\/TR><TR[^>]*><TD><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=1><TR><TD>Currently ') !== false) {
		if(strpos($html,'<td class="LabelV200" >Players Online:</td><td>') === false) {
			throw new Exception("Server or online list offline.");
		}
		$html = str_replace(array("&nbsp;","&#160;","&#39;"),array(" "," ","'"),$html);
		if(preg_match_all('~name=[^"]*?"[^>]*?>([^<]+?)</a></td><td[^>]*?>([^<]+?)</td><td[^>]+?>([^<]+?)</td>~',$html,$temp) == 0) {
			throw new Exception("Server or online list offline.");
		}
		foreach($temp[1] as $k => $v) {
			foreach($gmatches as $gk => $gv) {
				if($v === $gk) {
					self::$ocount++;
					$allon[] = $v;
					switch($temp[3][$k]) {
						case 'Druid':
						case 'Elder Druid':
							$omatches['Druids'][$v] = $temp[2][$k];
							$ovocs[$v] = $temp[3][$k] ==='Druid' ? 'D' : 'ED';
						break;
						case 'Sorcerer':
						case 'Master Sorcerer':
							$omatches['Sorcerers'][$v] = $temp[2][$k];
							$ovocs[$v] = $temp[3][$k]==='Sorcerer' ? 'S' : 'MS';
						break;
						case 'Paladin':
						case 'Royal Paladin':
							$omatches['Paladins'][$v] = $temp[2][$k];
							$ovocs[$v] = $temp[3][$k]==='Paladin' ? 'P' : 'RP';
						break;
						case 'Knight':
						case 'Elite Knight':
							$omatches['Knights'][$v] = $temp[2][$k];
							$ovocs[$v] = $temp[3][$k]==='Knight' ? 'K' : 'EK';
						break;
						default:
							$omatches['Rookies'][$v] = $temp[2][$k];
							$ovocs[$v] = 'N';
						break;
					}
				}
			}
		}
		if(empty($allon)) {
			if(self::$multi === false) throw new Exception("No players from ".self::$guild." online.");
			else throw new Exception("No players from ".implode(", ",self::$guild)." online.");
		}
	}
	
	private static function glink(&$name) {
		return "<a href=\"".self::$a_guild_esc.urlencode($name)."\">$name</a>";
	}
	
	private static function clink(&$name) {
		return "<a href=\"".self::$a_char_esc.urlencode($name)."\">$name</a>";
	}
	
	private static function get_dmg($level,$voc) {
		switch($voc) {
			case 'Druids':
			case 'Sorcerers':
				if($level<100) {
					if($level<45)
						return 0;
					else return floor($level*2.4);
				} else {
					if($level<150)
						return 250;
					else return floor(250+(($level-150)/15));
				}
			break;
			case 'Paladins':
				if($level<70) {
					if($level<32) 
						return 40; 
					else return floor($level*0.8);
				} else 
					return floor($level*1.3);
			break;
			case 'Knights':
				if($level<70) {
					if($level<32)
						return 0;
					else return floor($level*0.8);
				} else return floor($level*1.3);
			break;
			default:
				return 0;
			break;
		}
	}
	
	private static function get_icon($voc) {
		switch($voc) {
			case 'D':
			case 'ED':
			case 'Druids':
				return "<img src=\"img/ed.png\" width=\"12\" width=\"12\" alt=\"\" title=\"druid\" />";
			break;
			case 'S':
			case 'MS':
			case 'Sorcerers':
				return "<img src=\"img/ms.png\" width=\"12\" width=\"12\" alt=\"\" title=\"sorc\" />";
			break;
			case 'P':
			case 'RP':
			case 'Paladins':
				return "<img src=\"img/rp.png\" width=\"12\" width=\"12\" alt=\"\" title=\"pally\" />";
			break;
			case 'K':
			case 'EK':
			case 'Knights':
				return "<img src=\"img/ek.png\" width=\"12\" width=\"12\" alt=\"\" title=\"kina\" />";
			break;
			default:
				return "";
			break;
		}
	}
	
	private static function draw_table($omatches,$ovocs,$gmatches,$allon) {
		if(self::$style === 0) {							//CLASSIC
			$tcombo = array(0,0);
			$tlvl = 0;
			$damage = array("Knights"=>"Exori","Druids"=>"SD","Sorcerers"=>"SD","Paladins"=>"Dist + SD","Rookies"=>""); 
			self::$output .= "<table border=\"0\" width=\"720\" cellpadding=\"4\" cellspacing=\"1\">";
			foreach($omatches as $vocname => $vocarr) {
				if(sizeof($vocarr) > 0) {
					arsort($vocarr);
					$voccombo = 0;
					$dmg =& $damage[$vocname];
					self::$output .= "<tr class=\"hdr\"><td class=\"l\">$vocname</td><td class>Level</td><td>Voc</td><td>Guild</td><td>$dmg</td></tr>";	
					foreach($vocarr as $k => $v) {
						$dmg = self::get_dmg($v,$vocname);
						$voccombo += $dmg;
						self::$output .= "<tr onmouseover=\"cin(this);\" onmouseout=\"cout(this);\"><td class=\"l\">".self::clink($k)."</td><td>$v</td><td> ".$ovocs[$k]."</td><td>".self::glink($gmatches[$k])."</td><td>$dmg</td></tr>";
						$tlvl += $v;
					}
					$vocav = (int)(array_sum($vocarr)/count($vocarr));
					$tcombo[1] += $voccombo;
					
					
					if($vocname !== 'Knights') $tcombo[0] += $voccombo;
					self::$output .= "<tr class=\"sum\"><td></td><td>$vocav</td><td></td><td></td><td>$voccombo</td></tr>";
					self::$output .= "<tr class=\"fmt\"><td colspan=\"5\">&nbsp;</td></tr>";
				}
			}
			$tlvl = floor($tlvl/self::$ocount);
			self::$output .= "<tr class=\"hdr\"><td class=\"l\">Totals</td><td>Avg Level</td><td>Online</td><td>Combo w/o Knights</td><td>Total Combo</td></tr>";
			self::$output .= "<tr><td></td><td>$tlvl</td><td><strong>".self::$ocount." / ".sizeof($gmatches)."</strong></td><td>".$tcombo[0]."</td><td>".$tcombo[1]."</td></tr>";
			self::$output .= "</table>";
			
		} 
		else {											//VIP
		
			self::$output .= "<table border=0 cellspacing=0 cellpadding=0><tr height=\"15\"><td background=\"img/nw.png\" width=\"16\"></td><td background='img/n1.png'></td>";
			self::$output .= "<td width=\"16\" background=\"img/ne.png\"></td></tr><tr><td width=\"16\" background=\"img/w.png\"></td><td>";
			self::$output .= "<table border=0 cellspacing=0 cellpadding=0 class=\"cbg\">";
			foreach($omatches as $vocname => $vocarr) {
				if(sizeof($vocarr) > 0) {
					arsort($vocarr);
					foreach($vocarr as $k => $v) {
						$dmg = self::get_dmg($v,$vocname);
						$voccombo += $dmg;
						self::$output .= "<tr class=\"on\"><td>".self::get_icon($vocname)."</td><td class=\"l\">&nbsp;$k</td><td class=\"r\">&nbsp;&nbsp;$v&nbsp;</td><td class=\"l\"> ".$ovocs[$k]."</td><td class=\"l\">&nbsp;&nbsp;".$gmatches[$k]."</td></tr>";
					}
				}
			}
			self::$output .= "</table></td><td width=\"16\" background=\"img/e.png\">&nbsp;</td></tr>";
			self::$output .= "<tr height=\"15\"><td width=\"16\" background=\"img/sw.png\"></td><td background=\"img/s.png\"></td><td width=\"16\" background=\"img/se.png\"></td></tr></table>";
		}
	}
	
	public static function go() {
		self::getvars();
		self::$valid = self::checknames();
		try {
			if(self::$set === false) {
				self::append_header();
				#self::$output .= "<strong>*</strong> Specifying a server will make the lookup twice as fast.<br />";
				#self::$output .= "When keeping it automatic, server will update on the form after<br />the first time, ";
				#self::$output .= "so subsequent lookups will take less time.<br />";
				#self::append_footer();
			} else {
				if(!self::$valid) throw new Exception("No valid names entered.");
				
				if(!self::$multi) {								//SINGLE
					if(self::$server === '') {					//AUTO SERVER
						self::curlget($html[self::$guild]);
						self::parse_guild($html[self::$guild],self::$guild,$gmatches);
						self::curlget($html['--SERVER--'],true);
					} else {									//MAN SERVER
						self::multiget($html,true);
						self::parse_guild($html[self::$guild],self::$guild,$gmatches);
					}
				} else {										//MULTI
					if(self::$server === '') {					//AUTO SERVER
						;
						self::multiget($html);
						foreach(self::$guild as $g) {
							self::parse_guild($html[$g],$g,$gmatches);
						}
						self::curlget($html['--SERVER--'],true);
						
					} else {									//MAN SERVER
						self::multiget($html,true);
						foreach(self::$guild as $g) {
							self::parse_guild($html[$g],$g,$gmatches);
						}
					}
				}
				self::parse_match_online($html['--SERVER--'],$omatches,$ovocs,$gmatches,$allon);
				$html = null;
				self::append_header();
				self::draw_table($omatches,$ovocs,$gmatches,$allon);
				self::append_footer();
			}
		} catch (Exception $e) {
			self::append_header();
			$er = $e->getMessage();
			if(self::$style === 0) {
				self::$output .= "<table border=\"0\" width=\"720\" cellpadding=\"4\" cellspacing=\"1\">";
				self::$output .= "<tr class=\"hdr\"><td>Error</td></tr><tr><td>$er</td></tr></table><br />";
			} else {
				self::$output .= "<span class=\"err\">ERROR<br />";
				self::$output .= "$er</span><br /><br />";
			}
			self::append_footer();
		}
		echo self::$output;
	}
}
Gcheck::go();
?>