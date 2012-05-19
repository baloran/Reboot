<?php
    include_once $_SERVER['DOCUMENT_ROOT'].'/model/php/Root.php';
    include_once 'include/php/header.php';

    if(_VAR::$USER->isConnected) header('Location:'.build_url('play'));
?>
	<h1 class="Title">Identification</div>
    <form id="Connexion" class="" method="post" ACTION="identification" ENCTYPE="application/x-www-form-urlencoded"> 
		<input type="text" name="pseudo" style="display:none;" />    
        <div class="Row">
        	<input placeholder="Identifiant" type="text" name="val1" pattern="<?=User::$Pattern['pseudo']?>" required/>
        	<input placeholder="Mot de passe" type="password" name="val2" required/>
        	<input value="=>" type="submit" class=""/>
        </div>
        <div class="Row" id="Plus"><div class="Case" style="text-align:left;"><input id="cookie" name="cookie" type="checkbox"/><label for="cookie">Rest&eacute; connect&eacute;</label></div><div class="Case" style="text-align:right;"><a href="forget">Mot de passe oubli&eacute; ?</a></div></div>
	</form>
<?
    include_once 'include/php/footer.php';
?>  