<?php
/**
 * New Page Button plugin
 *
 * Syntax: <NEWPAGE> will be replaced with a form asking for
 *  the (URL) name of the page and a button. You can use these
 *  options: <NEWPAGE namespace|Button text> to specify
 *  where the page should go. Submitting the form goes directly
 *  to the edit screen.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Oleg Lavrovsky <loleg at hotmail dot com>
 */
 
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
 
/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_newpage extends DokuWiki_Syntax_Plugin {
 
   /**
    * Get an associative array with plugin info.
    *
    * <p>
    * The returned array holds the following fields:
    * <dl>
    * <dt>author</dt><dd>Author of the plugin</dd>
    * <dt>email</dt><dd>Email address to contact the author</dd>
    * <dt>date</dt><dd>Last modified date of the plugin in
    * <tt>YYYY-MM-DD</tt> format</dd>
    * <dt>name</dt><dd>Name of the plugin</dd>
    * <dt>desc</dt><dd>Short description of the plugin (Text only)</dd>
    * <dt>url</dt><dd>Website with more information on the plugin
    * (eg. syntax description)</dd>
    * </dl>
    * @param none
    * @return Array Information about this plugin class.
    * @public
    * @static
    */
    function getInfo(){
        return array(
            'author' => 'Oleg Lavrovsky',
            'email'  => 'loleg@hotmail.com',
            'date'   => '2013-2-1',
            'name'   => 'New Page Button Plugin',
            'desc'   => 'Simplifies page creation for users',
            'url'    => 'http://oleg.utou.ch',
        );
    }
 
   /**
    * Get the type of syntax this plugin defines.
    *
    * @param none
    * @return String <tt>'substition'</tt> (i.e. 'substitution').
    * @public
    * @static
    */
    function getType(){
        return 'substition';
    }
 
    /**
     * What kind of syntax do we allow (optional)
     */
//    function getAllowedTypes() {
//        return array();
//    }
 
   /**
    * Define how this plugin is handled regarding paragraphs.
    *
    * <p>
    * This method is important for correct XHTML nesting. It returns
    * one of the following values:
    * </p>
    * <dl>
    * <dt>normal</dt><dd>The plugin can be used inside paragraphs.</dd>
    * <dt>block</dt><dd>Open paragraphs need to be closed before
    * plugin output.</dd>
    * <dt>stack</dt><dd>Special case: Plugin wraps other paragraphs.</dd>
    * </dl>
    * @param none
    * @return String <tt>'block'</tt>.
    * @public
    * @static
    */
    function getPType(){
        return 'block';
    }
 
   /**
    * Where to sort in?
    *
    * @param none
    * @return Integer <tt>6</tt>.
    * @public
    * @static
    */
    function getSort(){
        return 999;
    }
 
 
   /**
    * Connect lookup pattern to lexer.
    *
    * @param $aMode String The desired rendermode.
    * @return none
    * @public
    * @see render()
    */
    function connectTo($mode) {
      $this->Lexer->addSpecialPattern('<NEWPAGE.*?>',$mode,'plugin_newpage');
    } 
 
   /**
    * Handler to prepare matched data for the rendering process.
    *
    * <p>
    * The <tt>$aState</tt> parameter gives the type of pattern
    * which triggered the call to this method:
    * </p>
    * <dl>
    * <dt>DOKU_LEXER_ENTER</dt>
    * <dd>a pattern set by <tt>addEntryPattern()</tt></dd>
    * <dt>DOKU_LEXER_MATCHED</dt>
    * <dd>a pattern set by <tt>addPattern()</tt></dd>
    * <dt>DOKU_LEXER_EXIT</dt>
    * <dd> a pattern set by <tt>addExitPattern()</tt></dd>
    * <dt>DOKU_LEXER_SPECIAL</dt>
    * <dd>a pattern set by <tt>addSpecialPattern()</tt></dd>
    * <dt>DOKU_LEXER_UNMATCHED</dt>
    * <dd>ordinary text encountered within the plugin's syntax mode
    * which doesn't match any pattern.</dd>
    * </dl>
    * @param $aMatch String The text matched by the patterns.
    * @param $aState Integer The lexer state for the match.
    * @param $aPos Integer The character position of the matched text.
    * @param $aHandler Object Reference to the Doku_Handler object.
    * @return Integer The current lexer state for the match.
    * @public
    * @see render()
    * @static
    */
    function handle($match, $state, $pos, &$handler){
    
        $match = substr($match,9,-1); // Strip markup
        $match = preg_split('/\|/u',$match,2); // Split commands
 
        if (!isset($match[1])) $match[1] = "New page";
    
        return $match;
    }
 
   /**
    * Handle the actual output creation.
    *
    * <p>
    * The method checks for the given <tt>$aFormat</tt> and returns
    * <tt>FALSE</tt> when a format isn't supported. <tt>$aRenderer</tt>
    * contains a reference to the renderer object which is currently
    * handling the rendering. The contents of <tt>$aData</tt> is the
    * return value of the <tt>handle()</tt> method.
    * </p>
    * @param $aFormat String The output format to generate.
    * @param $aRenderer Object A reference to the renderer object.
    * @param $aData Array The data created by the <tt>handle()</tt>
    * method.
    * @return Boolean <tt>TRUE</tt> if rendered successfully, or
    * <tt>FALSE</tt> otherwise.
    * @public
    * @see handle()
    */
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            $renderer->doc .= $this->FormContent($data);
             // ptype = 'block'
            return true;
        }
        return false;
    }
    
    function FormContent($data) {    
        $mr_namespace = $data[0];
        $mr_buttontext = $data[1];
        if ($mr_namespace != '') {
            $mr_namespace .= ':';
        }
        $mr = '';
        $mr .= '<form class="newpage" action="#"';
        $mr .= 'onSubmit="document.location.href=\'doku.php?do=edit&id=';
        if ($mr_namespace == 'user:') {
            $mr .= 'user:' . $_SERVER['REMOTE_USER'] . '\'; return false;">';
        } else {
            $mr .= $mr_namespace . '\' + document.getElementById(\'newpage_id\').value.replace(/[-\\\\.,_*+&#?^$[\](){}!=|]/g,\'\').replace(/\s/g,\'_\').toLowerCase(); return false">';
            $mr .= '<input id="newpage_id" type="text" size="10" />';
        }
        $mr .= '<input type="submit" value="' . $mr_buttontext . '" /></form>';
        return $mr;
    }
}
 
//Setup VIM: ex: et ts=4 enc=utf-8 :
?>
