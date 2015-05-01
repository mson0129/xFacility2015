<?php
//xFacility2015
//XFMarkdown
//Studio2b
//Michael Son(mson0129@gmail.com)
//27APR2015(0.1.0.) - This file is newly created. Replacing block or inline does not work as your intention. Grammar is following GitHub Markdown rules basically. And some rules are added from NamuWiki(//namu.wiki).
//29APR2015(0.2.0.) - replaceEmoji() is added. Every country code is supported. If you use the new codes, type ":country_CountryCode:" such as ":country_us:".
//01MAY2015(0.2.1.) - All Emoji are supported.

class XFMarkdown extends XFObject {
	var $string;
	
	function __construct($string=NULL) {
		if(!is_null($string))
			$this->string = $string;
	}
	
	function replace($string=NULL) {
		if(is_null($string))
			$string = $this->string;
		$return = $this->replaceBlock($string);
		$return = $this->replaceInline($return);
		$return = $this->replaceEmoji($return);
		return $return;
	}
	
	function replaceBlock($string) {
		if(is_null($string))
			$string = $this->string;
		$string = str_replace("\r\n", "\n", $string);
		$arr = explode("\n", $string);
		foreach($arr as $lineNo => $lineVal) {
			if(substr(trim($lineVal), 0, 3)=="```" || $mode[$lineNo-1]=="pre") {
				if(substr(trim($lineVal), 0, 3)=="```" xor $mode[$lineNo-1]=="pre") {
					$mode[$lineNo] = "pre";
				}
				$lineVal = substr(trim($lineVal), 0, 3)=="```"?(strpos(trim($lineVal), " ")===false?NULL:ltrim(substr(trim($lineVal), strpos(trim($lineVal), " ")))):$lineVal;
			} else if(substr(trim($lineVal), 0, 1)=="#") {
				$header = 0;
				for($i=0; $i<6; $i++) {
					//Block
					if(substr(trim($lineVal), $i, 1)=="#") {
						$header++;
					} else {
						break;
					}
				}
				$mode[$lineNo] .= "h".$header;
				$lineVal = ltrim(substr(trim($lineVal), $header));
			} else if(substr(trim($lineVal), 0, 2)=="* ") {
				$mode[$lineNo] = "ul";
			} else if(strpos($lineVal, ". ")!==false && is_numeric(substr(trim($lineVal), 0, strpos($lineVal, ". ")))) {
				$mode[$lineNo] = "ol";
			} else if(substr(trim($lineVal), 0, 1)==">") {
				$mode[$lineNo] = "blockquote";
				$lineVal = ltrim(substr(trim($lineVal), 1));
			} else if(strlen(trim($lineVal))>0) {
				$mode[$lineNo] = "p";
				$lineVal = trim($lineVal);
			}
				
			if($mode[$lineNo-1]!=$mode[$lineNo]) {
				if(!is_null($mode[$lineNo-1]))
					$return .= sprintf("</%s>", $mode[$lineNo-1]);
				$return .= "\n";
				if(!is_null($mode[$lineNo]))
					$return .= sprintf("<%s>", $mode[$lineNo]);
			} else if($mode[$lineNo]=="pre" || is_null($mode[$lineNo])) {
				$return .= "\n";
			} else {
				$return .= "<br />\n";
			}
				
			$return .= $lineVal;
		}
		if(!is_null($mode[count($arr)-1]))
			$return .= sprintf("</%s>", $mode[count($arr)-1]);
	
		return $return;
	}
	
	function replaceInline($string) {
		if(is_null($string))
			$string = $this->string;
		//Image
		$patterns[] = '/([^\\\\]!|^!)\[([^]]*)\]\(([^)]+)\)/';
		$replacements[] = "<a href='$3' target='_blank'><img src='$3' alt='$2' /></a>";
		//Link
		$patterns[] = '/[^\\\\]\[([^]]*)\]\(([^)]+)\)/';
		$replacements[] = "<a href='$2'>$1</a>";
		//$patterns[] = '/(([^\'\"]\/\/|^\/\/)((\w*):?(\w*)@)?([\w\.]+):?(\d{0,5})\/?([^\s\'\"]*))/';
		//$replacements[] = "<a href='$1'>$1</a>";
		//Underlined
		$patterns[] = '/__((?(?=__)[^_]|.)*)__/';
		$replacements[] = "<u>$1</u>";
				//Deleted
				$patterns[] = '/~~((?(?=~~)[^~]|.)*)~~/';
				$replacements[] = "<del>$1</del>";
				//Bold Italic
				$patterns[] = '/\*\*\*((?(?=\*\*\*)[^~]|.)*)\*\*\*/';
				$replacements[] = "<b><i>$1</i></b>";
				//Bold
				$patterns[] = '/\*\*((?(?=\*\*)[^~]|.)*)\*\*/';
				$replacements[] = "<b>$1</b>";
				//Italic
				$patterns[] = '/\*([^\*]+)\*/';
				$replacements[] = "<i>$1</i>";
				//Checkbox
				$patterns[] = '/<li[^>]*>(\[x\])/';
				$replacements[] = "<input type='checkbox' checked disabled>";
				$patterns[] = '/<li[^>]*>(\[\s\])/';
				$replacements[] = "<input type='checkbox' disabled>";
				//url
				$return = preg_replace($patterns, $replacements, $string);
				return str_replace("\\", "", $return);
	}
	
	function replaceEmoji($string) {
		$emoji = array(
				//People
				//":bowtie:"=>"", //Non-standard
				":smile:"=>"U+1F604", //😄
				":laughing:"=>"U+1F606", //😆
				":blush:"=>"U+1F60A", //😊
				":smiley:"=>"U+1F603", //😃
				":relaxed:"=>"U+263AU+FE0F", //☺️
				":smirk:"=>"U+1F60F", //😏
				":heart_eyes:"=>"U+1F60D", //😍
				":kissing_heart:"=>"U+1F618", //😘
				":kissing_closed_eyes:"=>"U+1F61A", //😚
				":flushed:"=>"U+1F633", //😳
				":relieved:"=>"U+1F60C", //😌
				":satisfied:"=>"U+1F606", //:laughing:=😆
				":grin:"=>"U+1F601", //😁
				":wink:"=>"U+1F609", //😉
				":stuck_out_tongue_winking_eye:"=>"U+1F61C", //😜
				":stuck_out_tongue_closed_eyes:"=>"U+1F61D", //😝
				":grinning:"=>"U+1F600", //😀
				":kissing:"=>"U+1F617", //😗
				":kissing_smiling_eyes:"=>"U+1F619", //😙
				":stuck_out_tongue:"=>"U+1F61B", //😛
				":sleeping:"=>"U+1F634", //😴
				":worried:"=>"U+1F61F", //😟
				":frowning:"=>"U+1F626", //😦
				":anguished:"=>"U+1F627", //😧
				":open_mouth:"=>"U+1F62E", //😮
				":grimacing:"=>"U+1F62C", //😬
				":confused:"=>"U+1F615", //😕
				":hushed:"=>"U+1F62F", //😯
				":expressionless:"=>"U+1F611", //😑
				":unamused:"=>"U+1F612", //😒
				":sweat_smile:"=>"U+1F605", //😅
				":sweat:"=>"U+1F613", //😓
				":disappointed_relieved:"=>"U+1F625", //😥
				":weary:"=>"U+1F629", //😩
				":pensive:"=>"U+1F614", //😔
				":disappointed:"=>"U+1F61E", //😞
				":confounded:"=>"U+1F616", //😖
				":fearful:"=>"U+1F628", //😨
				":cold_sweat:"=>"U+1F630", //😰
				":persevere:"=>"U+1F623", //😣
				":cry:"=>"U+1F622", //😢
				":sob:"=>"U+1F62D", //😭
				":joy:"=>"U+1F602", //😂
				":astonished:"=>"U+1F632", //😲
				":scream:"=>"U+1F631", //😱
				//":neckbeard:"=>"", //Non-standard
				":tired_face:"=>"U+1F62B", //😫
				":angry:"=>"U+1F620", //😠
				":rage:"=>"U+1F621", //😡
				":triumph:"=>"U+1F624", //😤
				":sleepy:"=>"U+1F62A", //😪
				":yum:"=>"U+1F60B", //😋
				":mask:"=>"U+1F637", //😷
				":sunglasses:"=>"U+1F60E", //😎
				":dizzy_face:"=>"U+1F635", //😵
				":imp:"=>"U+1F47F", //👿
				":smiling_imp:"=>"U+1F608", //😈
				":neutral_face:"=>"U+1F610", //😐
				":no_mouth:"=>"U+1F636", //😶
				":innocent:"=>"U+1F607", //😇
				":alien:"=>"U+1F47D", //👽
				":yellow_heart:"=>"U+1F49B", //💛
				":blue_heart:"=>"U+1F499", //💙
				":purple_heart:"=>"U+1F49C", //💜
				":heart:"=>"U+2764U+FE0F", //❤️
				":green_heart:"=>"U+1F49A", //💚
				":broken_heart:"=>"U+1F494", //💔
				":heartbeat:"=>"U+1F493", //💓
				":heartpulse:"=>"U+1F497", //💗
				":two_hearts:"=>"U+1F495", //💕
				":revolving_hearts:"=>"U+1F49E", //💞
				":cupid:"=>"U+1F498", //💘
				":sparkling_heart:"=>"U+1F496", //💖
				":sparkles:"=>"U+2728", //✨
				":star:"=>"U+2B50U+FE0F", //⭐️
				":star2:"=>"U+1F31F", //🌟
				":dizzy:"=>"U+1F4AB", //💫
				":boom:"=>"U+1F4A5", //:collision:=💥
				":collision:"=>"U+1F4A5", //💥
				":anger:"=>"U+1F4A2", //💢
				":exclamation:"=>"U+2757", //❗
				":question:"=>"U+2753", //❓
				":grey_exclamation:"=>"U+2755", //❕
				":grey_question:"=>"U+2754", //❔
				":zzz:"=>"U+1F4A4", //sleeping symbol=💤
				":dash:"=>"U+1F4A8", //💨
				":sweat_drops:"=>"U+1F4A6", //💦
				":notes:"=>"U+1F3B6", //🎶
				":musical_note:"=>"U+1F3B5", //🎵
				":fire:"=>"U+1F525", //🔥
				":hankey:"=>"U+1F4A9", //💩
				":poop:"=>"U+1F4A9", //pile of poo=💩
				":shit:"=>"U+1F4A9", //💩
				":+1:"=>"U+1F44D", //👍
				":thumbsup:"=>"U+1F44D", //:+1:=👍
				":-1:"=>"U+1F44E", //👎
				":thumbsdown:"=>"U+1F44E", //:-1:=👎
				":ok_hand:"=>"U+1F44C", //👌
				":punch:"=>"U+1F44A", //👊
				":facepunch:"=>"U+1F44A", //:punch:=👊
				":fist:"=>"U+270A", //✊
				":v:"=>"U+270CU+FE0F", //✌️
				":wave:"=>"U+1F44B", //👋
				":hand:"=>"U+270B", //:raised_hand:=✋
				":raised_hand:"=>"U+270B", //✋
				":open_hands:"=>"U+1F450", //👐
				":point_up:"=>"U+261DU+FE0F", //☝️
				":point_down:"=>"U+1F447", //👇
				":point_left:"=>"U+1F448", //👈
				":point_right:"=>"U+1F449", //👉
				":raised_hands:"=>"U+1F64C", //🙌
				":pray:"=>"U+1F64F", //🙏
				":point_up_2:"=>"U+1F446", //👆
				":clap:"=>"U+1F44F", //👏
				":muscle:"=>"U+1F4AA", //💪
				//":metal:"=>"", //Non-standard
				//":fu:"=>"", //Non-standard
				":walking:"=>"U+1F6B6", //🚶
				":runner:"=>"U+1F3C3", //🏃
				":running:"=>"U+1F3C3", //🏃
				":couple:"=>"U+1F46B", //👫
				":family:"=>"U+1F46A", //👪
				":two_men_holding_hands:"=>"U+1F46C", //👬
				":two_women_holding_hands:"=>"U+1F46D", //👭
				":dancer:"=>"U+1F483", //💃
				":dancers:"=>"U+1F46F", //👯
				":ok_woman:"=>"U+1F646", //🙆
				":no_good:"=>"U+1F645", //🙅
				":information_desk_person:"=>"U+1F481", //💁
				":raising_hand:"=>"U+1F64B", //🙋
				":bride_with_veil:"=>"U+1F470", //👰
				":person_with_pouting_face:"=>"U+1F64E", //🙎
				":person_frowning:"=>"U+1F64D", //🙍
				":bow:"=>"U+1F647", //🙇
				":couplekiss:"=>"U+1F48F", //💏
				":couple_with_heart:"=>"U+1F491", //💑
				":massage:"=>"U+1F486", //💆
				":haircut:"=>"U+1F487", //💇
				":nail_care:"=>"U+1F485", //💅
				":boy:"=>"U+1F466", //👦
				":girl:"=>"U+1F467", //👧
				":woman:"=>"U+1F469", //👩
				":man:"=>"U+1F468", //👨
				":baby:"=>"U+1F476", //👶
				":older_woman:"=>"U+1F475", //👵
				":older_man:"=>"U+1F474", //👴
				":person_with_blond_hair:"=>"U+1F471", //👱
				":man_with_gua_pi_mao:"=>"U+1F472", //👲
				":man_with_turban:"=>"U+1F473", //👳
				":construction_worker:"=>"U+1F477", //👷
				":cop:"=>"U+1F46E", //👮
				":angel:"=>"U+1F47C", //👼
				":princess:"=>"U+1F478", //👸
				":smiley_cat:"=>"U+1F63A", //😺
				":smile_cat:"=>"U+1F638", //😸
				":heart_eyes_cat:"=>"U+1F63B", //😻
				":kissing_cat:"=>"U+1F63D", //😽
				":smirk_cat:"=>"U+1F63C", //😼
				":scream_cat:"=>"U+1F640", //🙀
				":crying_cat_face:"=>"U+1F63F", //😿
				":joy_cat:"=>"U+1F639", //😹
				":pouting_cat:"=>"U+1F63E", //😾
				":japanese_ogre:"=>"U+1F479", //👹
				":japanese_goblin:"=>"U+1F47A", //👺
				":see_no_evil:"=>"U+1F648", //🙈
				":hear_no_evil:"=>"U+1F649", //🙉
				":speak_no_evil:"=>"U+1F64A", //🙊
				":guardsman:"=>"U+1F482", //💂
				":skull:"=>"U+1F480", //💀
				":feet:"=>"U+1F463", //👣
				":lips:"=>"U+1F444", //👄
				":kiss:"=>"U+1F48B", //💋
				":droplet:"=>"U+1F4A7", //💧
				":ear:"=>"U+1F442", //👂
				":eyes:"=>"U+1F440", //👀
				":nose:"=>"U+1F443", //👃
				":tongue:"=>"U+1F445", //👅
				":love_letter:"=>"U+1F48C", //💌
				":bust_in_silhouette:"=>"U+1F464", //👤
				":busts_in_silhouette:"=>"U+1F465", //👥
				":speech_balloon:"=>"U+1F4AC", //💬
				":thought_balloon:"=>"U+1F4AD", //💭
				//":feelsgood:"=>"", //Non-standard
				//":finnadie:"=>"", //Non-standard
				//":goberserk:"=>"", //Non-standard
				//":godmode:"=>"", //Non-standard
				//":hurtrealbad:"=>"", //Non-standard
				//":rage1:"=>"", //Non-standard
				//":rage2:"=>"", //Non-standard
				//":rage3:"=>"", //Non-standard
				//":rage4:"=>"", //Non-standard
				//":suspect:"=>"", //Non-standard
				//":trollface:"=>"", //Non-standard
					
				//Nature
				":sunny:"=>"U+2600U+FE0F", //☀️
				":umbrella:"=>"U+2614U+FE0F", //☔️
				":cloud:"=>"U+2601U+FE0F", //☁️
				":snowflake:"=>"U+2744U+FE0F", //❄️
				":snowman:"=>"U+26C4U+FE0F", //⛄️
				":zap:"=>"U+26A1U+FE0F", //⚡️
				":cyclone:"=>"U+1F300", //🌀
				":foggy:"=>"U+1F301", //🌁
				":ocean:"=>"U+1F30A", //water wave=🌊
				":cat:"=>"U+1F431", //🐱
				":dog:"=>"U+1F436", //🐶
				":mouse:"=>"U+1F42D", //🐭
				":hamster:"=>"U+1F439", //🐹
				":rabbit:"=>"U+1F430", //🐰
				":wolf:"=>"U+1F43A", //🐺
				":frog:"=>"U+1F438", //🐸
				":tiger:"=>"U+1F42F", //🐯
				":koala:"=>"U+1F428", //🐨
				":bear:"=>"U+1F43B", //🐻
				":pig:"=>"U+1F437", //🐷
				":pig_nose:"=>"U+1F43D", //🐽
				":cow:"=>"U+1F42E", //🐮
				":boar:"=>"U+1F417", //🐗
				":monkey_face:"=>"U+1F435", //🐵
				":monkey:"=>"U+1F412", //🐒
				":horse:"=>"U+1F434", //🐴
				":racehorse:"=>"U+1F40E", //🐎
				":camel:"=>"U+1F42B", //🐫
				":sheep:"=>"U+1F411", //🐑
				":elephant:"=>"U+1F418", //🐘
				":panda_face:"=>"U+1F43C", //🐼
				":snake:"=>"U+1F40D", //🐍
				":bird:"=>"U+1F426", //🐦
				":baby_chick:"=>"U+1F424", //🐤
				":hatched_chick:"=>"U+1F425", //🐥
				":hatching_chick:"=>"U+1F423", //🐣
				":chicken:"=>"U+1F414", //🐔
				":penguin:"=>"U+1F427", //🐧
				":turtle:"=>"U+1F422", //🐢
				":bug:"=>"U+1F41B", //🐛
				":honeybee:"=>"U+1F41D", //🐝
				":ant:"=>"U+1F41C", //🐜
				":beetle:"=>"U+1F41E", //🐞
				":snail:"=>"U+1F40C", //🐌
				":octopus:"=>"U+1F419", //🐙
				":tropical_fish:"=>"U+1F420", //🐠
				":fish:"=>"U+1F41F", //🐟
				":whale:"=>"U+1F433", //🐳
				":whale2:"=>"U+1F40B", //🐋
				":dolphin:"=>"U+1F42C", //🐬
				":cow2:"=>"U+1F404", //🐄
				":ram:"=>"U+1F40F", //🐏
				":rat:"=>"U+1F400", //🐀
				":water_buffalo:"=>"U+1F403", //🐃
				":tiger2:"=>"U+1F405", //🐅
				":rabbit2:"=>"U+1F407", //🐇
				":dragon:"=>"U+1F409", //🐉
				":goat:"=>"U+1F410", //🐐
				":rooster:"=>"U+1F413", //🐓
				":dog2:"=>"U+1F415", //🐕
				":pig2:"=>"U+1F416", //🐖
				":mouse2:"=>"U+1F401", //🐁
				":ox:"=>"U+1F402", //🐂
				":dragon_face:"=>"U+1F432", //🐲
				":blowfish:"=>"U+1F421", //🐡
				":crocodile:"=>"U+1F40A", //🐊
				":dromedary_camel:"=>"U+1F42A", //🐪
				":leopard:"=>"U+1F406", //🐆
				":cat2:"=>"U+1F408", //🐈
				":poodle:"=>"U+1F429", //🐩
				":paw_prints:"=>"U+1F43E", //🐾
				":bouquet:"=>"U+1F490", //💐
				":cherry_blossom:"=>"U+1F338", //🌸
				":tulip:"=>"U+1F337", //🌷
				":four_leaf_clover:"=>"U+1F340", //🍀
				":rose:"=>"U+1F339", //🌹
				":sunflower:"=>"U+1F33B", //🌻
				":hibiscus:"=>"U+1F33A", //🌺
				":maple_leaf:"=>"U+1F341", //🍁
				":leaves:"=>"U+1F343", //🍃
				":fallen_leaf:"=>"U+1F342", //🍂
				":herb:"=>"U+1F33F", //🌿
				":mushroom:"=>"U+1F344", //🍄
				":cactus:"=>"U+1F335", //🌵
				":palm_tree:"=>"U+1F334", //🌴
				":evergreen_tree:"=>"U+1F332", //🌲
				":deciduous_tree:"=>"U+1F333", //🌳
				":chestnut:"=>"U+1F330", //🌰
				":seedling:"=>"U+1F331", //🌱
				":blossom:"=>"U+1F33C", //🌼
				":ear_of_rice:"=>"U+1F33E", //🌾
				":shell:"=>"U+1F41A", //🐚
				":globe_with_meridians:"=>"", //
				":sun_with_face:"=>"U+1F31E", //🌞
				":full_moon_with_face:"=>"U+1F31D", //🌝
				":new_moon_with_face:"=>"U+1F31A", //🌚
				":new_moon:"=>"U+1F311", //🌑
				":waxing_crescent_moon:"=>"U+1F312", //🌒
				":first_quarter_moon:"=>"U+1F313", //🌓
				":waxing_gibbous_moon:"=>"U+1F314", //🌔
				":full_moon:"=>"U+1F315", //🌕
				":waning_gibbous_moon:"=>"U+1F316", //🌖
				":last_quarter_moon:"=>"U+1F317", //🌗
				":waning_crescent_moon:"=>"U+1F318", //🌘
				":last_quarter_moon_with_face:"=>"U+1F31C", //🌜
				":first_quarter_moon_with_face:"=>"U+1F31B", //🌛
				":crescent_moon:"=>"U+1F319", //🌙
				":earth_africa:"=>"U+1F30D", //🌍
				":earth_americas:"=>"U+1F30E", //🌎
				":earth_asia:"=>"U+1F30F", //🌏
				":volcano:"=>"U+1F30B", //🌋
				":milky_way:"=>"U+1F30C", //🌌
				":partly_sunny:"=>"U+26C5U+FE0F", //⛅️
				//":octocat:"=>"", //Non-standard
				//":squirrel:"=>"", //Non-standard
					
				//Objects
				":bamboo:"=>"U+1F38D", //🎍
				":gift_heart:"=>"U+1F49D", //💝
				":dolls:"=>"U+1F38E", //🎎
				":school_satchel:"=>"U+1F392", //🎒
				":mortar_board:"=>"U+1F393", //🎓
				":flags:"=>"U+1F38F", //🎏
				":fireworks:"=>"U+1F386", //🎆
				":sparkler:"=>"U+1F387", //🎇
				":wind_chime:"=>"U+1F390", //🎐
				":rice_scene:"=>"U+1F391", //🎑
				":jack_o_lantern:"=>"U+1F383", //🎃
				":ghost:"=>"U+1F47B", //👻
				":santa:"=>"U+1F385", //🎅
				":christmas_tree:"=>"U+1F384", //🎄
				":gift:"=>"U+1F381", //🎁
				":bell:"=>"U+1F514", //🔔
				":no_bell:"=>"U+1F515", //🔕
				":tanabata_tree:"=>"U+1F38B", //🎋
				":tada:"=>"U+1F389", //🎉
				":confetti_ball:"=>"U+1F38A", //🎊
				":balloon:"=>"U+1F388", //🎈
				":crystal_ball:"=>"U+1F52E", //🔮
				":cd:"=>"U+1F4BF", //💿
				":dvd:"=>"U+1F4C0", //📀
				":floppy_disk:"=>"U+1F4BE", //💾
				":camera:"=>"U+1F4F7", //📷
				":video_camera:"=>"U+1F4F9", //📹
				":movie_camera:"=>"U+1F3A5", //🎥
				":computer:"=>"U+1F4BB", //💻
				":tv:"=>"U+1F4FA", //📺
				":iphone:"=>"U+1F4F1", //📱
				":phone:"=>"U+260EU+FE0F", //:telephone:=☎️
				":telephone:"=>"U+260EU+FE0F", //☎️
				":telephone_receiver:"=>"U+1F4DE", //📞
				":pager:"=>"U+1F4DF", //📟
				":fax:"=>"U+1F4E0", //📠
				":minidisc:"=>"U+1F4BD", //💽
				":vhs:"=>"U+1F4FC", //📼
				":sound:"=>"U+1F509", //🔉
				":speaker:"=>"U+1F4FC", //🔈
				":mute:"=>"U+1F507", //🔇
				":loudspeaker:"=>"U+1F4E2", //📢
				":mega:"=>"U+1F4E3", //📣
				":hourglass:"=>"U+231BU+FE0F", //⌛️
				":hourglass_flowing_sand:"=>"U+23F3", //⏳
				":alarm_clock:"=>"U+23F0", //⏰
				":watch:"=>"U+231AU+FE0F", //⌚️
				":radio:"=>"U+1F4FB", //📻
				":satellite:"=>"U+1F4E1", //📡
				":loop:"=>"U+27BF", //➿
				":mag:"=>"U+1F50D", //🔍
				":mag_right:"=>"U+1F50E", //🔎
				":unlock:"=>"U+1F513", //🔓
				":lock:"=>"U+1F512", //🔒
				":lock_with_ink_pen:"=>"U+1F50F", //🔏
				":closed_lock_with_key:"=>"U+1F510", //🔐
				":key:"=>"U+1F511", //🔑
				":bulb:"=>"U+1F4A1", //💡
				":flashlight:"=>"U+1F526", //electronic torch=🔦
				":high_brightness:"=>"U+1F506", //🔆
				":low_brightness:"=>"U+1F505", //🔅
				":electric_plug:"=>"U+1F50C", //🔌
				":battery:"=>"U+1F50B", //🔋
				":calling:"=>"U+1F4F2", //📲
				":email:"=>"U+1F4E9", //📩
				":mailbox:"=>"U+1F4EB", //📫
				":postbox:"=>"U+1F4EE", //📮
				":bath:"=>"U+1F6C0", //🛀
				":bathtub:"=>"U+1F6C1", //🛁
				":shower:"=>"U+1F6BF", //🚿
				":toilet:"=>"U+1F6BD", //🚽
				":wrench:"=>"U+1F527", //🔧
				":nut_and_bolt:"=>"U+1F529", //🔩
				":hammer:"=>"U+1F528", //🔨
				":seat:"=>"U+1F4BA", //💺
				":moneybag:"=>"U+1F4B0", //💰
				":yen:"=>"U+1F4B4", //💴
				":dollar:"=>"U+1F4B5", //💵
				":pound:"=>"U+1F4B7", //💷
				":euro:"=>"U+1F4B6", //💶
				":credit_card:"=>"U+1F4B3", //💳
				":money_with_wings:"=>"U+1F4B8", //💸
				":e-mail:"=>"U+1F4E7", //📧
				":inbox_tray:"=>"U+1F4E5", //📥
				":outbox_tray:"=>"U+1F4E4", //📤
				":envelope:"=>"U+2709 U+FE0F", //✉️
				":incoming_envelope:"=>"U+1F4E8", //📨
				":postal_horn:"=>"U+1F4EF", //📯
				":mailbox_closed:"=>"U+1F4EA", //📪
				":mailbox_with_mail:"=>"U+1F4EC", //📬
				":mailbox_with_no_mail:"=>"U+1F4ED", //📭
				":package:"=>"U+1F4E6", //📦
				":door:"=>"U+1F6AA", //🚪
				":smoking:"=>"U+1F6AC", //🚬
				":bomb:"=>"U+1F4A3", //💣
				":gun:"=>"U+1F52B", //pistol=🔫
				":hocho:"=>"U+1F52A", //🔪
				":pill:"=>"U+1F48A", //💊
				":syringe:"=>"U+1F489", //💉
				":page_facing_up:"=>"U+1F4C4", //📄
				":page_with_curl:"=>"U+1F4C3", //📃
				":bookmark_tabs:"=>"U+1F4D1", //📑
				":bar_chart:"=>"U+1F4CA", //📊
				":chart_with_upwards_trend:"=>"U+1F4C8", //📈
				":chart_with_downwards_trend:"=>"U+1F4C9", //📉
				":scroll:"=>"U+1F4DC", //📜
				":clipboard:"=>"U+1F4CB", //📋
				":calendar:"=>"U+1F4C6", //📆
				":date:"=>"U+1F4C5", //📅
				":card_index:"=>"U+1F4C7", //📇
				":file_folder:"=>"U+1F4C1", //📁
				":open_file_folder:"=>"U+1F4C2", //📂
				":scissors:"=>"U+2702U+FE0F", //✂️
				":pushpin:"=>"U+1F4CC", //📌
				":paperclip:"=>"U+1F4CE", //📎
				":black_nib:"=>"U+2712U+FE0F", //✒️
				":pencil2:"=>"U+270FU+FE0F", //✏️
				":straight_ruler:"=>"U+1F4CF", //📏
				":triangular_ruler:"=>"U+1F4D0", //📐
				":closed_book:"=>"U+1F4D5", //📕
				":green_book:"=>"U+1F4D7", //📗
				":blue_book:"=>"U+1F4D8", //📘
				":orange_book:"=>"U+1F4D9", //📙
				":notebook:"=>"U+1F4D3", //📓
				":notebook_with_decorative_cover:"=>"U+1F4D4", //📔
				":ledger:"=>"U+1F4D2", //📒
				":books:"=>"U+1F4DA", //📚
				":bookmark:"=>"U+1F516", //🔖
				":name_badge:"=>"U+1F4DB", //📛
				":microscope:"=>"U+1F52C", //🔬
				":telescope:"=>"U+1F52D", //🔭
				":newspaper:"=>"U+1F4F0", //📰

				//Object - Playing
				":football:"=>"U+1F3C8", //🏈
				":basketball:"=>"U+1F3C0", //🏀
				":soccer:"=>"U+26BDU+FE0F", //⚽️
				":baseball:"=>"U+26BEU+FE0F", //⚾️
				":tennis:"=>"U+1F3BE", //🎾
				":8ball:"=>"U+1F3B1", //billiards=🎱
				":rugby_football:"=>"U+1F3C9", //🏉
				":bowling:"=>"U+1F3B3", //🎳
				":golf:"=>"U+26F3U+FE0F", //flag in hole=⛳️
				":mountain_bicyclist:"=>"U+1F6B5", //🚵
				":bicyclist:"=>"U+1F6B4", //🚴
				":horse_racing:"=>"U+1F3C7", //🏇
				":snowboarder:"=>"U+1F3C2", //🏂
				":swimmer:"=>"U+1F3CA", //🏊
				":surfer:"=>"U+1F3C4", //🏄
				":ski:"=>"U+1F3BF", //🎿
				":spades:"=>"U+2660U+FE0F", //♠️
				":hearts:"=>"U+2665U+FE0F", //♥️
				":clubs:"=>"U+2663U+FE0F", //♣️
				":diamonds:"=>"U+2666U+FE0F", //♦️
				":gem:"=>"U+1F48E", //💎
				":ring:"=>"U+1F48D", //💍
				":trophy:"=>"U+1F3C6", //🏆
				":musical_score:"=>"U+1F3BC", //🎼
				":musical_keyboard:"=>"U+1F3B9", //🎹
				":violin:"=>"U+1F3BB", //🎻
				":space_invader:"=>"U+1F47E", //alien monster=👾
				":video_game:"=>"U+1F3AE", //🎮
				":black_joker:"=>"U+1F0CF", //🃏
				":flower_playing_cards:"=>"U+1F3B4", //🎴
				":game_die:"=>"U+1F3B2", //🎲
				":dart:"=>"U+1F3AF", //direct hit=🎯
				":mahjong:"=>"U+1F004U+FE0F", //🀄️

				":clapper:"=>"U+1F3AC", //🎬
				":memo:"=>"U+1F4DD", //📝
				":pencil:"=>"U+1F4DD", //memo=📝
				":book:"=>"U+1F4D6", //📖
				":art:"=>"U+1F3A8", //artist palette=🎨
				":microphone:"=>"U+1F3A4", //🎤
				":headphones:"=>"U+1F3A7", //🎧
				":trumpet:"=>"U+1F3BA", //🎺
				":saxophone:"=>"U+1F3B7", //🎷
				":guitar:"=>"U+1F3B8", //🎸
				
				//Clothes
				":shoe:"=>"U+1F45F", //athletic shoe=👟
				":sandal:"=>"U+1F461", //👡
				":high_heel:"=>"U+1F460", //👠
				":lipstick:"=>"U+1F484", //💄
				":boot:"=>"U+1F462", //👢
				":shirt:"=>"U+1F455", //:tshirt:=👕
				":tshirt:"=>"U+1F455", //👕
				":necktie:"=>"U+1F454", //👔
				":womans_clothes:"=>"U+1F45A", //👚
				":dress:"=>"U+1F457", //👗
				":running_shirt_with_sash:"=>"U+1F3BD", //🎽
				":jeans:"=>"U+1F456", //👖
				":kimono:"=>"U+1F458", //👘
				":bikini:"=>"U+1F459", //👙
				":ribbon:"=>"U+1F380", //🎀
				":tophat:"=>"U+1F3A9", //🎩
				":crown:"=>"U+1F451", //👑
				":womans_hat:"=>"U+1F452", //👒
				":mans_shoe:"=>"U+1F45E", //👞
				":closed_umbrella:"=>"U+1F302", //🌂
				":briefcase:"=>"U+1F4BC", //💼
				":handbag:"=>"U+1F45C", //👜
				":pouch:"=>"U+1F45D", //👝
				":purse:"=>"U+1F45B", //👛
				":eyeglasses:"=>"U+1F453", //👓

				//Foods
				":fishing_pole_and_fish:"=>"U+1F3A3", //🎣
				":coffee:"=>"U+2615U+FE0F", //hot beverage=☕️
				":tea:"=>"U+1F375", //🍵
				":sake:"=>"U+1F376", //🍶
				":baby_bottle:"=>"U+1F37C", //🍼
				":beer:"=>"U+1F37A", //🍺
				":beers:"=>"U+1F37B", //🍻
				":cocktail:"=>"U+1F378", //🍸
				":tropical_drink:"=>"U+1F379", //🍹
				":wine_glass:"=>"U+1F377", //🍷
				":fork_and_knife:"=>"U+1F374", //🍴
				":pizza:"=>"U+1F355", //🍕
				":hamburger:"=>"U+1F354", //🍔
				":fries:"=>"U+1F35F", //🍟
				":poultry_leg:"=>"U+1F357", //🍗
				":meat_on_bone:"=>"U+1F356", //🍖
				":spaghetti:"=>"U+1F35D", //🍝
				":curry:"=>"U+1F35B", //🍛
				":fried_shrimp:"=>"U+1F364", //🍤
				":bento:"=>"U+1F371", //🍱
				":sushi:"=>"U+1F363", //🍣
				":fish_cake:"=>"U+1F365", //🍥
				":rice_ball:"=>"U+1F359", //🍙
				":rice_cracker:"=>"U+1F358", //🍘
				":rice:"=>"U+1F35A", //🍚
				":ramen:"=>"U+1F35C", //steaming bowl=🍜
				":stew:"=>"U+1F372", //pod of food=🍲
				":oden:"=>"U+1F362", //🍢
				":dango:"=>"U+1F361", //🍡
				":egg:"=>"U+1F373", //cooking=🍳
				":bread:"=>"U+1F35E", //🍞
				":doughnut:"=>"U+1F369", //🍩
				":custard:"=>"U+1F36E", //🍮
				":icecream:"=>"U+1F366", //🍦
				":ice_cream:"=>"U+1F368", //🍨
				":shaved_ice:"=>"U+1F367", //🍧
				":birthday:"=>"U+1F382", //🎂
				":cake:"=>"U+1F370", //🍰
				":cookie:"=>"U+1F36A", //🍪
				":chocolate_bar:"=>"U+1F36B", //🍫
				":candy:"=>"U+1F36C", //🍬
				":lollipop:"=>"U+1F36D", //🍭
				":honey_pot:"=>"U+1F36F", //🍯
				":apple:"=>"U+1F34E", //🍎
				":green_apple:"=>"U+1F34F", //🍏
				":tangerine:"=>"U+1F34A", //🍊
				":lemon:"=>"U+1F34B", //🍋
				":cherries:"=>"U+1F352", //🍒
				":grapes:"=>"U+1F347", //🍇
				":watermelon:"=>"U+1F349", //🍉
				":strawberry:"=>"U+1F353", //🍓
				":peach:"=>"U+1F351", //🍑
				":melon:"=>"U+1F348", //🍈
				":banana:"=>"U+1F34C", //🍌
				":pear:"=>"U+1F350", //🍐
				":pineapple:"=>"U+1F34D", //🍍
				":sweet_potato:"=>"U+1F360", //🍠
				":eggplant:"=>"U+1F346", //aubergine=🍆
				":tomato:"=>"U+1F345", //🍅
				":corn:"=>"U+1F33D", //ear of maize=🌽
					
				//Places
				":house:"=>"U+1F3E0", //🏠
				":house_with_garden:"=>"U+1F3E1", //🏡
				":school:"=>"U+1F3EB", //🏫
				":office:"=>"U+1F3E2", //🏢
				":post_office:"=>"U+1F3E3", //🏣
				":hospital:"=>"U+1F3E5", //🏥
				":bank:"=>"U+1F3E6", //🏦
				":convenience_store:"=>"U+1F3EA", //🏪
				":love_hotel:"=>"U+1F3E9", //🏩
				":hotel:"=>"U+1F3E8", //🏨
				":wedding:"=>"U+1F492", //💒
				":church:"=>"U+26EAU+FE0F", //⛪️
				":department_store:"=>"U+1F3EC", //🏬
				":european_post_office:"=>"U+1F3E4", //🏤
				":city_sunrise:"=>"U+1F307", //🌇
				":city_sunset:"=>"U+1F306", //🌆
				":japanese_castle:"=>"U+1F3EF", //🏯
				":european_castle:"=>"U+1F3F0", //🏰
				":tent:"=>"U+26FAU+FE0F", //⛺️
				":factory:"=>"U+1F3ED", //🏭
				":tokyo_tower:"=>"U+1F5FC", //🗼
				":japan:"=>"U+1F5FE", //🗾
				":mount_fuji:"=>"U+1F5FB", //🗻
				":sunrise_over_mountains:"=>"U+1F304", //🌄
				":sunrise:"=>"U+1F305", //🌅
				":stars:"=>"U+1F320", //🌠
				":statue_of_liberty:"=>"U+1F5FD", //🗽
				":bridge_at_night:"=>"U+1F309", //🌉
				":carousel_horse:"=>"U+1F3A0", //🎠
				":rainbow:"=>"U+1F308", //🌈
				":ferris_wheel:"=>"U+1F3A1", //🎡
				":fountain:"=>"U+26F2U+FE0F", //⛲️
				":roller_coaster:"=>"U+1F3A2", //🎢
				":ship:"=>"U+1F6A2", //🚢
				":speedboat:"=>"U+1F6A4", //🚤
				":boat:"=>"U+26F5U+FE0F", //sailboat=⛵️
				":sailboat:"=>"U+26F5U+FE0F", //⛵️
				":rowboat:"=>"U+1F6A3", //🚣
				":anchor:"=>"U+2693U+FE0F", //⚓️
				":rocket:"=>"U+1F680", //🚀
				":airplane:"=>"U+2708U+FE0F", //✈️
				":helicopter:"=>"U+1F681", //🚁
				":steam_locomotive:"=>"U+1F682", //🚂
				":tram:"=>"U+1F68A", //🚊
				":mountain_railway:"=>"U+1F69E", //🚞
				":bike:"=>"U+1F6B2", //🚲
				":aerial_tramway:"=>"U+1F6A1", //🚡
				":suspension_railway:"=>"U+1F69F", //🚟
				":mountain_cableway:"=>"U+1F6A0", //🚠
				":tractor:"=>"U+1F69C", //🚜
				":blue_car:"=>"U+1F699", //🚙
				":oncoming_automobile:"=>"U+1F698", //🚘
				":car:"=>"U+1F697", //automobile=🚗
				":red_car:"=>"U+1F697", //automobile=🚗
				":taxi:"=>"U+1F695", //🚕
				":oncoming_taxi:"=>"U+1F696", //🚖
				":articulated_lorry:"=>"U+1F69B", //🚛
				":bus:"=>"U+1F68C", //🚌
				":oncoming_bus:"=>"U+1F68D", //🚍
				":rotating_light:"=>"U+1F6A8", //🚨
				":police_car:"=>"U+1F693", //🚓
				":oncoming_police_car:"=>"U+1F694", //🚔
				":fire_engine:"=>"U+1F692", //🚒
				":ambulance:"=>"U+1F691", //🚑
				":minibus:"=>"U+1F690", //🚐
				":truck:"=>"U+1F69A", //🚚
				":train:"=>"U+1F68B", //🚋
				":station:"=>"U+1F689", //🚉
				":train2:"=>"U+1F686", //🚆
				":bullettrain_front:"=>"U+1F685", //🚅
				":bullettrain_side:"=>"U+1F684", //🚄
				":light_rail:"=>"U+1F688", //🚈
				":monorail:"=>"U+1F69D", //🚝
				":railway_car:"=>"U+1F683", //🚃
				":trolleybus:"=>"U+1F68E", //🚎
				":ticket:"=>"U+1F3AB", //🎫
				":fuelpump:"=>"U+26FDU+FE0F", //⛽️
				":vertical_traffic_light:"=>"U+1F6A6", //🚦
				":traffic_light:"=>"U+1F6A5", //🚥
				":warning:"=>"U+26A0U+FE0F", //⚠️
				":construction:"=>"U+1F6A7", //🚧
				":beginner:"=>"U+1F530", //🔰
				":atm:"=>"U+1F3E7", //🏧
				":slot_machine:"=>"U+1F3B0", //🎰
				":busstop:"=>"U+1F68F", //🚏
				":barber:"=>"U+1F488", //💈
				":hotsprings:"=>"U+2668U+FE0F", //♨️
				":checkered_flag:"=>"U+1F3C1", //🏁
				":crossed_flags:"=>"U+1F38C", //🎌
				":izakaya_lantern:"=>"U+1F3EE", //🏮
				":moyai:"=>"U+1F5FF", //🗿
				":circus_tent:"=>"U+1F3AA", //🎪
				":performing_arts:"=>"U+1F3AD", //🎭
				":round_pushpin:"=>"U+1F4CD", //📍
				":triangular_flag_on_post:"=>"U+1F6A9", //🚩
				":jp:"=>"U+1F1EFU+1F1F5", //🇯🇵
				":kr:"=>"U+1F1F0U+1F1F7", //🇰🇷
				":cn:"=>"U+1F1E8U+1F1F3", //🇨🇳
				":us:"=>"U+1F1FAU+1F1F8", //🇺🇸
				":fr:"=>"U+1F1EBU+1F1F7", //🇫🇷
				":es:"=>"U+1F1EAU+1F1F8", //🇪🇸
				":it:"=>"U+1F1EEU+1F1F9", //🇮🇹
				":ru:"=>"U+1F1F7U+1F1FA", //🇷🇺
				":gb:"=>"U+1F1ECU+1F1E7", //🇬🇧
				":uk:"=>"U+1F1ECU+1F1E7", //gb=🇬🇧 
				":de:"=>"U+1F1E9U+1F1EA", //🇩🇪
				
				//Symbols
				":one:"=>"U+0031U+FE0FU+20E3", //1️⃣
				":two:"=>"U+0032U+FE0FU+20E3", //2️⃣
				":three:"=>"U+0033U+FE0FU+20E3", //3️⃣
				":four:"=>"U+0034U+FE0FU+20E3", //4️⃣
				":five:"=>"U+0035U+FE0FU+20E3", //5️⃣
				":six:"=>"U+0036U+FE0FU+20E3", //6️⃣
				":seven:"=>"U+0037U+FE0FU+20E3", //7️⃣
				":eight:"=>"U+0038U+FE0FU+20E3", //8️⃣
				":nine:"=>"U+0039U+FE0FU+20E3", //9️⃣
				":keycap_ten:"=>"U+1F51F", //🔟
				":1234:"=>"U+1F522", //🔢
				":zero:"=>"U+0030U+FE0FU+20E3", //0️⃣
				":hash:"=>"U+0023U+FE0FU+20E3", //#️⃣
				":symbols:"=>"U+1F523", //🔣
				":arrow_backward:"=>"U+25C0U+FE0F", //◀️
				":arrow_down:"=>"U+2B07U+FE0F", //⬇️
				":arrow_forward:"=>"U+25B6U+FE0F", //▶️
				":arrow_left:"=>"U+2B05U+FE0F", //⬅️
				":capital_abcd:"=>"U+1F520", //🔠
				":abcd:"=>"U+1F521", //🔡
				":abc:"=>"U+1F524", //🔤
				":arrow_lower_left:"=>"U+2199U+FE0F", //↙️
				":arrow_lower_right:"=>"U+2198U+FE0F", //↘️
				":arrow_right:"=>"U+27A1U+FE0F", //➡️
				":arrow_up:"=>"U+2B06U+FE0F", //⬆️
				":arrow_upper_left:"=>"U+2B05U+FE0F", //⬅️
				":arrow_upper_right:"=>"U+27A1U+FE0F", //➡️
				":arrow_double_down:"=>"U+23EC", //⏬
				":arrow_double_up:"=>"U+23EB", //⏫
				":arrow_down_small:"=>"U+1F53D", //🔽
				":arrow_heading_down:"=>"U+2935U+FE0F", //⤵️
				":arrow_heading_up:"=>"U+2934U+FE0F", //⤴️
				":leftwards_arrow_with_hook:"=>"U+21A9U+FE0F", //↩️
				":arrow_right_hook:"=>"U+21AAU+FE0F", //↪️
				":left_right_arrow:"=>"U+2194U+FE0F", //↔️
				":arrow_up_down:"=>"U+2195U+FE0F", //↕️
				":arrow_up_small:"=>"U+1F53C", //🔼
				":arrows_clockwise:"=>"U+1F503", //🔃
				":arrows_counterclockwise:"=>"U+1F504", //🔄
				":rewind:"=>"U+23EA", //⏪
				":fast_forward:"=>"U+23E9", //⏩
				":information_source:"=>"U+2139U+FE0F", //ℹ️
				":ok:"=>"U+1F197", //🆗
				":twisted_rightwards_arrows:"=>"U+1F500", //🔀
				":repeat:"=>"U+1F501", //🔁
				":repeat_one:"=>"U+1F502", //🔂
				":new:"=>"U+1F195", //🆕
				":top:"=>"U+1F51D", //🔝
				":up:"=>"U+1F199", //🆙
				":cool:"=>"U+1F192", //🆒
				":free:"=>"U+1F193", //🆓
				":ng:"=>"U+1F196", //🆖
				":cinema:"=>"U+1F3A6", //🎦
				":koko:"=>"U+1F201", //🈁
				":signal_strength:"=>"U+1F4F6", //📶
				":u5272:"=>"U+1F239", //🈹
				":u5408:"=>"U+1F234", //🈴
				":u55b6:"=>"U+1F23A", //🈺
				":u6307:"=>"U+1F22F", //🈯
				":u6708:"=>"U+1F237U+FE0F", //🈷️
				":u6709:"=>"U+1F236", //🈶
				":u6e80:"=>"U+1F235", //🈵
				":u7121:"=>"U+1F21A", //🈚
				":u7533:"=>"U+1F238", //🈸
				":u7a7a:"=>"U+1F233", //🈳
				":u7981:"=>"U+1F232", //🈲
				":sa:"=>"U+1F202U+FE0F", //🈂️
				":restroom:"=>"U+1F6BB", //🚻
				":mens:"=>"U+1F6B9", //🚹
				":womens:"=>"U+1F6BA", //🚺
				":baby_symbol:"=>"U+1F6BC", //🚼
				":no_smoking:"=>"U+1F6AD", //🚭
				":parking:"=>"U+1F17FU+FE0F", //🅿️
				":wheelchair:"=>"U+267FU+FE0F", //♿️
				":metro:"=>"U+1F687", //🚇
				":baggage_claim:"=>"U+1F6C4", //🛄
				":accept:"=>"U+1F251", //🉑
				":wc:"=>"U+1F6BE", //🚾
				":potable_water:"=>"U+1F6B0", //🚰
				":put_litter_in_its_place:"=>"U+1F6AE", //🚮
				":secret:"=>"U+3299U+FE0F", //㊙️
				":congratulations:"=>"U+3297U+FE0F", //㊗️
				":m:"=>"U+24C2U+FE0F", //Ⓜ️
				":passport_control:"=>"U+1F6C2", //🛂
				":left_luggage:"=>"U+1F6C5", //🛅
				":customs:"=>"U+1F6C3", //🛃
				":ideograph_advantage:"=>"U+1F250", //🉐
				":cl:"=>"U+1F191", //🆑
				":sos:"=>"U+1F198", //🆘
				":id:"=>"U+1F194", //🆔
				":no_entry_sign:"=>"U+1F6AB", //🚫
				":underage:"=>"U+1F51E", //🔞
				":no_mobile_phones:"=>"U+1F4F5", //📵
				":do_not_litter:"=>"U+1F6AF", //🚯
				":non-potable_water:"=>"U+1F6B1", //🚱
				":no_bicycles:"=>"U+1F6B3", //🚳
				":no_pedestrians:"=>"U+1F6B7", //🚷
				":children_crossing:"=>"U+1F6B8", //🚸
				":no_entry:"=>"U+26D4U+FE0F", //⛔️
				":eight_spoked_asterisk:"=>"U+2733U+FE0F", //✳️
				":sparkle:"=>"U+2747U+FE0F", //❇️
				":eight_pointed_black_star:"=>"U+2734U+FE0F", //✴️
				":heart_decoration:"=>"U+1F49F", //💟
				":vs:"=>"U+1F19A", //🆚
				":vibration_mode:"=>"U+1F4F3", //📳
				":mobile_phone_off:"=>"U+1F4F4", //📴
				":chart:"=>"U+1F4B9", //💹
				":currency_exchange:"=>"U+1F4B1", //💱
				":aries:"=>"U+2648U+FE0F", //♈️
				":taurus:"=>"U+2649U+FE0F", //♉️
				":gemini:"=>"U+264AU+FE0F", //♊️
				":cancer:"=>"U+264BU+FE0F", //♋️
				":leo:"=>"U+264CU+FE0F", //♌️
				":virgo:"=>"U+264DU+FE0F", //♍️
				":libra:"=>"U+264EU+FE0F", //♎️
				":scorpius:"=>"U+264FU+FE0F", //♏️
				":sagittarius:"=>"U+2650U+FE0F", //♐️
				":capricorn:"=>"U+2651U+FE0F", //♑️
				":aquarius:"=>"U+2652U+FE0F", //♒️
				":pisces:"=>"U+2653U+FE0F", //♓️
				":ophiuchus:"=>"U+26CE", //⛎
				":six_pointed_star:"=>"U+1F52F", //🔯
				":negative_squared_cross_mark:"=>"U+274E", //❎
				":a:"=>"U+1F170U+FE0F", //🅰️
				":b:"=>"U+1F171U+FE0F", //🅱️
				":ab:"=>"U+1F18E", //🆎
				":o2:"=>"U+1F17EU+FE0F", //🅾️
				":diamond_shape_with_a_dot_inside:"=>"U+1F4A0", //💠
				":recycle:"=>"U+267BU+FE0F", //♻️
				":end:"=>"U+1F51A", //🔚
				":back:"=>"U+1F519", //🔙
				":on:"=>"U+1F51B", //🔛
				":soon:"=>"U+1F51C", //🔜
				":clock1:"=>"U+1F550", //🕐
				":clock130:"=>"U+1F55C", //🕜
				":clock10:"=>"U+1F559", //🕙
				":clock1030:"=>"U+1F565", //🕥
				":clock11:"=>"U+1F55A", //🕚
				":clock1130:"=>"U+1F566", //🕦
				":clock12:"=>"U+1F55B", //🕛
				":clock1230:"=>"U+1F567", //🕧
				":clock2:"=>"U+1F551", //🕑
				":clock230:"=>"U+1F55D", //🕝
				":clock3:"=>"U+1F552", //🕒
				":clock330:"=>"U+1F55E", //🕞
				":clock4:"=>"U+1F553", //🕓
				":clock430:"=>"U+1F55F", //🕟
				":clock5:"=>"U+1F554", //🕔
				":clock530:"=>"U+1F560", //🕠
				":clock6:"=>"U+1F555", //🕕
				":clock630:"=>"U+1F561", //🕡
				":clock7:"=>"U+1F556", //🕖
				":clock730:"=>"U+1F562", //🕢
				":clock8:"=>"U+1F557", //🕗
				":clock830:"=>"U+1F563", //🕣
				":clock9:"=>"U+1F558", //🕘
				":clock930:"=>"U+1F564", //🕤
				":heavy_dollar_sign:"=>"U+1F4B2", //💲
				":copyright:"=>"U+00A9U+FE0F", //©️
				":registered:"=>"U+00AEU+FE0F", //®️
				":tm:"=>"U+2122U+FE0F", //™️
				":x:"=>"U+274C", //❌
				":heavy_exclamation_mark:"=>"U+2757U+FE0F", //❗️
				":bangbang:"=>"U+203CU+FE0F", //‼️
				":interrobang:"=>"U+2049U+FE0F", //⁉️
				":o:"=>"U+2B55U+FE0F", //⭕️
				":heavy_multiplication_x:"=>"U+2716U+FE0F", //✖️
				":heavy_plus_sign:"=>"U+2795", //➕
				":heavy_minus_sign:"=>"U+2796", //➖
				":heavy_division_sign:"=>"U+2797", //➗
				":white_flower:"=>"U+1F4AE", //💮
				":100:"=>"U+1F4AF", //💯
				":heavy_check_mark:"=>"U+2714U+FE0F", //✔️
				":ballot_box_with_check:"=>"U+2611U+FE0F", //☑️
				":radio_button:"=>"U+1F518", //🔘
				":link:"=>"U+1F517", //🔗
				":curly_loop:"=>"U+27B0", //➰
				":wavy_dash:"=>"U+3030U+FE0F", //〰️
				":part_alternation_mark:"=>"U+303DU+FE0F", //〽️
				":trident:"=>"U+1F531", //🔱
				":black_small_square:"=>"U+25AAU+FE0F", //▪️
				":white_small_square:"=>"U+25ABU+FE0F", //▫️
				":black_medium_small_square:"=>"U+25FEU+FE0F", //◾️
				":white_medium_small_square:"=>"U+25FDU+FE0F", //◽️
				":black_medium_square:"=>"U+25FCU+FE0F", //◼️
				":white_medium_square:"=>"U+25FBU+FE0F", //◻️
				":black_large_square:"=>"U+2B1BU+FE0F", //⬛️
				":white_large_square:"=>"U+2B1CU+FE0F", //⬜️
				":white_check_mark:"=>"U+2705", //✅
				":black_square_button:"=>"U+1F532", //🔲
				":white_square_button:"=>"U+1F533", //🔳
				":black_circle:"=>"U+26ABU+FE0F", //⚫️
				":white_circle:"=>"U+26AAU+FE0F", //⚪️
				":red_circle:"=>"U+1F534", //🔴
				":large_blue_circle:"=>"U+1F535", //🔵
				":large_blue_diamond:"=>"U+1F537", //🔷
				":large_orange_diamond:"=>"U+1F536", //🔶
				":small_blue_diamond:"=>"U+1F539", //🔹
				":small_orange_diamond:"=>"U+1F538", //🔸
				":small_red_triangle:"=>"U+1F53A", //🔺
				":small_red_triangle_down:"=>"U+1F53B", //🔻
				
				//":shipit:"=>"", 
					
				//Country Codes
				":country_ad:"=>"U+1F1E6U+1F1E9", //🇦🇩
				":country_ae:"=>"U+1F1E6U+1F1EA", //🇦🇪
				":country_af:"=>"U+1F1E6U+1F1EB", //🇦🇫
				":country_ag:"=>"U+1F1E6U+1F1EC", //🇦🇬
				":country_ai:"=>"U+1F1E6U+1F1EE", //🇦🇮
				":country_al:"=>"U+1F1E6U+1F1F1", //🇦🇱
				":country_am:"=>"U+1F1E6U+1F1F2", //🇦🇲
				":country_ao:"=>"U+1F1E6U+1F1F4", //🇦🇴
				":country_ar:"=>"U+1F1E6U+1F1F7", //🇦🇷
				":country_as:"=>"U+1F1E6U+1F1F8", //🇦🇸
				":country_at:"=>"U+1F1E6U+1F1F9", //🇦🇹
				":country_au:"=>"U+1F1E6U+1F1FA", //🇦🇺
				":country_aw:"=>"U+1F1E6U+1F1FC", //🇦🇼
				":country_az:"=>"U+1F1E6U+1F1FF", //🇦🇿
				":country_ba:"=>"U+1F1E7U+1F1E6", //🇧🇦
				":country_bb:"=>"U+1F1E7U+1F1E7", //🇧🇧
				":country_bd:"=>"U+1F1E7U+1F1E9", //🇧🇩
				":country_be:"=>"U+1F1E7U+1F1EA", //🇧🇪
				":country_bf:"=>"U+1F1E7U+1F1EB", //🇧🇫
				":country_bg:"=>"U+1F1E7U+1F1EC", //🇧🇬
				":country_bh:"=>"U+1F1E7U+1F1ED", //🇧🇭
				":country_bi:"=>"U+1F1E7U+1F1EE", //🇧🇮
				":country_bj:"=>"U+1F1E7U+1F1EF", //🇧🇯
				":country_bm:"=>"U+1F1E7U+1F1F2", //🇧🇲
				":country_bn:"=>"U+1F1E7U+1F1F3", //🇧🇳
				":country_bo:"=>"U+1F1E7U+1F1F4", //🇧🇴
				":country_br:"=>"U+1F1E7U+1F1F7", //🇧🇷
				":country_bs:"=>"U+1F1E7U+1F1F8", //🇧🇸
				":country_bt:"=>"U+1F1E7U+1F1F9", //🇧🇹
				":country_bw:"=>"U+1F1E7U+1F1FC", //🇧🇼
				":country_by:"=>"U+1F1E7U+1F1FE", //🇧🇾
				":country_bz:"=>"U+1F1E7U+1F1FF", //🇧🇿
				":country_ca:"=>"U+1F1E8U+1F1E6", //🇨🇦
				":country_cd:"=>"U+1F1E8U+1F1E9", //🇨🇩
				":country_cf:"=>"U+1F1E8U+1F1EB", //🇨🇫
				":country_cg:"=>"U+1F1E8U+1F1EC", //🇨🇬
				":country_ch:"=>"U+1F1E8U+1F1ED", //🇨🇭
				":country_ci:"=>"U+1F1E8U+1F1EE", //🇨🇮
				":country_ck:"=>"U+1F1E8U+1F1F0", //🇨🇰
				":country_cl:"=>"U+1F1E8U+1F1F1", //🇨🇱
				":country_cm:"=>"U+1F1E8U+1F1F2", //🇨🇲
				":country_cn:"=>"U+1F1E8U+1F1F3", //🇨🇳
				":country_co:"=>"U+1F1E8U+1F1F4", //🇨🇴
				":country_cr:"=>"U+1F1E8U+1F1F7", //🇨🇷
				":country_cu:"=>"U+1F1E8U+1F1FA", //🇨🇺
				":country_cv:"=>"U+1F1E8U+1F1FB", //🇨🇻
				":country_cw:"=>"U+1F1E8U+1F1FC", //🇨🇼
				":country_cy:"=>"U+1F1E8U+1F1FE", //🇨🇾
				":country_cz:"=>"U+1F1E8U+1F1FF", //🇨🇿
				":country_de:"=>"U+1F1E9U+1F1EA", //🇩🇪
				":country_dj:"=>"U+1F1E9U+1F1EF", //🇩🇯
				":country_dk:"=>"U+1F1E9U+1F1F0", //🇩🇰
				":country_dm:"=>"U+1F1E9U+1F1F2", //🇩🇲
				":country_do:"=>"U+1F1E9U+1F1F4", //🇩🇴
				":country_dz:"=>"U+1F1E9U+1F1FF", //🇩🇿
				":country_ec:"=>"U+1F1EAU+1F1E8", //🇪🇨
				":country_ee:"=>"U+1F1EAU+1F1EA", //🇪🇪
				":country_eg:"=>"U+1F1EAU+1F1EC", //🇪🇬
				":country_er:"=>"U+1F1EAU+1F1F7", //🇪🇷
				":country_es:"=>"U+1F1EAU+1F1F8", //🇪🇸
				":country_et:"=>"U+1F1EAU+1F1F9", //🇪🇹
				":country_fi:"=>"U+1F1EBU+1F1EE", //🇫🇮
				":country_fj:"=>"U+1F1EBU+1F1EF", //🇫🇯
				":country_fo:"=>"U+1F1EBU+1F1F4", //🇫🇴
				":country_fr:"=>"U+1F1EBU+1F1F7", //🇫🇷
				":country_ga:"=>"U+1F1ECU+1F1E6", //🇬🇦
				":country_gb:"=>"U+1F1ECU+1F1E7", //🇬🇧
				":country_gd:"=>"U+1F1ECU+1F1E9", //🇬🇩
				":country_ge:"=>"U+1F1ECU+1F1EA", //🇬🇪
				":country_gf:"=>"U+1F1ECU+1F1EB", //🇬🇫
				":country_gh:"=>"U+1F1ECU+1F1ED", //🇬🇭
				":country_gi:"=>"U+1F1ECU+1F1EE", //🇬🇮
				":country_gm:"=>"U+1F1ECU+1F1F2", //🇬🇲
				":country_gn:"=>"U+1F1ECU+1F1F3", //🇬🇳
				":country_gp:"=>"U+1F1ECU+1F1F5", //🇬🇵
				":country_gq:"=>"U+1F1ECU+1F1F6", //🇬🇶
				":country_gr:"=>"U+1F1ECU+1F1F7", //🇬🇷
				":country_gt:"=>"U+1F1ECU+1F1F9", //🇬🇹
				":country_gu:"=>"U+1F1ECU+1F1FA", //🇬🇺
				":country_gw:"=>"U+1F1ECU+1F1FC", //🇬🇼
				":country_gy:"=>"U+1F1ECU+1F1FE", //🇬🇾
				":country_hk:"=>"U+1F1EDU+1F1F0", //🇭🇰
				":country_hn:"=>"U+1F1EDU+1F1F3", //🇭🇳
				":country_hr:"=>"U+1F1EDU+1F1F7", //🇭🇷
				":country_ht:"=>"U+1F1EDU+1F1F9", //🇭🇹
				":country_hu:"=>"U+1F1EDU+1F1FA", //🇭🇺
				":country_id:"=>"U+1F1EEU+1F1E9", //🇮🇩
				":country_ie:"=>"U+1F1EEU+1F1EA", //🇮🇪
				":country_il:"=>"U+1F1EEU+1F1F1", //🇮🇱
				":country_in:"=>"U+1F1EEU+1F1F3", //🇮🇳
				":country_iq:"=>"U+1F1EEU+1F1F6", //🇮🇶
				":country_ir:"=>"U+1F1EEU+1F1F7", //🇮🇷
				":country_is:"=>"U+1F1EEU+1F1F8", //🇮🇸
				":country_it:"=>"U+1F1EEU+1F1F9", //🇮🇹
				":country_jm:"=>"U+1F1EFU+1F1F2", //🇯🇲
				":country_jo:"=>"U+1F1EFU+1F1F4", //🇯🇴
				":country_jp:"=>"U+1F1EFU+1F1F5", //🇯🇵
				":country_ke:"=>"U+1F1F0U+1F1EA", //🇰🇪
				":country_kg:"=>"U+1F1F0U+1F1EC", //🇰🇬
				":country_kh:"=>"U+1F1F0U+1F1ED", //🇰🇭
				":country_ki:"=>"U+1F1F0U+1F1EE", //🇰🇮
				":country_km:"=>"U+1F1F0U+1F1F2", //🇰🇲
				":country_kn:"=>"U+1F1F0U+1F1F3", //🇰🇳
				":country_kp:"=>"U+1F1F0U+1F1F5", //🇰🇵
				":country_kr:"=>"U+1F1F0U+1F1F7", //🇰🇷
				":country_kw:"=>"U+1F1F0U+1F1FC", //🇰🇼
				":country_ky:"=>"U+1F1F0U+1F1FE", //🇰🇾
				":country_kz:"=>"U+1F1F0U+1F1FF", //🇰🇿
				":country_la:"=>"U+1F1F1U+1F1E6", //🇱🇦
				":country_lb:"=>"U+1F1F1U+1F1E7", //🇱🇧
				":country_lc:"=>"U+1F1F1U+1F1E8", //🇱🇨
				":country_li:"=>"U+1F1F1U+1F1EE", //🇱🇮
				":country_lk:"=>"U+1F1F1U+1F1F0", //🇱🇰
				":country_lr:"=>"U+1F1F1U+1F1F7", //🇱🇷
				":country_ls:"=>"U+1F1F1U+1F1F8", //🇱🇸
				":country_lt:"=>"U+1F1F1U+1F1F9", //🇱🇹
				":country_lu:"=>"U+1F1F1U+1F1FA", //🇱🇺
				":country_lv:"=>"U+1F1F1U+1F1FB", //🇱🇻
				":country_ly:"=>"U+1F1F1U+1F1FE", //🇱🇾
				":country_ma:"=>"U+1F1F2U+1F1E6", //🇲🇦
				":country_md:"=>"U+1F1F2U+1F1E9", //🇲🇩
				":country_me:"=>"U+1F1F2U+1F1EA", //🇲🇪
				":country_mg:"=>"U+1F1F2U+1F1EC", //🇲🇬
				":country_mk:"=>"U+1F1F2U+1F1F0", //🇲🇰
				":country_ml:"=>"U+1F1F2U+1F1F1", //🇲🇱
				":country_mm:"=>"U+1F1F2U+1F1F2", //🇲🇲
				":country_mn:"=>"U+1F1F2U+1F1F3", //🇲🇳
				":country_mo:"=>"U+1F1F2U+1F1F4", //🇲🇴
				":country_mp:"=>"U+1F1F2U+1F1F5", //🇲🇵
				":country_mq:"=>"U+1F1F2U+1F1F6", //🇲🇶
				":country_mr:"=>"U+1F1F2U+1F1F7", //🇲🇷
				":country_ms:"=>"U+1F1F2U+1F1F8", //🇲🇸
				":country_mt:"=>"U+1F1F2U+1F1F9", //🇲🇹
				":country_mv:"=>"U+1F1F2U+1F1FB", //🇲🇻
				":country_mw:"=>"U+1F1F2U+1F1FC", //🇲🇼
				":country_mx:"=>"U+1F1F2U+1F1FD", //🇲🇽
				":country_my:"=>"U+1F1F2U+1F1FE", //🇲🇾
				":country_mz:"=>"U+1F1F2U+1F1FF", //🇲🇿
				":country_na:"=>"U+1F1F3U+1F1E6", //🇳🇦
				":country_nc:"=>"U+1F1F3U+1F1E8", //🇳🇨
				":country_ne:"=>"U+1F1F3U+1F1EA", //🇳🇪
				":country_ng:"=>"U+1F1F3U+1F1EC", //🇳🇬
				":country_ni:"=>"U+1F1F3U+1F1EE", //🇳🇮
				":country_nl:"=>"U+1F1F3U+1F1F1", //🇳🇱
				":country_no:"=>"U+1F1F3U+1F1F4", //🇳🇴
				":country_np:"=>"U+1F1F3U+1F1F5", //🇳🇵
				":country_nu:"=>"U+1F1F3U+1F1FA", //🇳🇺
				":country_nz:"=>"U+1F1F3U+1F1FF", //🇳🇿
				":country_om:"=>"U+1F1F4U+1F1F2", //🇴🇲
				":country_pa:"=>"U+1F1F5U+1F1E6", //🇵🇦
				":country_pe:"=>"U+1F1F5U+1F1EA", //🇵🇪
				":country_pg:"=>"U+1F1F5U+1F1EC", //🇵🇬
				":country_ph:"=>"U+1F1F5U+1F1ED", //🇵🇭
				":country_pk:"=>"U+1F1F5U+1F1F0", //🇵🇰
				":country_pl:"=>"U+1F1F5U+1F1F1", //🇵🇱
				":country_pr:"=>"U+1F1F5U+1F1F7", //🇵🇷
				":country_ps:"=>"U+1F1F5U+1F1F8", //🇵🇸
				":country_pt:"=>"U+1F1F5U+1F1F9", //🇵🇹
				":country_pw:"=>"U+1F1F5U+1F1FC", //🇵🇼
				":country_py:"=>"U+1F1F5U+1F1FE", //🇵🇾
				":country_qa:"=>"U+1F1F6U+1F1E6", //🇶🇦
				":country_re:"=>"U+1F1F7U+1F1EA", //🇷🇪
				":country_ro:"=>"U+1F1F7U+1F1F4", //🇷🇴
				":country_rs:"=>"U+1F1F7U+1F1F8", //🇷🇸
				":country_ru:"=>"U+1F1F7U+1F1FA", //🇷🇺
				":country_rw:"=>"U+1F1F7U+1F1FC", //🇷🇼
				":country_sa:"=>"U+1F1F8U+1F1E6", //🇸🇦
				":country_sb:"=>"U+1F1F8U+1F1E7", //🇸🇧
				":country_sc:"=>"U+1F1F8U+1F1E8", //🇸🇨
				":country_sd:"=>"U+1F1F8U+1F1E9", //🇸🇩
				":country_se:"=>"U+1F1F8U+1F1EA", //🇸🇪
				":country_sg:"=>"U+1F1F8U+1F1EC", //🇸🇬
				":country_si:"=>"U+1F1F8U+1F1EE", //🇸🇮
				":country_sk:"=>"U+1F1F8U+1F1F0", //🇸🇰
				":country_sl:"=>"U+1F1F8U+1F1F1", //🇸🇱
				":country_sm:"=>"U+1F1F8U+1F1F2", //🇸🇲
				":country_sn:"=>"U+1F1F8U+1F1F3", //🇸🇳
				":country_so:"=>"U+1F1F8U+1F1F4", //🇸🇴
				":country_sr:"=>"U+1F1F8U+1F1F7", //🇸🇷
				":country_ss:"=>"U+1F1F8U+1F1F8", //🇸🇸
				":country_st:"=>"U+1F1F8U+1F1F9", //🇸🇹
				":country_sv:"=>"U+1F1F8U+1F1FB", //🇸🇻
				":country_sx:"=>"U+1F1F8U+1F1FD", //🇸🇽
				":country_sy:"=>"U+1F1F8U+1F1FE", //🇸🇾
				":country_sz:"=>"U+1F1F8U+1F1FF", //🇸🇿
				":country_tc:"=>"U+1F1F9U+1F1E8", //🇹🇨
				":country_tf:"=>"U+1F1F9U+1F1EB", //🇹🇫
				":country_tg:"=>"U+1F1F9U+1F1EC", //🇹🇬
				":country_th:"=>"U+1F1F9U+1F1ED", //🇹🇭
				":country_tj:"=>"U+1F1F9U+1F1EF", //🇹🇯
				":country_tl:"=>"U+1F1F9U+1F1F1", //🇹🇱
				":country_tm:"=>"U+1F1F9U+1F1F2", //🇹🇲
				":country_tn:"=>"U+1F1F9U+1F1F3", //🇹🇳
				":country_to:"=>"U+1F1F9U+1F1F4", //🇹🇴
				":country_tr:"=>"U+1F1F9U+1F1F7", //🇹🇷
				":country_tt:"=>"U+1F1F9U+1F1F9", //🇹🇹
				":country_tv:"=>"U+1F1F9U+1F1FB", //🇹🇻
				":country_tz:"=>"U+1F1F9U+1F1FF", //🇹🇿
				":country_ua:"=>"U+1F1FAU+1F1E6", //🇺🇦
				":country_ug:"=>"U+1F1FAU+1F1EC", //🇺🇬
				":country_us:"=>"U+1F1FAU+1F1F8", //🇺🇸
				":country_uy:"=>"U+1F1FAU+1F1FE", //🇺🇾
				":country_uz:"=>"U+1F1FAU+1F1FF", //🇺🇿
				":country_vc:"=>"U+1F1FBU+1F1E8", //🇻🇨
				":country_ve:"=>"U+1F1FBU+1F1EA", //🇻🇪
				":country_vg:"=>"U+1F1FBU+1F1EC", //🇻🇬
				":country_vi:"=>"U+1F1FBU+1F1EE", //🇻🇮
				":country_vn:"=>"U+1F1FBU+1F1F3", //🇻🇳
				":country_vu:"=>"U+1F1FBU+1F1FA", //🇻🇺
				":country_ws:"=>"U+1F1FCU+1F1F8", //🇼🇸
				":country_ye:"=>"U+1F1FEU+1F1EA", //🇾🇪
				":country_za:"=>"U+1F1FFU+1F1E6", //🇿🇦
				":country_zm:"=>"U+1F1FFU+1F1F2", //🇿🇲
				":country_zw:"=>"U+1F1FFU+1F1FC" //🇿🇼
		);
		//HTML Escape
		foreach($emoji as $key=>$val) {
			$emoji[$key] = preg_replace("/U\+([A-Fa-f0-9]+)/", "&#x$1;", $val);
		}
		foreach($emoji as $key => $value) {
			if(!is_array($value))
				$string = str_replace($key, $value, $string);
		}
		return $string;
	}
	
	function getCountryCodes() {
		//ISO 3166-1 alpha-2(https://www.iso.org/obp/ui/#search/code/)
		//A U+1F1E6 🇦
		//B U+1F1E7 🇧
		//C U+1F1E8 🇨
		//D U+1F1E9 🇩
		//E U+1F1EA 🇪
		//F U+1F1EB 🇫
		//G U+1F1EC 🇬
		//H U+1F1ED 🇭
		//I U+1F1EE 🇮
		//J U+1F1EF 🇯
		//K U+1F1F0 🇰
		//L U+1F1F1 🇱
		//M U+1F1F2 🇲
		//N U+1F1F3 🇳
		//O U+1F1F4 🇴
		//P U+1F1F5 🇵
		//Q U+1F1F6 🇶
		//R U+1F1F7 🇷
		//S U+1F1F8 🇸
		//T U+1F1F9 🇹
		//U U+1F1FA 🇺
		//V U+1F1FB 🇻
		//W U+1F1FC 🇼
		//X U+1F1FD 🇽
		//Y U+1F1FE 🇾
		//Z U+1F1FF 🇿
		$chars = array(a=>"🇦", b=>"🇧", c=>"🇨", d=>"🇩", e=>"🇪", f=>"🇫", g=>"🇬", h=>"🇭", i=>"🇮", j=>"🇯", k=>"🇰", l=>"🇱", m=>"🇲", n=>"🇳", o=>"🇴", p=>"🇵", q=>"🇶", r=>"🇷", s=>"🇸", t=>"🇹", u=>"🇺", v=>"🇻", w=>"🇼", x=>"🇽", y=>"🇾", z=>"🇿");
		$codes = array(a=>"U+1F1E6", b=>"U+1F1E7", c=>"U+1F1E8", d=>"U+1F1E9", e=>"U+1F1EA", f=>"U+1F1EB", g=>"U+1F1EC", h=>"U+1F1ED", i=>"U+1F1EE", j=>"U+1F1EF", k=>"U+1F1F0", l=>"U+1F1F1", m=>"U+1F1F2", n=>"U+1F1F3", o=>"U+1F1F4", p=>"U+1F1F5", q=>"U+1F1F6", r=>"U+1F1F7", s=>"U+1F1F8", t=>"U+1F1F9", u=>"U+1F1FA", v=>"U+1F1FB", w=>"U+1F1FC", x=>"U+1F1FD", y=>"U+1F1FE", z=>"U+1F1FF");
		foreach($chars as $key=>$val) {
			foreach($chars as $subKey=>$subVal) {
				$return .= sprintf("%s%s = %s%s = %s%s<br >\n", $key, $subKey, $codes[$key], $codes[$subKey], $val, $subVal);
			}
		}
		return $return;
	}
}
?>