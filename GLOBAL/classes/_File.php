<?php
/**
 * Classe de gestion de Fichier
 *
 * @author UrielMyeline
 */
class File extends Object{
    
    public $parent;
    public $name;
    public $url;
    public $extension;
    public $mode;
    public $size = 0;
    public $exist;
    
    public $handle;
    
    public static $tabMode = array(
        /*mode     lecture, écriture, création */
        'r'     =>  array(true, false, false),
        'r+'    =>  array(true, true, false),
        'w'     =>  array(false, true, true),
        'w+'    =>  array(true, true, true),
        'a'     =>  array(false, true, false),
        'a+'    =>  array(true, true, true)
    );
    
    public function __construct($parent, $name, $mode = 'r', $testExist = false){
        parent::__construct('File');
        /*
            r 	=>	lecture                         début
            r+ 	=>	lecture/écriture 		début
            w	=>	écriture/création		début + taille = 0
            w+	=>	lecture/écriture/création	début + taille = 0
            a	=>	écriture			fin
            a+	=>	lecture/écriture/création	fin
        */
        
        if(get_class($this) != 'Folder'){
            $this->parent = $parent;
            $this->name = $name;
            $this->mode = $mode;

            $testExist = File::$tabMode[$mode][2] ? $testExist : true;
            $isOk = $testExist ? $this->exist = file_exists($this->getPath()) : true;

            if($isOk) $this->update();
        }
    }
    
    public function getPath(){
        return File::getHome().$this->parent.$this->name;
    }
    
    public static function getHome(){
        return get_include_path();
    }
    
    public function init($handle){
        if($handle){
            $this->exist = true;
            $this->handle = $handle;
            $this->size = filesize($this->getPath());
        }
        else{
            unset($this->size);
            unset($this->handle);
            $this->exist = false;
        }
        
        return $this->exist;
    }
    
    public static function getFile($url, $mode = 'r'){
        if(file_exists($url)){
            $url = str_replace(File::getHome(), '', $url);

            if($url != '/' && basename($url) != ''){
                $pos = strpos($url, basename($url));
                $name = substr($url, $pos);
                $parent = substr($url, 0, $pos);
                
                //echo $url.' : '.$name.' : '.$parent.' : '.basename($url).' File:'.is_file($url).' Folder:'.is_dir($url).'<br/>';

                if(is_dir($url))
                    return new Folder($parent, $name, $mode);
                elseif(is_file($url))
                    return new File($parent, $name, $mode);
            }
            else return new File('/', '', $mode);
        }
        else return false;
    }
    
    public function getParent(){
        if(!file_exists($this->getPath())) return $this->init(false);
        
        if($this->getPath() != File::getHome().$this->parent){
            $file = File::getFile(dirname($this->getPath()).'/');
            if($file){
                return new Folder($file->parent, $file->name);
            }
            else return false;
        }
        else return new Folder('/', '');
    }
    
    public function getSize($formated = false){
        return !$formated ? $this->size : 0;
    }
    
    public function rename($newname){
        if(!file_exists($this->getPath())) return $this->init(false);
        
        if(rename($this->getPath(), File::getHome().$this->parent.$newname)){
            $this->name = $newname;
            return true;
        }
        else return false;
    }
    
    public function copy($folder){
        if(!file_exists($this->getPath())) return $this->init(false);
        if(!file_exists($folder->getPath())) return $folder->init(false);
        
        return copy($this->getPath(), $folder->getPath().$this->name);
    }
    
    public function move($folder){
        if(!file_exists($this->getPath())) return $this->init(false);
        
        if($this->copy($folder)){
            $this->delete();
        }
    }
    
    public function __toString(){
        return print_r(array(
            'name'  =>  $this->name,
            'parent'=>  $this->parent,
            'url'   =>  $this->getPath(),
            'mode'  =>  $this->mode,
            'exist' =>  $this->exist
        ), true);
    }
    
    public function update(){
        return $this->init(@fopen($this->getPath(), $this->mode));
    }
    
    public function delete(){
        if(!file_exists($this->getPath())) return $this->init(false);
        
        return unlink($this->getPath());
    }
    
    public function setMode($mode){
        return new File($this->parent, $this->name, $mode);
    }
    
    public function __destruct(){
        if(isset($this->handle) && $this->handle) fclose($this->handle);
    }
}

