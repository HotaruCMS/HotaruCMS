<?php

// Include the generic_pmd class that reads post metadata from the a plugin
require_once(EXTENSIONS . 'GenericPHPConfig/class.metadata.php');

class Plugin extends generic_pmd
{

    protected $id           = '';         // plugin id
    protected $enabled      = 0;          // activate (1), inactive (0)
    protected $name         = '';         // plugin proper name
    protected $prefix       = '';         // plugin prefix
    protected $folder       = '';         // plugin folder name
    protected $class        = '';         // plugin class name
    protected $desc         = '';         // plugin description
    protected $version      = 0;          // plugin version number
    protected $order        = 0;          // plugin order number
    protected $requires     = '';         // string of plugin->version pairs
    protected $dependencies = array();    // array of plugin->version pairs
    protected $hooks        = array();    // array of plugin hooks


    /**
     * Include language file if available
     */
    public function hotaru_header()
    {
        global $plugins;

        $plugins->includeLanguage($this->folder);
    }
    

    /**
     * Display Admin sidebar link
     */
    public function admin_sidebar_plugin_settings()
    {
        echo "<li><a href='" . url(array('page'=>'plugin_settings', 'plugin'=>$this->folder), 'admin') . "'>" . $this->name . "</a></li>";
    }
    
    
    /**
     * Display Admin settings page
     *
     * @return true
     */
    public function admin_plugin_settings()
    {
        if (file_exists(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php')) {
            include_once(PLUGINS . $this->folder . '/' . $this->folder . '_settings.php');
        }
        
        $settings_function = $this->folder . '_settings';
        $settings_function();   // call the settings function
        return true;
    }
    

    /**
     * Include All CSS and JavaScript files for this plugin
     */
    public function header_include()
    {
        global $plugins;
        
        if (is_dir(PLUGINS . $this->folder . '/css/')) {
            $css_files = getFilenames(PLUGINS . $this->folder . '/css/', 'short');
            $css_files = stripAllFileExtensions($css_files);
            foreach($css_files as $file) {
                $plugins->includeCSS(stripFileExtension($file));
            }
        }
        
        if (is_dir(PLUGINS . $this->folder . '/javascript/')) {
            $css_files = getFilenames(PLUGINS . $this->folder . '/javascript/', 'short');
            $css_files = stripAllFileExtensions($js_files);
            foreach($js_files as $file) {
                $plugins->includeJS(stripFileExtension($file));
            }
        }
    }
    
    
    /**
     * Include code as a template before the closing </body> tag
     */
    public function pre_close_body()
    {
        global $hotaru;
        
        $hotaru->displayTemplate($this->folder . '_footer', $this->folder);
    }
}

?>