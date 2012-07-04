<?php
/*http://julp.developpez.com/php/curl/*/
class HttpQuery
{
    /**
     * Tableau : les donn�es POST qui seront envoy�es (nom du champ => valeur)
     **/
    protected $_post;

    /**
     * Tableau des options cURL definies par l'utilisateur (option => valeur)
     **/
    protected $_options;

    /**
     * La ressource cURL
     **/
    protected $_ch;

    /**
     * Constructeur
     * @param url URL � laquelle la requ�te sera envoy�e
     * @throws Exception si l'extension cURL n'est pas active
     **/
    public function __construct($url)
    {
        if (!extension_loaded('curl')) {
            throw new Exception("L'extension curl n'est pas disponible");
        }
        $this->_ch = curl_init($url);
        $this->_options = array();
    }

    /**
     * Obtenir la valeur des options cURL avec la syntaxe $ojet->CURLOPT_X d�finie par l'utilisateur
     * @param nom le nom de l'option cURL
     * @return NULL si l'option n'a pas �t� d�finie sinon sa valeur
     **/
    public function __get($nom)
    {
        $resultat = NULL;
        if (defined($nom)) {
            $valeur = constant($nom);
            if (isset($this->_options[$valeur])) {
                $resultat = $this->_options[$valeur];
            }
        }
        return $resultat;
    }

    /**
     * Fixer les valeurs des options cURL avec la syntaxe $objet->CURLOPT_X = Y
     * @param nom    le nom de l'option cURL (constantes CURLOPT_*)
     * @param valeur la nouvelle valeur de l'option (�crase la pr�c�dente)
     * @throws Exception si l'option "nom" n'est pas valide (inexistante ou ne commen�ant pas par CURLOPT_) ou est
     *                   prot�g�e de fa�on � ce que vous passiez par les m�thodes d�l�gu�es � la fonctionnalit� cibl�e
     **/
    public function __set($nom, $valeur)
    {
        if (defined($nom) && preg_match('/^CURLOPT_(?!POSTFIELDS)/', $nom)) {
            $this->_options[constant($nom)] = $valeur;
        } else {
            throw new Exception("Option '$nom' invalide ou prot�g�e");
        }
    }

    /**
     * Prendre connaissance de la d�finition d'une option cURL par l'utilisateur
     * @param nom le nom de l'option cURL
     * @return un bool�en indiquant si cette option a �t� d�finie
     **/
    public function __isset($nom)
    {
        return (defined($nom) && isset($this->_options[constant($nom)]));
    }

    /**
     * D�truire la d�finition d'une option cURL
     * @param nom le nom de l'option cURL � d�truire
     **/
    public function __unset($nom)
    {
        if (defined($nom) && isset($this->_options[constant($nom)])) {
            unset($this->_options[constant($nom)]);
        }
    }

    /**
     * Description de l'objet
     * @return une cha�ne de caract�res d�crivant l'objet
     **/
    public function __toString()
    {
        return sprintf("%s (%s)", __CLASS__, curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL));
    }

    /**
     * Fixer la dur�e maximale d'ex�cution de la requ�te
     * @param timeout cette dur�e exprim�e en secondes
     **/
    public function setTimeout($timeout)
    {
        $timeout = intval($timeout);
        if ($timeout > 0) {
            $this->CURLOPT_TIMEOUT = $timeout;
            $this->CURLOPT_CONNECTTIMEOUT = $timeout;
        }
    }

    /**
     * Ajouter des donn�es textuelles aux donn�es POST � envoyer
     * @param nom_champ le nom du champ (permet d'exploiter les donn�es c�t� serveur - $_POST)
     * @param valeur    les donn�es correspondantes � envoyer
     * @return un bool�en indiquant que les donn�es ont �t� prises en compte
     **/
    public function addPostData($nom_champ, $valeur)
    {
        if (!isset($this->_post[$nom_champ]) && !is_array($valeur)) {
            $this->_post[$nom_champ] = $valeur;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Ajouter un fichier aux donn�es POST � envoyer (upload de fichiers)
     * @param nom_champ le nom du champ (permet d'exploiter le fichier � sa r�ception - $_FILES)
     * @param fichier   le fichier � envoyer
     * @throws Exception si le fichier indiqu� est inexistant ou n'est pas un fichier r�gulier
     **/
    public function addPostFile($nom_champ, $fichier)
    {
        if (is_file($fichier)) {
            $this->_post[$nom_champ] = '@' . realpath($fichier);
        } else {
            throw new Exception("Le fichier '$fichier' n'existe pas ou n'est pas un fichier r�gulier");
        }
    }

    /**
     * Ex�cuter la requ�te
     * @param fichier_sortie, renseign� le contenu de la page distante est �crit dans le fichier indiqu�
     * @return le contenu de la page distante ou alors TRUE si le param�tre fichier_sortie a �t� utilis�
     * @throws Exception en cas d'erreur li�e � cURL ou � l'�criture du fichier
     **/
    public function doRequest($fichier_sortie = FALSE)
    {
        if ($this->_options) {
            if (function_exists('curl_setopt_array')) {
                curl_setopt_array($this->_ch, $this->_options);
            } else {
                foreach ($this->_options as $option => $valeur) {
                    curl_setopt($this->_ch, $option, $valeur);
                }
            }
        }
        if ($fichier_sortie) {
            @ $fp = fopen($fichier_sortie, 'w');
            if (!$fp) {
                throw new Exception("Impossible d'ouvrir en �criture le fichier '$fichier_sortie'");
            }
            curl_setopt($this->_ch, CURLOPT_FILE, $fp);
        } else {
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
        }
        if ($this->_post) {
            curl_setopt($this->_ch, CURLOPT_POST, TRUE);
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_post);
        }
        $ret = curl_exec($this->_ch);
        if ($fichier_sortie) {
            fclose($fp);
        }
        if ($ret === FALSE) {
            throw new Exception("Une erreur est survenue : '" . curl_error($this->_ch) . "'");
        }
        return $ret;
    }

    /**
     * Destructeur
     **/
    public function __destruct()
    {
        unset($this->_options);
        unset($this->_post);
        curl_close($this->_ch);
    }
}?>