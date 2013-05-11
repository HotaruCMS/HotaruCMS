<?php
/**
 * api: php
 * type: functions
 * title: plugin meta data
 * description: extracts .php plugin meta info, handles dependencies
 * priority: never
 * category: library
 * author: milky
 * license: Public Domain
 * version: 1.4
 * 
 * 
 *  Utility code for reading plugin meta data (pmd) and
 *  dependencies.
 *
 *  Upon reading all .php plugin files, it builds up two lists.
 *  One containing all plugin description data ($this->plugin),
 *  and the other with configsetting values ($this->config).
 *  Both are indexed by plugin $id or config/varname.
 *  Additionaly lists for dependencies are then extracted, too.
 *
 *  http://en.php-resource.de/scripte/script,582,Generic-PHP-Config-and-Plugin-System.htm
 */



/**
 * Reads in plugin .php files, and parses meta data header (see top
 * comment of this one for an example). Provides data normalization
 * and some grouping functions, resolves some dependencies.
 *
 */
class generic_pmd {


   /**
    * plugin meta data
    *
    */
   var $plugin = array();      // id->row hash, with individual info fields
   var $config = array();      // extracted config variables

   /**
    * separated dependency fields
    * hash id->(id,id,...)
    *
    */   
   var $depends = array();     // enforced dependencies, +virtnames
   var $replaces = array();    // negative dependencies, +virtnames
   var $suggests = array();    // relaxed dep
   var $conflicts = array();   // similar to $replaces, but keeps current plugin off if other enabled

   /**
    * virtual plugin names are used by depends: and replaces:
    *
    */
   var $provides = array();    // hash alias->(id,id,id,) list
   
   
   /**
    * allows to read in WordPress plugin comment too,
    * http://codex.wordpress.org/Writing_a_Plugin
    *
    */
   var $wp_compat = array(
      "plugin name" => "title",
      "description" => "description",
      "plugin uri" => "url",
      "author uri" => "author_url",
   );



   /**
    * constructor stub
    *
    */
   function generic_pmd() {
      
#      $this->pmd = & $this->plugin;   // reference (old name)
      
   }




   /**
    * get meta info data from a single .php plugin
    * - we only read first 4KiB of file
    *
    */
   function read($fn, $size=4096) {
   
      // read from given filename
      if (file_exists($fn) and ($f = fopen($fn, "r"))) {
         $src = fread($f, $size);
         fclose($f);
         $src .= "\n*/?>";   // add this, else parsing for /*..*/ might fail, because file possible shortened to 4096 byte
         return $this->parse($src);
      }
      // file not found/readable
      else {
        //echo "The file '" . $fn . "' couldn't be found in your plugins folder.";
        //echo "uh-oh, file '" . $fn . "' not found in class.metadata.php";
        return(NULL);
      }
   }
   
   
   /**
    * get text info data from a single ..text plugin
    * - we only read first 4KiB of file
    *
    */
   function readText($fn, $size=4096) {
   
      // read from given filename
      if (file_exists($fn) and ($f = fopen($fn, "r"))) {
         $src = fread($f, $size);
         fclose($f);
         return $src;
      }
      // file not found/readable
      else {
        return "The file '" . $fn . "' couldn't be found in your plugins folder.";                
      }
   }


   
   /**
    * Parses out meta informations from a .php script
    * - does not add [fn] filename itself
    * - does not parse [config] itself
    * the data ends up in $this->plugin[] sorted by $id
    *
    * individual entries typically contain:
    * [$id] => array(
    *    "id" => "$id",
    *    "title" => "plugin title/name",
    *    "api" => "php",
    *    "description" => "...",
    *    "type" => "class",
    *    "categoriy" => "system",
    *    "version" => "1.0",
    *    "license" => "PHPL",
    *    "author" => "name <mail@localhost>",
    *    "homepage" => "http://example.com/author-homepage.htm",
    *    "url" => "http://example.com/plugins-homepage.htm",
    *    "priority" => "optional",
    *    "fn" => "local/path/to/plugin/filename.php",    // later added by ->scan()
    *    "depends" => "x, y, plugins",
    *    "update" => "ftp://example.com/bin/downl.asis",
    *    "sort" => "0",
    *    "config" => " ... remains here as text <var name=app[set] ... /> ",
    *    "...",
    *    "help" => "longer text \n usually spanning \n multiple lines \n ...",
    * )
    *
    */
   function parse($src) {
      $info = array();
      
      #-- first comment block
      $src = $this->extract_first_comment($src);
       
      #-- find empty line and split cfg:block from help text part
      if (preg_match("/^(.+?)\n[ \t]*\n(.+)$/s", $src, $uu)) {
         $src = $uu[1];

         // add second part as help text
         $info["help"] = trim($uu[2]);
      }


      #-- read lines and name:value pairs
      preg_match_all("/^(\w+(?: \w+)?):\s*([^\n]*(\n[ ]+[^\n]+)*)/m", $src, $uu);
      /*                    |                    |
                       this (?: \w+) is just for compatibility with wordpress plugin comments
                                                 |
                       lines with leading spaces (\n[ ]+ hold continuing description values
      */
 
      #-- add each line after trimming outer whitespace
      foreach ($uu[1] as $i=>$tmp) {
         $info[strtolower($uu[1][$i])] = trim($uu[2][$i]);
      }

      #-- ok - folder used for plugins, name used for themes
      if (isset($info["folder"]) || isset($info["name"])) {
         return($info);
      }
   }



   /**
    * gets first block of asterisk /* comment or # hash or // slash
    * comment, removes leading whitespace and comment characters
    *
    */
   function extract_first_comment($src) {

      #-- clean out first line
      $src = preg_replace("/^<\?(php)?[^\n]*/i", "", $src);

      #-- extract /* ... */ comment block
      #  or lines of #... #... and //... //...
      if (preg_match("_^\s*/\*+(.+?)\*+/_s", $src, $uu)
      or (preg_match("_^\s*((^\s*(#+|//+)\s*.+?$\n)+)_ms", $src, $uu))) {
         $src = $uu[1];
      }
      else {
         return;
      }

      #-- cut comment/whitespace prefixes like _*__ or  __#_ or _//__ from
      #   lines - with same length from everyone! - don't care about actual
      #   pattern, but allow shortened lines (missing spaces after # or *)
      preg_match("_^([*#/ ]+)\w+( \w+)?:_m", $src, $uu);
      if ($uu) {
	      $n = strlen($uu[1]);
	      $src = preg_replace("_^[*#/ ]{0,$n}_m", "", $src);
	      return($src);
	  }
	  return false;
   }






   /**
    * Reads in .php plugin meta data from given directory and subdirectories.
    * (Three levels actually.)
    * The data gets stored into ->$plugin for later use, augmented by every
    * plugins [fn] relative to the supplied basedir.
    *
    */
   function scan($basedir) {
      
      #-- reading in
      $basedir = realpath($basedir);
      
      #-- each file
      foreach ($this->scan_subdirs($basedir) as $num=>$fn) {
      
         #-- basename == id
         $id = basename($fn, ".php");

         #-- parse
         if ($e = $this->read($fn)) {
         
            #-- has plugin custom set id: ?  (should not happen, but who knows if this might be useful?)
            if (!empty($e["id"])) {
               $id = $e["id"];
            }
            else {
               $e["id"] = $id;
            }
	    
	    #-- WordPress plugin scheme compatibility
	    if ($e["plugin name"]) {
	       foreach ($this->wp_compat as $from=>$to) if (!isset($e[$to])) {
                  $e[$to] = $e[$from];
                  unset($e[$from]);
               }
               $e["api"] = "wordpress";
	    }

            #-- add localized filename
            $fn = substr($fn, strlen($basedir)+1);
            $e["fn"] = $fn;
            
            #-- append to list 
            if (isset($this->plugin[$id])) {
               $this->error("a plugin with the name '$id' is already registered (second_fn=$fn, registered={$this->plugin[$id][fn]})\n");
            }
            else {
               $this->plugin[$id] = $e;
            }
         }
      }

      // plugin dependencies
      $this->extract_lists();

      // send it back even if probably unused
      return $this->plugin;
   }



   /**
    * separates dependency fields and config variables out of all
    * plugin entries
    *
    */
   function extract_lists() {

      #-- extract list fields
      $fields = array("depends", "suggests", "replaces", "conflicts");
      foreach ($this->plugin as $id=>$e) {
         
         #-- provides:
         if ($e["provides"]) foreach (explode(",",$e["provides"]) as $set) if ($set=trim($set)) {
            $this->provides[$set][] = $id;    // only the first gets used
         }
         
         #-- depends:
         foreach ($fields as $field) {
            if (isset($e[$field])) foreach (explode(",",$e["depends"]) as $set) if ($set=trim($set)) {
               $this->{$field}[$id][] = $set;
            }
         }
         
         #-- config:
         if ($e["config"]) {
            # currently doesn't get unfold into plugin $e entry,
            # but just into $this->config[] list
            #
            $cfg_txt = $e["config"];
            // $e["config"] = array();
            foreach ($this->parse_options($cfg_txt, $id) as $opt) {
               $this->config[$opt["name"]] = $opt;
               // $e["config"][$opt["name]] = & $this->config[$opt["name"]];
            }
         }

      }
   }


   
   /**
    * look for .php files in subdirectories
    *
    */
   function scan_subdirs($basedir) {
      $r = array();
      if ($dh = opendir($basedir)) {
         while ($fn = readdir($dh)) {
            if ($fn[0] == ".") {
            }
            elseif (is_dir("$basedir/$fn")) {
               foreach ($this->scan_subdirs("$basedir/$fn") as $fn) {
                  $r[] = $fn;
               }
            }
            elseif (strpos($fn, ".php")) {
               $r[] = "$basedir/$fn";
            }
         }
         closedir($dh);
      }
      return $r;
      #glob("$basedir/*.php") + glob("$basedir/*/*.php") + glob("$basedir/*/*/*.php") + glob("$basedir/*/*/*/*.php");
   }


   /**
    * extract config: options pseudo XML into array,
    * returns an array per config option / varname
    *
    * <var name="app[setting]" value="default_1" title=".." />
    *   ->
    * array(
    *   "is" => "var",
    *   "plugin" => "config_option_was_found_in_this_php_plugin_script",
    *   "name" => "app[setting]",
    *   "value" => "default_1",
    *   "title" => "name of first setting",
    *   "description" => "should better be present",
    *  # "multi" => array("val1" => "title1", "v2" => "t2", ...),
    *  # "type" => "text",
    *  # "..." => "depending on var type, could have other options",
    * )
    *
    */
   function parse_options($str, $plugin) {
      $r = array();
   
      #-- search for < angle brackets > first
      preg_match_all("_<(\w+)(.+?)/\s*>_ims", $str, $uu);
      foreach ($uu[1] as $i=>$optiontype) {
         $inner = $uu[2][$i];

         #-- prepare new
         $entry = array(
            "is" => $optiontype,
            "plugin" => $plugin,
         );
      
         #-- extract individual fields
         preg_match_all("_\s+([-\w:]+)=[\"\']([^\"\']*?)[\"\']_msi", $inner, $vv);
         foreach ($vv[1] as $j=>$field) {
            $entry[$field] = $vv[2][$j];
         }
         
         #-- clean name=
         $entry["name"] = preg_replace("/[\$\"\'\s]/", "", $entry["name"]);
         
         #-- split up multi= value (our value= field holds the default entry instead)
         if (strpos($entry["multi"], "|")) {
            $opt = array();
            foreach (explode("|", $entry["multi"]) as $o) {
               if (strpos($o, "=")) {
                  $opt[strtok($o,"=")] = strtok("\n");
               }
               else {
                  $opt[$o] = $o;
               }
            }
            $entry["multi"] = $opt;
         }
         
         #-- rename, just in case (actually default= is not recommended, and value= should be used)
         if (isset($row["default"]) && empty($row["value"])) {
            $row["value"] = $row["default"];
         }
         
         #-- add to list
         $r[] = $entry;
      }
      return($r);
   }



   /**
    * returns plugins grouped by entries' $field: value
    * - or comparison value
    *
    */
   function by($field, $cmp=NULL) {
      $r = array();
      foreach ($this->plugin as $id=>$row) {
         if ( (empty($cmp) && isset($row[$field]))
         or ($cmp==strtolower($row[$field])) )
         {
            $r[$row[$field]][$id] = $row;
         }
      }
      return($r);
   }



   /**
    * complain
    *
    */
   function error($s) {
      trigger_error($s, E_USER_WARNING);
      $this->error = 1;
   }

}


?>