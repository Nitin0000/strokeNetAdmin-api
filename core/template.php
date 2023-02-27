<?php
class bQuickTpl {
    private $_data = array();   
    private $_dataView = array();
    public function render( $fileName, $data = false ) 
    {
        if($data) {
            $this->_dataView = $data;
        }
        if(defined("BQUICK_TPL_PATH")) {
            $fileName = BQUICK_TPL_PATH.$fileName;
        }
        $rendered = "";
        if(file_exists($fileName)) {
            ob_start();
            require($fileName);   
            $rendered = ob_get_contents(); 
            ob_end_clean();         
        }
        $this->_dataView = array();
        return $rendered;             
    }
    
    /** 
     * Renders a view for each element of array
     */      
    
    public function renderAr ($fileName, $dataAr) { 
        $rendered = "";
        if(count($dataAr && is_array($dataAr))) {
            foreach($dataAr AS $data) {
                $rendered.= $this->render($fileName, $data);
            }
        }
        return $rendered;
    
    }
    
    /**
     * magic method set
     */          
    public function __set($key, $value) 
    {
        $this->_data[$key] = $value;
    }
     
    /**
     * magic method get
     */    
    public function __get($key) 
    {
        if(isset($this->_dataView[$key])) {  
            return $this->_dataView[$key];
        }
        else if(isset($this->_data[$key])) {
            return $this->_data[$key];
        }
        else {
            return false;
        }
    }
}