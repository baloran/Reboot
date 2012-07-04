function Map(nom, tile) {
	// Création de l'objet XmlHttpRequest
	var xhr = getXMLHttpRequest();
		
	// Chargement du fichier
	xhr.open("GET", './maps/' + nom + '.json', false);
	xhr.send(null);
	if(xhr.readyState != 4 || (xhr.status != 200 && xhr.status != 0)) // Code == 0 en local
		throw new Error("Impossible de charger la carte nommée \"" + nom + "\" (code HTTP : " + xhr.status + ").");
	var mapJsonData = xhr.responseText;
	
	// Analyse des données
	var mapData = JSON.parse(mapJsonData);
	this.tileset = new Tileset(mapData.tileset, tile);
	this.terrain = mapData.terrain;
}

// Pour récupérer la taille (en tiles) de la carte
Map.prototype.getHauteur = function() {
	return this.terrain.length;
}
Map.prototype.getLargeur = function() {
	return this.terrain[0].length;
}

Map.prototype.dessinerMap = function(context, tile, scroll) {
	for(var i = 0, l = this.terrain.length ; i < l ; i++) {
		var ligne = this.terrain[i];
		var y = i;
		for(var j = 0, k = ligne.length ; j < k ; j++) {
		var posx;
		var posy;
		posx = scroll.x + ( y + j ) * (tile.ldest / 2);
		posy = scroll.y + ( y - j ) * (tile.hdest / 2);
			
			this.tileset.dessinerTile(ligne[j], context, posx, posy, tile);
			
		}
	}
}















