<?php
/**
 * Templify - the native php5 Template Engine
 *
 * This library is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.
 * If not, see <{@link http://www.gnu.org/licenses/lgpl-3.0.txt}>.
 *
 * @author Felix Middendorf
 * @copyright 2007-2009 Felix Middendorf
 * @link http://www.felixmiddendorf.eu
 * @link http://templify.googlecode.com
 * @package Templify
 * @version 0.3
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License 3.0
 */
class Templify{
	/**
	 * @see Templify::get()
	 */
	const ESCAPE = true;

	/**
	 * Version number of Templify
	 */
	const VERSION = '0.3';

	/**
	 * Stores all assignments made using Templify::assign().
	 *
	 * @var	array
	 * @see	Templify::assign()
	 */
	protected $assignments = array();

	/**
	 * Charset that is used. The default charset is: 'UTF-8'. Please consult {@link htmlentities} for a list of valid charsets.
	 *
	 * @var	string
	 * @static
	 */
	protected static $charset = 'UTF-8';

	/**
	 * Directory that contains the template files. The default value is './templates'
	 *
	 * @var	string
	 */
	protected $templateDirectory;

	/**
	 * Directory that is used to store cached files. The default value is './cache'
	 *
	 * @var	string
	 */
	protected $cacheDirectory;

	/**
	 * Lifetime of cached files in seconds. The default value is 300 seconds (5 minutes).
	 *
	 * @var	integer >= 0
	 */
	protected $cacheLifetime = 300;

	/**
	 * File extension of cached files. The default value is 'html'. Set to false to disable the use of file extensions.
	 *
	 * @var	mixed Either a file extension or false.
	 */
	protected $cacheFileExtension = 'html';

	/**
	 * Enables/disables caching. Caching is disabled by default (false).
	 *
	 * @var	boolean
	 */
	protected $caching = false;

	/**
	 * Appends a html comment to each cached template if set to true.
	 * Default value is false.
	 *
	 * @var	boolean
	 */
	protected $verboseCaching = false;

	/**
	 * In order to increase performance, templates
	 * are not checked for changes when serving cached files in production mode.
	 * Furthermore the syntax of names/keys is not checked in production mode.
	 * Production mode is disabled by default in order to ease the development of neat
	 * templates (false).
	 * In order to improve performance it is highly recommended to enable production
	 * mode once the template files are stable.
	 *
	 * @var	boolean
	 */
	protected $production = false;

	/**
	 * The algorithm that is used in order to hash the names of cache files.
	 * The default algorithm is 'sha256'. Setting <var>Template::$hash</var> to
	 * false disables the use of hashed file names.
	 *
	 * @link 	http://php.net/manual/en/function.hash-algos.php
	 * @var		mixed	Either a valid algorithm name or false
	 */
	protected $hash = 'sha256';

	/**
	 * The default constructor Templify::__construct() initializes the template
	 * as well as the cache directory.
	 *
	 * Please ensure that constructors of inheriting classes explicitly call Templify's
	 * constructor in the following manner:
	 * <code>
	 * class MyVeryOwnTemplify extends Templify{
	 *  public function __construct(){
	 *     parent::__construct();
	 * 	   //...
	 *   }
	 * }
	 * </code>
	 */
	public function __construct(){
		$this->setDir('.'.DIRECTORY_SEPARATOR.'templates');
		$this->setCacheDir('.'.DIRECTORY_SEPARATOR.'cache');
	}

	/**
	 * Sets the charset that is used when escaping text.
	 *
	 * @param	string	$charset the charset that will be used.
	 * @see		Templify::$charset
	 * @static
	 **/
	public static function setCharset($charset){
		self::$charset = (string) $charset;
	}

	/**
	 * Returns the charset that will be used when parsing the template.
	 *
	 * @static
	 * @return	string	<var>Templify::$charset</var>
	 * @see		Templify::$charset
	 **/
	public static function getCharset(){
		return self::$charset;
	}

	/**
	 * Sets the directory that contains the template files.
	 *
	 * @param	string	$dir	Directory that contains the template files
	 * @see		Templify::$templateDirectory
	 */
	public function setDir($dir){
		$this->templateDirectory = (string) $dir;
	}

	/**
	 * Returns the directory that contains the template files.
	 *
	 * @see		Templify::$templateDirectory
	 * @return	string	<var>Templify::$templateDirectory</var>
	 */
	public function getDir(){
		return $this->templateDirectory;
	}

	/**
	 * Sets the directory that is used to store cached files.
	 *
	 * @param	string	$dir	Cache directory
	 * @see		Templify::$cacheDirectory
	 */
	public function setCacheDir($dir){
		$this->cacheDirectory = (string) $dir;
	}

	/**
	 * Returns the directory that is used to store cache files.
	 *
	 * @see 	Templify::$cacheDirectory
	 * @return 	string	<var>Templify::$cacheDirectory</var>
	 */
	public function getCacheDir(){
		return $this->cacheDirectory;
	}

	/**
	 * Enables/disables caching.
	 *
	 * @param	boolean	$cache	true enables, false disables caching
	 * @see		Templify::$caching
	 */
	public function setCaching($cache){
		$this->caching = (boolean) $cache;
	}

	/**
	 * Enables/disables production mode.
	 *
	 * @param	boolean	$production	true to enable, false to disable production mode
	 * @see Templify::$production
	 */
	public function setProduction($production){
		$this->production = (boolean) $production;
	}

	/**
	 * Sets the lifetime of cached files in seconds.
	 *
	 * @param	boolean	$lifetime	Cache lifetime in seconds
	 * @see 	Templify::$cacheLifetime
	 */
	public function setCacheLifetime($lifetime){
		if((int) $lifetime < 0){
			throw new Exception("Illegal Argument: lifetime must be at least 0.");
		}
		$this->cacheLifetime = (int) $lifetime;
	}

	/**
	 * Sets the algorithm that is used to hash the names of cached files.
	 *
	 * @link http://php.net/manual/en/function.hash-algos.php
	 * @param	mixed	$algorithm	Please refer to the php documentation to choose a valid hash algorithm.
	 * @see	Templify::$hash
	 */
	public function setHash($algorithm){
		if(!$this->production && !in_array($algorithm, hash_algos())){
			throw new Exception("{$algorithm} is not a valid hash algorithm");
		}
		$this->hash = ($algorithm === false)?false:((string) $algorithm);
	}

	/**
	 * Assigns a name/key to a value so that the value can be retrieved from within a template.
	 *
	 * Please note that:
	 * <ul>
	 *  <li><var>$name</var> must be a valid php variable name (please refer to {@link http://de.php.net/manual/en/language.variables.php}).</li>
	 *  <li>the syntax check of <var>$name</var> is skipped if Templify runs in production mode.</li>
	 * </ul>
	 *
	 * Some usage examples:
	 * <code>
	 * // in control
	 * $t = new Templify();
	 * $t->assign('firstname', 'Felix');
	 * $t->assign('active', true);
	 * $t->assign('likes', array('salad', 'pizza'));
	 * $t->assign('now', time());
	 * $t->assign('person', new Person();
	 *
	 * // in template file
	 * echo $firstname; #=> 'Felix'
	 * echo $this->get('firstname'); #=> 'Felix'
	 * echo $this->firstname; #=> 'Felix'
	 *
	 * foreach($likes as $food){
	 * 	echo $food.' ';
	 * }
	 * #=> 'salad pizza '
	 * </code>
	 *
	 * @param	string	$name	name/key
	 * @param	mixed	$value	value to be assigned to <var>$name</var>
	 */
	public function assign($name, $value){
		//there is probably a better regexp than this one
		if($this->production || preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',$name) === 1){
			$this->assignments[$name] = $value;
		}else{
			throw new Exception(get_class().": '{$name}' is not a valid name.");
		}
	}

	/**
	 * Assigns all the keys of an associative array to their values.
	 *
	 * Example:
	 * <code>
	 * //in control
	 * $t = new Templify();
	 * //...
	 * $t->assignAll(array(	'lastname'	=> 'Middendorf',
	 * 						'firstname'	=> 'Felix',
	 * 						'homepage'	=> 'http://www.felixmiddendorf.eu'));
	 *
	 * //in template file
	 * echo $this->firstname; #=> 'Felix'
	 * </code>
	 *
	 * @param	array	$array	key/value-pairs
	 * @see		Templify::assign()
	 */
	public function assignAll(array $array){
		foreach($array as $name => $value){
			$this->assign($name, $value);
		}
	}

	/**
	 * Retrieves the value that is assigned to <var>$name</var> in an object oriented manner.
	 * Please note that you can retrieve a keys value by using the magic getter, too.
	 *
	 * <code>
	 * //in template
	 * $this->get('firstname');
	 * $this->get('firstname', Templify::ESCAPE);
	 * </code>
	 *
	 * @param	string	$name	Name
	 * @param	boolean	$escape	Set <var>Templify::ESCAPE</var> in order to escape the retrieved assignment on the fly
	 * @return	string	the value that was assigned to <var>$name</var> or an empty string
	 * @see		Templify::escape()
	 * @see		Templify::__get()
	 */
	public function get($name, $escape = false){
		$toReturn = (array_key_exists($name,$this->assignments))?$this->assignments[$name]:'';
		return (($escape === self::ESCAPE)? self::escape($toReturn) : $toReturn);
	}

	/**
	 * Recursively escapes (multi-dimensional) arrays, strings and string representation of objects using
	 * the php library functions htmlentities as well as nl2br.
	 *
	 * Some usage examples:
	 * <code>
	 * // a string
	 * Templify::escape('<script>...</script>');
	 * #=> '&lt;script&gt;...&lt;/script&gt;'
	 *
	 * // an array containing html entities
	 * Templify::escape(array('c&a', 'h&m'));
	 * #=> array('c&amp;a','h&amp;m');
	 *
	 * // a multi-dimensional array
	 * Templify::escape(array(array('c','&','a'), array('h','&','m')));
	 * #=> array(array('c','&amp;','a'), array('h','&amp;','m'))
	 *
	 * // an object
	 * class Person{
	 * 		//...
	 * 		public function __toString(){ return 'hello & goodbye everybody!'; }
	 * 		//...
	 * }
	 * Templify::escape(new Person());
	 * #=> 'hello &amp; goodbye everybody!'
	 * </code>
	 *
	 * @param 	mixed 		$arg 		Argument that will be escaped (array, string or object)
	 * @param 	boolean 	$break		'new-lines' will be transformed into 'html-breaks' (<br>) if set to true. Default is true.
	 * @return 	mixed 					Escaped input
	 */
	public static function escape($arg, $break = true){
		if(is_string($arg)){
			$arg = htmlentities($arg,ENT_QUOTES,self::$charset);
			return ($break === true)?nl2br($arg):$arg;
		}elseif(is_array($arg)){
			// arrays are escaped recursively
			// unfortunately, array_map and the like cannot be used due to their lack of support for calling static methods
			foreach($arg as $key => $value){
				$arg[$key] = self::escape($value, $break);
			}
			return $arg;
		}elseif(is_object($arg)){
			// toString is called explicitly in order to support php <= 5.2.0
			return self::escape((method_exists($arg,'__toString'))?$arg->__toString():'Object of type '.(get_class($arg)),$break);
		}else{
			// does not need to be escaped
			return $arg;
		}
	}

	/**
	 * Parses a template and displays it. If caching is enabled, the cached file will be served.
	 *
	 * Example:
	 * <code>
	 * $t = new Templify();
	 * $t->assign('firstname', 'Felix');
	 * // some mores assignments...
	 * $t->parse('person_details.php');
	 * </code>
	 *
	 * @param	string	$templateFile		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 */
	public function parse($templateFile, $cacheIdentifier = '') {
		$pathToTemplateFile = $this->templateDirectory.DIRECTORY_SEPARATOR.$templateFile;
		if(!is_file($pathToTemplateFile)){
			throw new Exception("The requested template file '{$templateFile}' does not exist");
		}else{
			if($this->caching === true){
				$pathToCacheFile = $this->cacheFilename($templateFile, $cacheIdentifier);
				if($this->isCached($templateFile, $cacheIdentifier)){
					echo file_get_contents($pathToCacheFile);
				}else{
					echo $this->cache($templateFile, $cacheIdentifier);
				}
			}else{
				echo $this->compile($templateFile);
			}
		}
	}

	/**
	 * An alias of parse.
	 *
	 * @param	string	$templateFile		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 */
	public function display($templateFile, $cacheIdentifier = ''){
		$this->parse($templateFile, $cacheIdentifier);
	}

	/**
	 * Compiles a template file, but does not persist the result.
	 *
	 * @param	string	$templateFile	filename of the template
	 * @return 	string	Compiled template
	 */
	protected function compile($templateFile){
		$pathToTemplateFile = $this->templateDirectory.DIRECTORY_SEPARATOR.$templateFile;
		if(is_array($this->assignments)){
			extract($this->assignments);
		}
		//parse the template
		ob_start();
		require $pathToTemplateFile;
		$compiledTemplate = ob_get_contents();
		ob_end_clean();
		return $compiledTemplate;
	}

	/**
	 * Determines whether there is a cache file for <var>$templateFile</var> which did not exceed its lifetime.
	 *
	 * @param	string	$templateFile 		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 * @return	boolean	true, if cached. Else false.
	 */
	public function isCached($templateFile, $cacheIdentifier = ''){
		$cacheFile = $this->cacheFilename($templateFile, $cacheIdentifier);
		return ($this->caching &&
		file_exists($cacheFile) &&
		(time() - $this->cacheLifetime <= filemtime($cacheFile)) &&
		($this->production == true || filemtime($this->templateDirectory.DIRECTORY_SEPARATOR.$templateFile) <= filemtime($cacheFile)));
	}

	/**
	 * Caches a template file.
	 *
	 * @param	string	$templateFile		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 * @return	string	compiled template
	 */
	public function cache($templateFile, $cacheIdentifier = ''){
		$cacheFile = $this->cacheFilename($templateFile, $cacheIdentifier);
		$cachedTemplate = $this->compile($templateFile);
		if($this->verboseCaching){
			$time = (date('m/d/Y H:i'));
			$cachedTemplate .= "\n<!-- {$templateFile} cached ($time) -->\n";
		}
		if(@file_put_contents($cacheFile, $cachedTemplate) === false){
			throw new Exception("Templify could not write the cache directory '{$this->cacheDirectory}'");
		}else{
			return $cachedTemplate;
		}
	}

	/**
	 * Deletes a cached template.
	 *
	 * @param	string	$templateFile		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 * @return	boolean	true if successful
	 */
	public function uncache($templateFile, $cacheIdentifier = ''){
		$cacheFile = $this->cacheFilename($templateFile, $cacheIdentifier);
		if(file_exists($cacheFile) && @unlink($cacheFile) !== true){
			throw new Exception("Could not delete files within the cache directory '{$this->cacheDirectory}");
		}else{
			return true;
		}
	}

	/**
	 * Returns the path and filename of a cached file.
	 *
	 * @param	string	$templateFile 		name of the template file
	 * @param	string	$cacheIdentifier	e.g. primary key
	 * @return	string	path and filename of a cached file
	 */
	protected function cacheFilename($templateFile, $cacheIdentifier = ''){
		$cacheFile = $templateFile.((strlen($cacheIdentifier) > 0)?'_'.$cacheIdentifier:'');
		return $this->cacheDirectory.DIRECTORY_SEPARATOR.(($this->hash !== false)?hash($this->hash,$cacheFile):$cacheFile).(($this->cacheFileExtension !== false)?'.'.$this->cacheFileExtension:'');
	}

	/**
	 * Returns the class name ('Templify' or the name of the inheriting class) as well as the version number of Templify.
	 *
	 * Example:
	 * <code>
	 * //in control
	 * $t = new Templify();
	 * echo $t;
	 * echo $t->__toString();
	 *
	 * //in template
	 * echo $this;
	 * echo $this->__toString();
	 * </code>
	 *
	 * @return	string
	 * @link	http://php.net/manual/en/language.oop5.magic.php
	 */
	public function __toString(){
		return 'Instance of '.get_class($this).' '.self::VERSION;
	}

	/**
	 * A magic getter for assignments as specified in {@link http://php.net/manual/en/language.oop5.overloading.php}.
	 *
	 * Example:
	 * <code>
	 * // in template file
	 * echo $this->firstname;
	 * echo $this->lastname;
	 * echo $this->homepage;
	 * </code>
	 *
	 * @see		Templify::get()
	 * @see		Templify::set();
	 * @return	string	the value that is assigned to <var>$name</var>
	 * @param	string	$name	name/key of assignment
	 */
	public function __get($name){
		return $this->get($name);
	}

	/**
	 * A magic setter for assignments as specified in {@link http://php.net/manual/en/language.oop5.overloading.php}.
	 *
	 * Example:
	 * <code>
	 * // in control
	 * $t = new Templify();
	 * $t->name = 'Felix';
	 * // same as
	 * $t->assign('name', 'Felix');
	 * </code>
	 * @see		Templify::assign()
	 * @see		Templify::__get()
	 * @param	$name	name/key of assignment
	 * @param	$value	the value that is to be assigned to <var>$name</var>
	 */
	public function __set($name, $value){
		$this->assign($name, $value);
	}
}

if(!function_exists('h')){
	/**
	 * Alias for Templify::escape() that is only defined if there is not already another function called h().
	 *
	 * @see	Templify::escape()
	 * @param	mixed	$input		String, object or array that will be escaped
	 * @param	boolean	$break		new-lines ('\n') will be converted to html breaks if <var>$break</var> is true
	 * @return	mixed	escaped input
	 */
	function h($input, $break = true){
		return Templify::escape($input, $break);
	}
}