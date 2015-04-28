<?php
//xFacility2015
//XFMarkdown
//Studio2b
//Michael Son(mson0129@gmail.com)
//27APR2015(0.1.0.) - This file is newly created. Replacing block or inline does not work as your intention. Grammar is following GitHub Markdown rules basically. And some rules are added from NamuWiki(//namu.wiki).
//29APR2015(0.2.0.) - replaceEmoji() is added. Every country code is supported. If you use the new codes, type ":country_CountryCode:" such as ":country_us:".

class XFMarkdown extends XFObject {
	var $string;
	
	function __construct($string) {
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
			":bowtie:"=>"",
			":smile:"=>"U+1F604", //😄
			":laughing:"=>"U+1F606", //😆
			":blush:"=>"U+1F60A", //😊
			":smiley:"=>"U+1F603", //😃
			":relaxed:"=>"U+1F60C", //😌
			":smirk:"=>"U+1F60F", //😏
			":heart_eyes:"=>"U+1F60D", //😍
			":kissing_heart:"=>"U+1F618", //😘
			":kissing_closed_eyes:"=>"U+1F61A", //😚
			":flushed:"=>"U+1F633", //😳
			":relieved:"=>"",
			":satisfied:"=>"",
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
			":neckbeard:"=>"",
			":tired_face:"=>"U+1F62B", //😫
			":angry:"=>"U+1F620", //😠
			":rage:"=>"U+1F621", //😡
			":triumph:"=>"U+1F624", //😤
			":sleepy:"=>"U+1F62A", //😪
			":yum:"=>"U+1F60B", //😋
			":mask:"=>"U+1F637", //😷
			":sunglasses:"=>"U+1F60E", //😎
			":dizzy_face:"=>"U+1F635", //😵
			":imp:"=>"",
			":smiling_imp:"=>"U+1F608", //😈
			":neutral_face:"=>"U+1F610", //😐
			":no_mouth:"=>"U+1F636", //😶
			":innocent:"=>"U+1F607", //😇
			":alien:"=>"",
			":yellow_heart:"=>"",
			":blue_heart:"=>"",
			":purple_heart:"=>"",
			":heart:"=>"U+2764", //❤
			":green_heart:"=>"",
			":broken_heart:"=>"",
			":heartbeat:"=>"",
			":heartpulse:"=>"",
			":two_hearts:"=>"",
			":revolving_hearts:"=>"",
			":cupid:"=>"",
			":sparkling_heart:"=>"",
			":sparkles:"=>"U+2728", //✨
			":star:"=>"",
			":star2:"=>"",
			":dizzy:"=>"",
			":boom:"=>"",
			":collision:"=>"",
			":anger:"=>"",
			":exclamation:"=>"U+2757", //❗
			":question:"=>"U+2753", //❓
			":grey_exclamation:"=>"U+2755", //❕
			":grey_question:"=>"U+2754", //❔
			":zzz:"=>"",
			":dash:"=>"",
			":sweat_drops:"=>"",
			":notes:"=>"",
			":musical_note:"=>"",
			":fire:"=>"",
			":hankey:"=>"",
			":poop:"=>"",
			":shit:"=>"",
			":+1:"=>"",
			":thumbsup:"=>"",
			":-1:"=>"",
			":thumbsdown:"=>"",
			":ok_hand:"=>"",
			":punch:"=>"",
			":facepunch:"=>"",
			":fist:"=>"U+270A", //✊
			":v:"=>"U+270C", //✌
			":wave:"=>"",
			":hand:"=>"", 
			":raised_hand:"=>"U+270B", //✋
			":open_hands:"=>"",
			":point_up:"=>"",
			":point_down:"=>"",
			":point_left:"=>"",
			":point_right:"=>"",
			":raised_hands:"=>"U+1F64C", //🙌
			":pray:"=>"U+1F64F", //🙏
			":point_up_2:"=>"",
			":clap:"=>"",
			":muscle:"=>"",
			":metal:"=>"",
			":fu:"=>"",
			":walking:"=>"U+1F6B6", //🚶
			":runner:"=>"",
			":running:"=>"",
			":couple:"=>"",
			":family:"=>"",
			":two_men_holding_hands:"=>"U+1F46C", //👬
			":two_women_holding_hands:"=>"U+1F46D", //👭
			":dancer:"=>"",
			":dancers:"=>"",
			":ok_woman:"=>"U+1F646", //🙆
			":no_good:"=>"U+1F645", //🙅
			":information_desk_person:"=>"",
			":raising_hand:"=>"U+1F64B", //🙋
			":bride_with_veil:"=>"",
			":person_with_pouting_face:"=>"U+1F64E", //🙎
			":person_frowning:"=>"U+1F64D", //🙍
			":bow:"=>"U+1F647", //🙇
			":couplekiss:"=>"",
			":couple_with_heart:"=>"",
			":massage:"=>"",
			":haircut:"=>"",
			":nail_care:"=>"",
			":boy:"=>"",
			":girl:"=>"",
			":woman:"=>"",
			":man:"=>"",
			":baby:"=>"",
			":older_woman:"=>"",
			":older_man:"=>"",
			":person_with_blond_hair:"=>"",
			":man_with_gua_pi_mao:"=>"",
			":man_with_turban:"=>"",
			":construction_worker:"=>"",
			":cop:"=>"",
			":angel:"=>"",
			":princess:"=>"",
			":smiley_cat:"=>"U+1F63A", //😺
			":smile_cat:"=>"U+1F638", //😸
			":heart_eyes_cat:"=>"U+1F63B", //😻
			":kissing_cat:"=>"U+1F63D", //😽
			":smirk_cat:"=>"U+1F63C", //😼
			":scream_cat:"=>"U+1F640", //🙀
			":crying_cat_face:"=>"U+1F63F", //😿
			":joy_cat:"=>"U+1F639", //😹
			":pouting_cat:"=>"U+1F63E", //😾
			":japanese_ogre:"=>"",
			":japanese_goblin:"=>"",
			":see_no_evil:"=>"U+1F648", //🙈
			":hear_no_evil:"=>"U+1F649", //🙉
			":speak_no_evil:"=>"U+1F64A", //🙊
			":guardsman:"=>"",
			":skull:"=>"",
			":feet:"=>"",
			":lips:"=>"",
			":kiss:"=>"",
			":droplet:"=>"",
			":ear:"=>"",
			":eyes:"=>"",
			":nose:"=>"",
			":tongue:"=>"",
			":love_letter:"=>"",
			":bust_in_silhouette:"=>"",
			":busts_in_silhouette:"=>"U+1F465", //👥
			":speech_balloon:"=>"",
			":thought_balloon:"=>"U+1F4AD", //💭
			":feelsgood:"=>"",
			":finnadie:"=>"",
			":goberserk:"=>"",
			":godmode:"=>"",
			":hurtrealbad:"=>"",
			":rage1:"=>"",
			":rage2:"=>"",
			":rage3:"=>"",
			":rage4:"=>"",
			":suspect:"=>"",
			":trollface:"=>"",
			
			//Nature
			":sunny:"=>"",
			":umbrella:"=>"",
			":cloud:"=>"",
			":snowflake:"=>"U+2744", //❄
			":snowman:"=>"",
			":zap:"=>"",
			":cyclone:"=>"",
			":foggy:"=>"",
			":ocean:"=>"",
			":cat:"=>"",
			":dog:"=>"",
			":mouse:"=>"",
			":hamster:"=>"",
			":rabbit:"=>"",
			":wolf:"=>"",
			":frog:"=>"",
			":tiger:"=>"",
			":koala:"=>"",
			":bear:"=>"",
			":pig:"=>"",
			":pig_nose:"=>"",
			":cow:"=>"",
			":boar:"=>"",
			":monkey_face:"=>"",
			":monkey:"=>"",
			":horse:"=>"",
			":racehorse:"=>"",
			":camel:"=>"",
			":sheep:"=>"",
			":elephant:"=>"",
			":panda_face:"=>"",
			":snake:"=>"",
			":bird:"=>"",
			":baby_chick:"=>"",
			":hatched_chick:"=>"",
			":hatching_chick:"=>"",
			":chicken:"=>"",
			":penguin:"=>"",
			":turtle:"=>"",
			":bug:"=>"",
			":honeybee:"=>"",
			":ant:"=>"",
			":beetle:"=>"",
			":snail:"=>"",
			":octopus:"=>"",
			":tropical_fish:"=>"",
			":fish:"=>"",
			":whale:"=>"",
			":whale2:"=>"U+1F40B", //🐋
			":dolphin:"=>"",
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
			":dragon_face:"=>"",
			":blowfish:"=>"",
			":crocodile:"=>"U+1F40A", //🐊
			":dromedary_camel:"=>"U+1F42A", //🐪
			":leopard:"=>"",
			":cat2:"=>"U+1F408", //🐈
			":poodle:"=>"",
			":paw_prints:"=>"",
			":bouquet:"=>"",
			":cherry_blossom:"=>"",
			":tulip:"=>"",
			":four_leaf_clover:"=>"",
			":rose:"=>"",
			":sunflower:"=>"",
			":hibiscus:"=>"",
			":maple_leaf:"=>"",
			":leaves:"=>"",
			":fallen_leaf:"=>"",
			":herb:"=>"",
			":mushroom:"=>"",
			":cactus:"=>"",
			":palm_tree:"=>"",
			":evergreen_tree:"=>"U+1F332", //🌲
			":deciduous_tree:"=>"U+1F333", //🌳
			":chestnut:"=>"",
			":seedling:"=>"",
			":blossom:"=>"",
			":ear_of_rice:"=>"",
			":shell:"=>"",
			":globe_with_meridians:"=>"",
			":sun_with_face:"=>"U+1F31E", //🌞
			":full_moon_with_face:"=>"U+1F31D", //🌝
			":new_moon_with_face:"=>"U+1F31A", //🌚
			":new_moon:"=>"",
			":waxing_crescent_moon:"=>"U+1F312", //🌒
			":first_quarter_moon:"=>"",
			":waxing_gibbous_moon:"=>"", 
			":full_moon:"=>"",
			":waning_gibbous_moon:"=>"U+1F316", //🌖
			":last_quarter_moon:"=>"U+1F317", //🌗
			":waning_crescent_moon:"=>"U+1F318", //🌘
			":last_quarter_moon_with_face:"=>"U+1F31C", //🌜
			":first_quarter_moon_with_face:"=>"",
			":crescent_moon:"=>"",
			":earth_africa:"=>"U+1F30D", //🌍
			":earth_americas:"=>"U+1F30E", //🌎
			":earth_asia:"=>"", //
			":volcano:"=>"",
			":milky_way:"=>"",
			":partly_sunny:"=>"",
			":octocat:"=>"",
			":squirrel:"=>"",
			
			//Objects
			":bamboo:"=>"",
			":gift_heart:"=>"",
			":dolls:"=>"",
			":school_satchel:"=>"",
			":mortar_board:"=>"",
			":flags:"=>"",
			":fireworks:"=>"",
			":sparkler:"=>"",
			":wind_chime:"=>"",
			":rice_scene:"=>"",
			":jack_o_lantern:"=>"",
			":ghost:"=>"",
			":santa:"=>"",
			":christmas_tree:"=>"",
			":gift:"=>"",
			":bell:"=>"",
			":no_bell:"=>"U+1F515", //🔕
			":tanabata_tree:"=>"",
			":tada:"=>"",
			":confetti_ball:"=>"",
			":balloon:"=>"",
			":crystal_ball:"=>"",
			":cd:"=>"",
			":dvd:"=>"",
			":floppy_disk:"=>"",
			":camera:"=>"",
			":video_camera:"=>"",
			":movie_camera:"=>"",
			":computer:"=>"",
			":tv:"=>"",
			":iphone:"=>"",
			":phone:"=>"",
			":telephone:"=>"",
			":telephone_receiver:"=>"",
			":pager:"=>"",
			":fax:"=>"",
			":minidisc:"=>"",
			":vhs:"=>"",
			":sound:"=>"U+1F509", //🔉
			":speaker:"=>"",
			":mute:"=>"U+1F507", //🔇
			":loudspeaker:"=>"",
			":mega:"=>"",
			":hourglass:"=>"",
			":hourglass_flowing_sand:"=>"",
			":alarm_clock:"=>"",
			":watch:"=>"",
			":radio:"=>"",
			":satellite:"=>"",
			":loop:"=>"",
			":mag:"=>"",
			":mag_right:"=>"",
			":unlock:"=>"",
			":lock:"=>"",
			":lock_with_ink_pen:"=>"",
			":closed_lock_with_key:"=>"",
			":key:"=>"",
			":bulb:"=>"",
			":flashlight:"=>"",
			":high_brightness:"=>"U+1F506", //🔆
			":low_brightness:"=>"U+1F505", //🔅
			":electric_plug:"=>"",
			":battery:"=>"",
			":calling:"=>"",
			":email:"=>"",
			":mailbox:"=>"",
			":postbox:"=>"",
			":bath:"=>"U+1F6C0", //🛀
			":bathtub:"=>"U+1F6C1", //🛁
			":shower:"=>"U+1F6BF", //🚿
			":toilet:"=>"U+1F6BD", //🚽
			":wrench:"=>"",
			":nut_and_bolt:"=>"",
			":hammer:"=>"",
			":seat:"=>"",
			":moneybag:"=>"",
			":yen:"=>"",
			":dollar:"=>"",
			":pound:"=>"U+1F4B7", //💷
			":euro:"=>"U+1F4B6", //💶
			":credit_card:"=>"",
			":money_with_wings:"=>"",
			":e-mail:"=>"",
			":inbox_tray:"=>"",
			":outbox_tray:"=>"",
			":envelope:"=>"U+2709", //✉
			":incoming_envelope:"=>"",
			":postal_horn:"=>"U+1F4EF", //📯
			":mailbox_closed:"=>"",
			":mailbox_with_mail:"=>"U+1F4EC", //📬
			":mailbox_with_no_mail:"=>"U+1F4ED", //📭
			":package:"=>"",
			":door:"=>"U+1F6AA", //🚪
			":smoking:"=>"U+1F6AC", //🚬
			":bomb:"=>"",
			":gun:"=>"",
			":hocho:"=>"",
			":pill:"=>"",
			":syringe:"=>"",
			":page_facing_up:"=>"",
			":page_with_curl:"=>"",
			":bookmark_tabs:"=>"",
			":bar_chart:"=>"",
			":chart_with_upwards_trend:"=>"",
			":chart_with_downwards_trend:"=>"",
			":scroll:"=>"",
			":clipboard:"=>"",
			":calendar:"=>"",
			":date:"=>"",
			":card_index:"=>"",
			":file_folder:"=>"",
			":open_file_folder:"=>"",
			":scissors:"=>"U+2702", //✂
			":pushpin:"=>"",
			":paperclip:"=>"",
			":black_nib:"=>"U+2712", //✒
			":pencil2:"=>"U+270F", //✏
			":straight_ruler:"=>"",
			":triangular_ruler:"=>"",
			":closed_book:"=>"",
			":green_book:"=>"",
			":blue_book:"=>"",
			":orange_book:"=>"",
			":notebook:"=>"",
			":notebook_with_decorative_cover:"=>"",
			":ledger:"=>"",
			":books:"=>"",
			":bookmark:"=>"",
			":name_badge:"=>"",
			":microscope:"=>"U+1F52C", //🔬
			":telescope:"=>"U+1F52D", //🔭
			":newspaper:"=>"",
			":football:"=>"", 
			":basketball:"=>"",
			":soccer:"=>"",
			":baseball:"=>"",
			":tennis:"=>"",
			":8ball:"=>"",
			":rugby_football:"=>"U+1F3C9", //🏉
			":bowling:"=>"",
			":golf:"=>"",
			":mountain_bicyclist:"=>"U+1F6B5", //🚵
			":bicyclist:"=>"U+1F6B4", //🚴
			":horse_racing:"=>"U+1F3C7", //🏇
			":snowboarder:"=>"",
			":swimmer:"=>"",
			":surfer:"=>"",
			":ski:"=>"",
			":spades:"=>"",
			":hearts:"=>"",
			":clubs:"=>"",
			":diamonds:"=>"",
			":gem:"=>"",
			":ring:"=>"",
			":trophy:"=>"",
			":musical_score:"=>"",
			":musical_keyboard:"=>"",
			":violin:"=>"",
			":space_invader:"=>"",
			":video_game:"=>"",
			":black_joker:"=>"",
			":flower_playing_cards:"=>"",
			":game_die:"=>"",
			":dart:"=>"",
			":mahjong:"=>"",
			":clapper:"=>"",
			":memo:"=>"",
			":pencil:"=>"",
			":book:"=>"",
			":art:"=>"",
			":microphone:"=>"",
			":headphones:"=>"",
			":trumpet:"=>"",
			":saxophone:"=>"",
			":guitar:"=>"",
			":shoe:"=>"",
			":sandal:"=>"",
			":high_heel:"=>"",
			":lipstick:"=>"",
			":boot:"=>"",
			":shirt:"=>"",
			":tshirt:"=>"",
			":necktie:"=>"",
			":womans_clothes:"=>"",
			":dress:"=>"",
			":running_shirt_with_sash:"=>"",
			":jeans:"=>"",
			":kimono:"=>"",
			":bikini:"=>"",
			":ribbon:"=>"",
			":tophat:"=>"",
			":crown:"=>"",
			":womans_hat:"=>"",
			":mans_shoe:"=>"",
			":closed_umbrella:"=>"",
			":briefcase:"=>"",
			":handbag:"=>"",
			":pouch:"=>"",
			":purse:"=>"",
			":eyeglasses:"=>"",
			":fishing_pole_and_fish:"=>"",
			":coffee:"=>"",
			":tea:"=>"",
			":sake:"=>"",
			":baby_bottle:"=>"U+1F37C", //🍼
			":beer:"=>"",
			":beers:"=>"",
			":cocktail:"=>"",
			":tropical_drink:"=>"",
			":wine_glass:"=>"",
			":fork_and_knife:"=>"",
			":pizza:"=>"",
			":hamburger:"=>"",
			":fries:"=>"",
			":poultry_leg:"=>"",
			":meat_on_bone:"=>"",
			":spaghetti:"=>"",
			":curry:"=>"",
			":fried_shrimp:"=>"",
			":bento:"=>"",
			":sushi:"=>"",
			":fish_cake:"=>"",
			":rice_ball:"=>"",
			":rice_cracker:"=>"",
			":rice:"=>"",
			":ramen:"=>"",
			":stew:"=>"",
			":oden:"=>"",
			":dango:"=>"",
			":egg:"=>"",
			":bread:"=>"",
			":doughnut:"=>"",
			":custard:"=>"",
			":icecream:"=>"",
			":ice_cream:"=>"",
			":shaved_ice:"=>"",
			":birthday:"=>"",
			":cake:"=>"",
			":cookie:"=>"",
			":chocolate_bar:"=>"",
			":candy:"=>"",
			":lollipop:"=>"",
			":honey_pot:"=>"",
			":apple:"=>"",
			":green_apple:"=>"",
			":tangerine:"=>"",
			":lemon:"=>"U+1F34B", //🍋
			":cherries:"=>"",
			":grapes:"=>"",
			":watermelon:"=>"",
			":strawberry:"=>"",
			":peach:"=>"",
			":melon:"=>"",
			":banana:"=>"",
			":pear:"=>"U+1F350", //🍐
			":pineapple:"=>"",
			":sweet_potato:"=>"",
			":eggplant:"=>"",
			":tomato:"=>"",
			":corn:"=>"",
			
			//Places
			":house:"=>"",
			":house_with_garden:"=>"",
			":school:"=>"",
			":office:"=>"",
			":post_office:"=>"",
			":hospital:"=>"",
			":bank:"=>"",
			":convenience_store:"=>"",
			":love_hotel:"=>"",
			":hotel:"=>"",
			":wedding:"=>"",
			":church:"=>"",
			":department_store:"=>"",
			":european_post_office:"=>"U+1F3E4", //🏤
			":city_sunrise:"=>"",
			":city_sunset:"=>"",
			":japanese_castle:"=>"",
			":european_castle:"=>"",
			":tent:"=>"",
			":factory:"=>"",
			":tokyo_tower:"=>"U+1F5FC", //🗼
			":japan:"=>"U+1F5FE", //🗾
			":mount_fuji:"=>"U+1F5FB", //🗻
			":sunrise_over_mountains:"=>"",
			":sunrise:"=>"",
			":stars:"=>"",
			":statue_of_liberty:"=>"U+1F5FD", //🗽
			":bridge_at_night:"=>"",
			":carousel_horse:"=>"",
			":rainbow:"=>"",
			":ferris_wheel:"=>"",
			":fountain:"=>"",
			":roller_coaster:"=>"",
			":ship:"=>"U+1F6A2", //🚢
			":speedboat:"=>"U+1F6A4", //🚤
			":boat:"=>"",
			":sailboat:"=>"",
			":rowboat:"=>"U+1F6A3", //🚣
			":anchor:"=>"",
			":rocket:"=>"U+1F680", //🚀
			":airplane:"=>"U+2708", //✈
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
			":car:"=>"U+1F697", //🚗
			":red_car:"=>"",
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
			":train:"=>"",
			":station:"=>"U+1F689", //🚉
			":train2:"=>"U+1F686", //🚆
			":bullettrain_front:"=>"U+1F685", //🚅
			":bullettrain_side:"=>"U+1F684", //🚄
			":light_rail:"=>"U+1F688", //🚈
			":monorail:"=>"U+1F69D", //🚝
			":railway_car:"=>"U+1F683", //🚃
			":trolleybus:"=>"U+1F68E", //🚎
			":ticket:"=>"",
			":fuelpump:"=>"",
			":vertical_traffic_light:"=>"U+1F6A6", //🚦
			":traffic_light:"=>"U+1F6A5", //🚥
			":warning:"=>"",
			":construction:"=>"U+1F6A7", //🚧
			":beginner:"=>"",
			":atm:"=>"",
			":slot_machine:"=>"",
			":busstop:"=>"U+1F68F", //🚏
			":barber:"=>"",
			":hotsprings:"=>"",
			":checkered_flag:"=>"",
			":crossed_flags:"=>"",
			":izakaya_lantern:"=>"",
			":moyai:"=>"U+1F5FF", //🗿
			":circus_tent:"=>"",
			":performing_arts:"=>"",
			":round_pushpin:"=>"",
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
			":uk:"=>"U+1F1ECU+1F1E7", //🇬🇧 = :gb:
			":de:"=>"U+1F1E9U+1F1EA", //🇩🇪
				
			//Symbols
			":one:"=>"",
			":two:"=>"",
			":three:"=>"",
			":four:"=>"",
			":five:"=>"",
			":six:"=>"",
			":seven:"=>"",
			":eight:"=>"",
			":nine:"=>"",
			":keycap_ten:"=>"",
			":1234:"=>"",
			":zero:"=>"",
			":hash:"=>"",
			":symbols:"=>"",
			":arrow_backward:"=>"",
			":arrow_down:"=>"",
			":arrow_forward:"=>"",
			":arrow_left:"=>"",
			":capital_abcd:"=>"",
			":abcd:"=>"",
			":abc:"=>"",
			":arrow_lower_left:"=>"",
			":arrow_lower_right:"=>"",
			":arrow_right:"=>"U+27A1", //➡
			":arrow_up:"=>"",
			":arrow_upper_left:"=>"",
			":arrow_upper_right:"=>"",
			":arrow_double_down:"=>"",
			":arrow_double_up:"=>"",
			":arrow_down_small:"=>"",
			":arrow_heading_down:"=>"",
			":arrow_heading_up:"=>"",
			":leftwards_arrow_with_hook:"=>"",
			":arrow_right_hook:"=>"",
			":left_right_arrow:"=>"",
			":arrow_up_down:"=>"",
			":arrow_up_small:"=>"",
			":arrows_clockwise:"=>"",
			":arrows_counterclockwise:"=>"U+1F504", //🔄
			":rewind:"=>"",
			":fast_forward:"=>"",
			":information_source:"=>"",
			":ok:"=>"U+1F197", //🆗
			":twisted_rightwards_arrows:"=>"U+1F500", //🔀
			":repeat:"=>"U+1F501", //🔁
			":repeat_one:"=>"U+1F502", //🔂
			":new:"=>"U+1F195", //🆕
			":top:"=>"",
			":up:"=>"U+1F199", //🆙
			":cool:"=>"U+1F192", //🆒
			":free:"=>"U+1F193", //🆓
			":ng:"=>"U+1F196", //🆖
			":cinema:"=>"",
			":koko:"=>"U+1F201", //🈁
			":signal_strength:"=>"",
			":u5272:"=>"U+1F239", //🈹
			":u5408:"=>"U+1F234", //🈴
			":u55b6:"=>"U+1F23A", //🈺
			":u6307:"=>"U+1F22F", //🈯
			":u6708:"=>"U+1F237", //🈷
			":u6709:"=>"U+1F236", //🈶
			":u6e80:"=>"U+1F235", //🈵
			":u7121:"=>"U+1F21A", //🈚
			":u7533:"=>"U+1F238", //🈸
			":u7a7a:"=>"U+1F233", //🈳
			":u7981:"=>"U+1F232", //🈲
			":sa:"=>"U+1F202", //🈂
			":restroom:"=>"U+1F6BB", //🚻
			":mens:"=>"U+1F6B9", //🚹
			":womens:"=>"U+1F6BA", //🚺
			":baby_symbol:"=>"U+1F6BC", //🚼
			":no_smoking:"=>"U+1F6AD", //🚭
			":parking:"=>"U+1F17F", //🅿
			":wheelchair:"=>"",
			":metro:"=>"U+1F687", //🚇
			":baggage_claim:"=>"U+1F6C4", //🛄
			":accept:"=>"U+1F251", //🉑
			":wc:"=>"U+1F6BE", //🚾
			":potable_water:"=>"U+1F6B0", //🚰
			":put_litter_in_its_place:"=>"U+1F6AE", //🚮
			":secret:"=>"",
			":congratulations:"=>"",
			":m:"=>"U+24C2", //Ⓜ
			":passport_control:"=>"U+1F6C2", //🛂
			":left_luggage:"=>"U+1F6C5", //🛅
			":customs:"=>"U+1F6C3", //🛃
			":ideograph_advantage:"=>"U+1F250", //🉐
			":cl:"=>"U+1F191", //🆑
			":sos:"=>"U+1F198", //🆘
			":id:"=>"U+1F194", //🆔
			":no_entry_sign:"=>"U+1F6AB", //🚫
			":underage:"=>"",
			":no_mobile_phones:"=>"U+1F4F5", //📵
			":do_not_litter:"=>"U+1F6AF", //🚯
			":non-potable_water:"=>"U+1F6B1", //🚱
			":no_bicycles:"=>"U+1F6B3", //🚳
			":no_pedestrians:"=>"U+1F6B7", //🚷
			":children_crossing:"=>"U+1F6B8", //🚸
			":no_entry:"=>"",
			":eight_spoked_asterisk:"=>"U+2733", //✳
			":sparkle:"=>"U+2747", //❇
			":eight_pointed_black_star:"=>"U+2734", //✴
			":heart_decoration:"=>"",
			":vs:"=>"U+1F19A", //🆚
			":vibration_mode:"=>"",
			":mobile_phone_off:"=>"",
			":chart:"=>"",
			":currency_exchange:"=>"",
			":aries:"=>"",
			":taurus:"=>"",
			":gemini:"=>"",
			":cancer:"=>"",
			":leo:"=>"",
			":virgo:"=>"",
			":libra:"=>"",
			":scorpius:"=>"",
			":sagittarius:"=>"",
			":capricorn:"=>"",
			":aquarius:"=>"",
			":pisces:"=>"",
			":ophiuchus:"=>"",
			":six_pointed_star:"=>"",
			":negative_squared_cross_mark:"=>"U+274E", //❎
			":a:"=>"U+1F170", //🅰
			":b:"=>"U+1F171", //🅱
			":ab:"=>"U+1F18E", //🆎
			":o2:"=>"U+1F17E", //🅾
			":diamond_shape_with_a_dot_inside:"=>"",
			":recycle:"=>"",
			":end:"=>"",
			":back:"=>"",
			":on:"=>"",
			":soon:"=>"",
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
			":heavy_dollar_sign:"=>"",
			":copyright:"=>"U+00A9", //©
			":registered:"=>"U+00AE", //®
			":tm:"=>"",
			":x:"=>"U+274C", //❌
			":heavy_exclamation_mark:"=>"",
			":bangbang:"=>"",
			":interrobang:"=>"",
			":o:"=>"",
			":heavy_multiplication_x:"=>"U+2716", //✖
			":heavy_plus_sign:"=>"U+2795", //➕
			":heavy_minus_sign:"=>"U+2796", //➖
			":heavy_division_sign:"=>"U+2797", //➗
			":white_flower:"=>"",
			":100:"=>"",
			":heavy_check_mark:"=>"U+2714", //✔
			":ballot_box_with_check:"=>"",
			":radio_button:"=>"",
			":link:"=>"",
			":curly_loop:"=>"U+27B0", //➰
			":wavy_dash:"=>"",
			":part_alternation_mark:"=>"",
			":trident:"=>"",
			":black_small_square:"=>"",
			":white_small_square:"=>"",
			":black_medium_small_square:"=>"",
			":white_medium_small_square:"=>"",
			":black_medium_square:"=>"",
			":white_medium_square:"=>"",
			":black_large_square:"=>"",
			":white_large_square:"=>"",
			":white_check_mark:"=>"U+2705", //✅
			":black_square_button:"=>"",
			":white_square_button:"=>"",
			":black_circle:"=>"",
			":white_circle:"=>"",
			":red_circle:"=>"",
			":large_blue_circle:"=>"",
			":large_blue_diamond:"=>"",
			":large_orange_diamond:"=>"",
			":small_blue_diamond:"=>"",
			":small_orange_diamond:"=>"",
			":small_red_triangle:"=>"",
			":small_red_triangle_down:"=>"",
			":shipit:"=>"",
			
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