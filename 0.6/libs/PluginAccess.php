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
    
}

?>