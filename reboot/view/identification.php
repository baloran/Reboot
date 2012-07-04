<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/model/php/Root.php';
    include_once 'include/php/header.php';

    if(_VAR::$USER->isConnected) header('Location:'.build_url('play'));
?>
    <form id="Connexion" class="" method="post" ACTION="identification" ENCTYPE="application/x-www-form-urlencoded"> 
		<input type="text" name="pseudo" style="display:none;" />    
     
    	<div><input placeholder="Identifiant" type="text" name="val1" pattern="<?=User::$Pattern['pseudo']?>" required/></div>
    	<div><input placeholder="Mot de passe" type="password" name="val2" required/></div>
    	<div class="Row" id="FormPlus">
            <input class="Case" value="" type="submit"/>
            <div class="Case" style="text-align:right;width:130px;">
                <div style="margin-top:6px;"><input id="cookie" name="cookie" type="checkbox"/><label for="cookie">Rest&eacute; connect&eacute;</label></div>
                <div style="margin-top:6px;"><a href="forget">Mot de passe oubli&eacute; ?</a></div>
            </div>
        </div>
        
	</form>
<?
    include_once 'include/php/footer.php';
?>