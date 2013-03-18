<?php 

PL_Cache::init();
class PL_Cache {

	const TTL_LOW  = 900; // 15 minutes
	const TTL_HIGH = 172800; // 48 hours

	private static $log_enabled = false;
	const LOG_PATH = "/Users/iantendick/dev/wp_cache.log";
	
	private static $offset_key = 'pl_cache_offset';
	public static $offset = 0;

	public $type = 'general';
	public $transient_id = false;

	function __construct ($type = 'general') {
		self::$offset = get_option(self::$offset_key, 0);
		$this->type = preg_replace( "/\W/", "_", strtolower($type) );
	}

	public static function init () {
		// Allow cache to be cleared by going to url like http://example.com/?clear_cache
		if(isset($_GET['clear_cache']) || isset($_POST['clear_cache'])) {
			// style-util.php calls its PLS_Style::init() immediately
			// so this can't be tied to a hook
			self::invalidate();
		}

		// Invalidate cache when site's theme is changed...
		add_action('switch_theme', array(__CLASS__, 'invalidate'));
		
		// Flush cache when posts are trashed or untrashed -pek
		add_action('wp_trash_post', array(__CLASS__, 'invalidate'));
		add_action('untrash_post', array(__CLASS__, 'invalidate'));
	}

	function get () {
		// Just ignore caching for admins and regular folk too!
		if (is_admin() || is_admin_bar_showing() || is_user_logged_in()) {
			return false;
		}

		// Backdoor to ignore the cache completely
		if (isset($_GET['no_cache']) || isset($_POST['no_cache'])) {
			return false;
		}
	
		$func_args = func_get_args();
		$arg_hash = rawToShortMD5( MD5_85_ALPHABET, md5(http_build_query($func_args), true) );
		$this->transient_id = 'pl_' . $this->type . '_' . self::$offset . '_' . $arg_hash;
        
        $transient = get_transient($this->transient_id);
        // Return as is -- if transient doesn't exist, it's up to the caller to check...
        return $transient;
	}

	public function save ($result, $duration = 172800) {
		// Don't save any content from logged in users
		// We were getting things like "log out" links cached
		if ($this->transient_id && !is_user_logged_in()) {
			set_transient($this->transient_id, $result , $duration);
		}
	}

	public static function clear() {
		// TODO: Allow user to clear by type...
	}

	public static function delete($option_name) {
		$option_name = str_replace('_transient_', '', $option_name);
		$result = delete_transient( $option_name );
		return $result;
	}

	public static function invalidate() {
		// Retrieve the latest offset value 
		$new_offset = get_option(self::$offset_key, 0);
		$new_offset += 1;

		// Reset offset if value is high enough...
		if ($new_offset > 99) {
			$new_offset = 0;
		}

		// Update the option, then update the static variable
		update_option(self::$offset_key, $new_offset);
		self::$offset = $new_offset;
	}

/*
 * Cache logging functionality...
 */

	private static function cache_log ($msg) {
		if ( !empty($msg) && self::$log_enabled ) {
			$msg = '[' . date("M-d-Y g:i A T") . '] ' . $msg . "\n";
			error_log($msg, 3, self::LOG_PATH);
		}
	}

	private static function log_trace ($trace) {
		// Print the file, the function in that file, and the specific line where the given caching call 
		// is being made from to the cache log...
		if ( isset($trace[1]) ) {
			$file = str_replace('/Users/iantendick/Dev/wordpress/', '', @$trace[1]['file']);
			$caller = $file . ', ' . @$trace[2]['function'] . ', ' . @$trace[1]['line'];
			self::cache_log('Caller: ' . $caller);
		}
	}

//end class
}

// Flush our cache when admins save option pages or configure widgets
add_action('init', 'PL_Options_Save_Flush');
function PL_Options_Save_Flush() {
	// Check if options are being saved
	$doing_ajax = ( defined('DOING_AJAX') && DOING_AJAX );
	$editing_widgets = ( isset($_GET['savewidgets']) || isset($_POST['savewidgets']));
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_admin() && (!$doing_ajax || $editing_widgets)) {

		// Flush the cache
		PL_Cache::invalidate();
	}
}

/* Functions for converting between notations and short MD5 generation.
 * No license (public domain) but backlink is always welcome :)
 * By Proger_XP. http://proger.i-forge.net/Short_MD5
 * Usage: rawToShortMD5(MD5_85_ALPHABET, md5($str, true))
 * (passing true as the 2nd param to md5 returns raw binary, not a hex-encoded 32-char string)
 */
define('MD5_24_ALPHABET', '0123456789abcdefghijklmnopqrstuvwxyzABCDE');
define('MD5_85_ALPHABET', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()"|;:?\/\'[]<>');

function RawToShortMD5($alphabet, $raw) {
  $result = '';
  $length = strlen(DecToBase($alphabet, 2147483647));

  foreach (str_split($raw, 4) as $dword) {
    $dword = ord($dword[0]) + ord($dword[1]) * 256 + ord($dword[2]) * 65536 + ord($dword[3]) * 16777216;
    $result .= str_pad(DecToBase($alphabet, $dword), $length, $alphabet[0], STR_PAD_LEFT);
  }

  return $result;
}

function DecToBase($alphabet, $dword) {
  $rem = fmod($dword, strlen($alphabet));
  if ($dword < strlen($alphabet)) {
    return $alphabet[(int) $rem];
  } else {
    return DecToBase($alphabet, ($dword - $rem) / strlen($alphabet)).$alphabet[(int) $rem];
  }
}