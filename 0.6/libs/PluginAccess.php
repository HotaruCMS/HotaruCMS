<?php

class PluginAccess extends Plugin
{

    public function __construct($folder = '') 
    {
        $this->setFolder($folder);
        $this->setName(make_name($folder));
    }
    
    public function __toString()
    {
        return $this->class;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function getFolder()
    {
        return $this->folder;
    }
        
    public function nameLength()
    {
        return strlen($this->name);
    }

    /**
     * setIncludeCSS
     *
     * @param string $file - full path to the CSS file
     */
    public function setCssIncludes($file)
    {
        array_push($this->cssIncludes, $file);
    }
    

    /**
     * getIncludeCSS
     */
    public function getCssIncludes()
    {
        return $this->cssIncludes;
    }
    

    /**
     * setIncludeJS
     *
     * @param string $file - full path to the JS file
     */
    public function setJsIncludes($file)
    {
        array_push($this->jsIncludes, $file);
    }
    

    /**
     * getIncludeJS
     */
    public function getJsIncludes()
    {
        return $this->jsIncludes;
    }
    
    
    /**
     * getIncludeType
     */
    public function getIncludeType()
    {
        return $this->includeType;
    }
}

?>