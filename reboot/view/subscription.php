<?php
    include_once '../model/php/Root.php';
    Root::$inGame = false;
    include_once 'include/php/header.php';
?>
	<form class="" method="post" ACTION="subscription" ENCTYPE="application/x-www-form-urlencoded" id="colonne">
		<div>Si nous vous donnions les moyens de tout reconstruire</div>
		<input type="submit" value="" class="Case hand left" src="img/pillule_gauche_rouge.png"/><img class="Case hand right" src="img/pillule_droite_bleu.png" onclick=""/>
		<div id="card">
			<div id="Subscription" class="Row">
				<div class="Case" id="Avatar"></div>
				<iv class="Case">
					<input type="text" name="pseudo" style="display:none;" /> 
			    	<div><input placeholder="Identifiant" type="text" name="val1" pattern="<?php echo User::$Pattern['pseudo'];?>" required/></div>
			    	<div><input placeholder="E-mail" type="text" name="val2" pattern="<?php echo User::$Pattern['email'];?>" required/></div>
			    </div>
			</form>
		</div>
	</form>
<?php
    include_once 'include/php/footer.php';
?>  