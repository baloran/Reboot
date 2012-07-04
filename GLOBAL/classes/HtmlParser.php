<?

class HtmlParser
{
    private $encoding;
    private $matches;
    private $escaped;
    private $opened = array();
    
    public $malformed;
    public $tree = array();

    public function parse($html, $namespace=NULL, $encoding='utf-8')
    {
        $this->malformed = false;
        $this->encoding = $encoding;

        $html = preg_replace('#\<br((\s+)?)((/+)?)\>#i', '<br />', $html);
        
        #
        # we take care of escaping comments and processing options. they will not be parsed
        # and will end as text nodes
        #
        
        $html = $this->escapeSpecials($html);
        
        #
        # in order to create a tree, we first need to split the HTML using the markups,
        # creating a nice flat array of texts and opening and closing markups.
        #
        # the array can be read as follows :
        #
        # i+0 => some text
        # i+1 => '/' for closing markups, nothing otherwise
        # i+2 => the markup it self, without the '<' '>'
        #
        # note that i+2 might end with a '/' indicating an auto-closing markup
        #
    
        $this->matches = preg_split
        (
            '#<(/?)' . $namespace . '([^>]*)>#', $html, -1, PREG_SPLIT_DELIM_CAPTURE
        );
        
        #
        # the flat representation is now ready, we can create our tree
        #
        
        $tree = $this->buildTree();
        
        #
        # if comments or processing options where escaped, we can
        # safely unescape them now
        #
        
        if ($this->escaped)
        {
            $tree = $this->unescapeSpecials($tree);
        }

        $this->tree = $tree;
        
        return $tree;
    }
    
    private function escapeSpecials($html)
    {
        #
        # here we escape comments
        #
        
        $html = preg_replace_callback('#<\!--.+-->#sU', array($this, 'escapeSpecials_callback'), $html);
        
        #
        # and processing options
        #
        
        $html = preg_replace_callback('#<\?.+\?>#sU', array($this, 'escapeSpecials_callback'), $html);
        
        return $html;
    }
    
    private function escapeSpecials_callback($m)
    {
        $this->escaped = true;
        
        $text = $m[0];
        
        $text = str_replace
        (
            array('<', '>'),
            array("\x01", "\x02"),
            $text
        );
        
        return $text;
    }

    private function unescapeSpecials($tree)
    {
        return is_array($tree) ? array_map(array($this, 'unescapeSpecials'), $tree) : str_replace
        (
            array("\x01", "\x02"),
            array('<', '>'),
            $tree
        );
    }

    private function buildTree()
    {
        $nodes = array();
            
        $i = 0;
        $text = NULL;
        
        while (($value = array_shift($this->matches)) !== NULL)
        {
            switch ($i++ % 3)
            {
                case 0:
                {
                    #
                    # if the trimed value is not empty we preserve the value,
                    # otherwise we discard it.
                    #
                    
                    if (trim($value))
                    {
                        $nodes[] = $value;
                    }
                }
                break;
                
                case 1:
                {
                    $closing = ($value == '/');
                }
                break;
                
                case 2:
                {
                    if (substr($value, -1, 1) == '/')
                    {
                        #
                        # auto closing
                        #
                        $nodes = $this->parseMarkup(substr($value, 0, -1));
                    }
                    else if ($closing)
                    {
                        #
                        # closing markup
                        #

                        $open = array_pop($this->opened);
                    
                        if ($value != $open)
                        {
                            $this->error($value, $open);
                        }

                        return $nodes;
                    }
                    else
                    {
                        #
                        # this is an open markup with possible children
                        #
                        
                        $node = $this->parseMarkup($value);
                        
                        #
                        # push the markup name into the opened markups
                        #

                        $this->opened[] = $node['name'];
                        
                        #
                        # create the node and parse its children
                        #
                                                                        
                        $node['child'] = $this->buildTree($this->matches);
                        
                        $nodes[] = $node;
                    }
                }
            }
        }
        
        return $nodes;
    }
    
    public function parseMarkup($markup)
    {
        #
        # get markup's name
        #
        
        preg_match('#^[^\s]+#', $markup, $matches);
        
        $name = $matches[0];
        
        #
        # get markup's arguments
        #
        
        preg_match_all("#\s+([^=]+)\s*=\s*'([^']+)'#", $markup, $matches_quote, PREG_SET_ORDER);
        preg_match_all('#\s+([^=]+)\s*=\s*"([^"]+)"#', $markup, $matches_doublequote, PREG_SET_ORDER);
        $matches = array_merge($matches_quote, $matches_doublequote);
        #
        # transform the matches into a nice key/value array
        #

        $args = array();
        
        foreach ($matches as $m)
        {
            #
            # we unescape the html entities of the argument's value
            #
            $key = strtolower($m[1]);
            $value = html_entity_decode($m[2], ENT_QUOTES, $this->encoding);

            if($key === 'class'){
                $value = explode(' ', $value);
                foreach($value as &$val) $val = trim($val);
            } 
            

            $args[$key] = $value;
        }

        return array('name' => $name, 'args' => $args);
    }
    
    public function error($markup, $expected)
    {
        $this->malformed = true;
        
        printf('Unexpected closing markup "%s", should be "%s"', $markup, $expected);
    }

    public function __call($method, $arguments){

    }

    public function __get($key){
        
    }

    public function search($search, $limit = false){
        $result = array();

        $result = $this->search_recursive($this->tree, $result, $search, $limit);

        if(count($result) > 0){
            $object = clone $this;
            $object->tree = $result;
            $result = $object;
        }
        else $result = false;

        return $result;
    }

    public function search_recursive($node, &$result, $search, $limit = false){
        $child = isset($node['child']) ? $node['child'] : $node;

        foreach($child as $children){
            $found = false;
            $continue = true;

            foreach($search as $key => $value){
                if(is_array($children)){
                    $found =    
                        $key == 'KEY' && isset($children['name']) && $children['name'] == $value ||
                        $key === 'VALUE' && isset($children['child']) && $children['child'][0] == $value || 
                        isset($children['args'][$key]) && $children['args'][$key] == $value;
                }
                /*elseif($key === 'VALUE' && $children == $value){
                    $found = true;
                }*/

                if(!$found) break;
            }

            

            if($found){
                if(!$limit || count($result) < $limit){
                    $result[] = $children;
                }
                else $continue = false;
            }

            if($continue){
                if(isset($children['child']) && is_array($children['child'])){
                    $this->search_recursive($children, $result, $search, $limit);
                }
            }
            else break;
        }

        return $result;
    }
}

?>