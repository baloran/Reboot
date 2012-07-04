<?php
/**
 * Description of Folder
 *
 * @author JA
 */
class Folder extends File{
    
    public function __construct($parent, $name, $mode = 0755){
        parent::__construct($parent,$name);
        /*
            r 	=>	lecture                     début
            r+ 	=>	lecture/écriture 		    début
            w	=>	écriture/création		    début + taille = 0
            w+	=>	lecture/écriture/création	début + taille = 0
            a	=>	écriture			        fin
            a+	=>	lecture/écriture/création	fin
        */
        
        $this->parent = $parent;
        $this->name = $name;
        $this->mode = $mode;
        
        if(!$this->exist = file_exists($this->getPath())){
            $this->exist = @mkdir($this->getPath(), 0755);
        }
    }
    
    public function update(){}
    
    public function delete(){
        if(!file_exists($this->getPath())) return $this->init(false);
        
        return rmdir($this->getPath());
    }
    
    public function setMode($mode){
        return new Folder($this->parent, $this->name, $mode);
    }
    
    public function scan($mode = 'r', $recursif = false, $searchFolder = false, $searchFile = false){       
        if(!file_exists($this->getPath())) return $this->init(false);
        if(!is_dir($this->getPath())) return array();
        
        $files = scandir($this->getPath());
        $Files = array();
        foreach($files as $file){
            $path = $this->getPath().'/'.$file;
            $isDir = is_dir($path);
            
            if($file != '.' && $file != '..' && (!$searchFolder || !$isDir || preg_match($searchFolder, $file)) && 
                                                (!$searchFile || $isDir || preg_match($searchFile, $file))){
                
                $File = File::getFile($path, $mode);
                if($File){
                    if(get_class($File) == 'Folder' && $recursif) $File->children = $File->scan($mode, $recursif, $searchFolder, $searchFile);
                    
                    if(!$isDir) echo get_class($File).' : '.$File->getPath().PHP_EOL;
                    
                    $Files[$File->name] = $File;
                }
                
            }
        }
        return $Files;
    }
    
    public function __destruct(){}
}

?>
