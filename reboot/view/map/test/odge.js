/* Copyright (c) 2010 Florent Fortat
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OupdatePlace(activeElt.x,activeElt.y);THER DEALINGS IN
 * THE SOFTWARE.
 */

var	object = new Object(),
//Misc Functions
	is_function = function(obj){return object.toString.call(obj) === "[object Function]";},
	is_array = function(obj){return object.toString.call(obj) === "[object Array]";},
	is_string = function(obj){return object.toString.call(obj) === "[object String]";},
	is_sprite = function(obj){return object.toString.call(obj) === "[object Object]" && obj.toString() === "[object Sprite]";},
	is_image = function(obj){return object.toString.call(obj) === "[object HTMLImageElement]"},


//----------------------//
//						//
//	   Class: Sprite 	//
//						//
//----------------------//
	Sprite = function(lbl){
		this.label = lbl;
		this.images = [];
		this.images_b = [];
		this.current_id = -1;
	
		this.add = function(str_img,collision,height,offset,bind){
			if(!is_string(str_img)) return;
			var	img = new Image(),tmp;
			img.src = str_img;
			tmp = {data:img,height:height,offset:offset||{x:0,y:0},collides:collision}
			this.images.push(tmp);
			this.current_id = this.images.length - 1;
			this.images_b[bind|| this.current_id] = tmp;
		};

		this.get = function(bind){
			return this.images_b[bind];
		};

		this.current = function(set){
			if(set)	this.current_id = set;
			return is_string(this.current_id)?this.images_b[this.current_id].data:this.current_id>-1?this.images[this.current_id].data:null;
		};
		
		this.offset = function(set){
			if(set)	this.current_id = set;
			return is_string(this.current_id)?this.images_b[this.current_id].offset:this.current_id>-1?this.images[this.current_id].offset:{x:0,y:0};
		};
		
		this.collision = function(set){
			if(set)	this.current_id = set;
			return is_string(this.current_id)?this.images_b[this.current_id].collides:this.current_id>-1?this.images[this.current_id].collides:true;
		};
		
		this.height = function(set){
			if(set)	this.current_id = set;
			return is_string(this.current_id)?this.images_b[this.current_id].height:this.current_id>-1?this.images[this.current_id].height:0;
		}

		this.toString=function(){return "[object Sprite]";};
	},


//----------------------//
//						//
//	 Class: ImgLoader	//
//						//
//----------------------//
	ImgLoader = function(){
		var	images = [],
			timer = 0,
			callback = null;
		
		this.add = function(img){
			if(is_array(img))
				for(var i=0;i<img.length;i++)
					this.add(img[i]);
			else if(is_sprite(img))
				this.add(img.images);
			else if(is_image(img) && !images.contains(img))
				images.push(img);
		};
		
		this.complete = function(cb){
			if(is_function(cb)){
				callback = function(){cb();};
				timer = setInterval(this.complete,100);
			}
			var complet = true;
			for(var i=0;i<images.length;i++)
				complet = complet && images[i].complete;
			if(complet){
				clearInterval(timer);
				timer = 0;
				callback();
			}
		};
		
		this.getImg=function(){return images;}
	},


//----------------------//
//						//
// Class: EltInterface	//
//						//
//----------------------//
	EltInterface = function(x,y,bg,opened,taggable,ctx,process){
		this.elements = [];
		this.text = [];
		this.position = {x:x||0,y:y||0};
		this.offset = {x:0,y:0};
		this.bg = bg || new Image();
		this.taggable = taggable;
		this.tags = [];
		this.ctx = ctx;
		this.process = process;
		
		var opened = opened || false;
		
		this.open	= function(){opened = true;};
		this.close	= function(){opened = false;};
		
		this.addImage	= function(image,x,y,tag){this.offset={x:x<0?(-x):0,y:y<0?(-y):0};this.elements.push({image:(function(){var tmp=new Image();tmp.src=image;return tmp;})(),x:x,y:y,tag:tag||true});;this.tags[tag] = function(){};};
		this.addText	= function(str,x,y,tag){var size = {w:100,h:10};this.text.push({text:str,x:x,y:y,width:size.w,height:size.h,tag:tag||true});this.tags[tag] = function(){};};
		this.popText	= function(){this.text.pop();};
		this.clearText	= function(){this.text = [];};
		
		this.width = function(){
			var width = this.bg.width;
			for(var i=0;i<this.elements.length;i++)
				width += (this.elements[i].x+this.elements[i].image.width > this.bg.width ? this.elements[i].x+this.elements[i].image.width-this.bg.width : 0);
			return width;
		};
		
		this.height = function(){
			var height = this.bg.height;
			for(var i=0;i<this.elements.length;i++)
				height += (this.elements[i].y+this.elements[i].image.height > this.bg.height ? this.elements[i].y+this.elements[i].image.height-this.bg.height : 0);
			return height;
		};
		
		this.move = function(x,y){
			this.position.x = x;
			this.position.y = y;
		};
		
		this.draw = function(c,pos){
			if(!opened)
				return
			
			var	context = c || this.ctx,
				x = pos ? pos.x : this.position.x,
				y = pos ? pos.y : this.position.y;
			
			if(this.bg.color){
				context.fillStyle = this.bg.color;
				context.fillRect(x,y,this.bg.width,this.bg.height);
			} else
				context.drawImage(this.bg,x,y);
			for(var i=0;i<this.elements.length;i++)
				context.drawImage(this.elements[i].image,x+this.elements[i].x,y+this.elements[i].y);
			for(var i=0;i<this.text.length;i++){
				context.fillStyle = "White"; 
				context.fillText(this.text[i].text,x+this.text[i].x,y+this.text[i].y);
			}
		};
		
		this.addEventListener = function(action, tag, fct){
			switch(action){
				case 'click':
					this.tags[tag] = fct;
					break;
				default:
					break;
			}
		};
		
		this.click = function(e){
			this.process.clearRect(0,0,800,600);
			
			this.draw(this.process,this.offset);
			
			x = e.offsetX-this.position.x;
			y = e.offsetY-this.position.y;
			
			pixel = [0,0,0,0];
			if(x >= 0 && x < this.width() && y >= 0 && y < this.height()){
				pixel = this.process.getImageData(x,y,1,1).data;
			}
			
			if(taggable){
				for(var i=0;i<this.elements.length;i++){
					if(this.elements[i].tag != true && (e.offsetX-(this.elements[i].x+this.position.x)) >= 0 && (e.offsetX-(this.elements[i].x+this.position.x)) <= this.elements[i].image.width && (e.offsetY-(this.elements[i].y+this.position.y)) >= 0 && (e.offsetY-(this.elements[i].y+this.position.y)) <= this.elements[i].image.height){
						this.tags[this.elements[i].tag]();
						break;
					}
				}
			
				for(var i=0;i<this.text.length;i++){
					if(this.text[i].tag != true && (e.offsetX-(this.text[i].x+this.position.x)) >= 0 && (e.offsetX-(this.text[i].x+this.position.x)) <= this.text[i].width && ((this.text[i].y+this.position.y)-e.offsetY) >= 0 && ((this.text[i].y+this.position.y)-e.offsetY) <= this.text[i].height){
						this.tags[this.text[i].tag]();
						break;
					}
				}
			}
			return pixel[3] != 0;
		}
	},


//----------------------//
//						//
//	 Class: Interface	//
//						//
//----------------------//
	Interface = function(ctx,process){
		this.elements = {};
		this.ctx = ctx;
		this.process = process;
		this.eventInterface = [];
		
		this.addElement = function(s,attr){
			if(attr.bg && is_string(attr.bg)){
				var	tmp = attr.bg;
				attr.bg = new Image();
				attr.bg.src = tmp;
			}
			this.elements[s] = new EltInterface(attr.x||0,attr.y||0,attr.bg||{color:attr.bgcolor,width:attr.width,height:attr.height},attr.visible||false,attr.taggable||false,this.ctx,this.process);
		};
		
		this.draw = function(){
			for(var elt in this.elements)
				this.elements[elt].draw();
		};
		
		this.resetTag = function(){
			for(var elt in this.elements)
				if(this.elements[elt].taggable)
					this.elements[elt].tag = true;
		};
		
		this.click = function(e){
			var	elt,clicked;
			for(elt in this.elements)
				if(this.elements[elt].click(e))	
					return true;
			return false;
		};
		
		this.addClickEvent = function(tag, fct){
			if(is_function(fct) && tag){
				this.eventInterface[tag] = fct;
			}
		};
		
		this.removeClickEvent = function(tag){
			if(tag && this.eventInterface[tag])
				this.eventInterface.remove(tag);
		};
		
		this.event = function(tag){
			if(tag && this.eventInterface[tag])
				return this.eventInterface[tag];
		}
	},


//----------------------//
//						//
//	   Class: Timer		//
//						//
//----------------------//
	Timer = function(cb, timing){
		this.elapsed = 0;
		this.limit = timing;
		this.callback = cb;
	},


//----------------------//
//						//
//	 Class: TimerMgr	//
//						//
//----------------------//
	TimerMgr = function(){
		var	active = [],
			idle = [];
			
		this.index = [];
		this.list = active;
		
		this.addTimer = function(tag, timer){
			idle[tag] = timer;
		};
		
		this.setActive = function(tag,args){
			if(!idle[tag]) return false;
			if(args){
				active[tag+args] = new Timer(function(){idle[tag].callback(args);},idle[tag].limit);
				this.index.push(tag+args);
			} else {
				active[tag] = idle[tag];
				idle.remove(tag);
				this.index.push(tag);
			}
			return true;
		};
		
		this.setIdle = function(tag,args){
			if(!active[tag] && !active[tag+args]) return false;
			if(!args)
				idle[tag] = active[tag];
			active.remove(tag+(args?args:''));
			this.index.remove(tag+(args?args:''));
			return true;
		};
		
		this.toggle = function(tag){
			return this.setActive(tag) || this.setIdle(tag);
		};
	},


//----------------------//
//						//
//	   Class: List		//
//		Extends Array	//
//						//
//----------------------//
	List = function(){
		this.data = [];
	};
	
	List.prototype.push = function(e){
		this.data.push(e);
	}
	
	List.prototype.unshift = function(e){
		this.data.unshift(e);
	}
	//Following prototypes appear when doing a For...in statement
	//Really coordinates specific, to be generalized and probably optimized
	List.prototype.contains = function(c){
		var i;
		for(i=0;i<this.data.length;i++)
			if(this.data[i].coord.x == c.x && this.data[i].coord.y == c.y)
				return true;
		return false;
	};

	List.prototype.getLightest = function(){
		var lightest = this.data[0],i;
		for(i=1;i<this.data.length;i++)
			if(lightest.f>this.data[i].f)
				lightest = this.data[i];
		this.data.remove(lightest);
		return lightest;
	};

	List.prototype.isEmpty = function(){
		return this.data.length == 0;
	};

	List.prototype.retain = function(c){
		var i;
		for(i=0;i<this.data.length;i++)
			if(this.data[i].coord.x == c.x && this.data[i].coord.y == c.y)
				return this.data[i];
		return null;
	};
	
	List.prototype.remove = function(elt){
		if(this.data[elt]){
			this.data.splice(elt,1);
			return true;
		}
		var i;
		for(i in this.data){
			if(this.data[i] == elt)
				return this.data.remove(i);
		}
		return false;
	};
	

//----------------------//
//						//
//	   Modified:		//
//		Class: Array	//
//						//
//----------------------//
	//Following prototype appears when doing a For...in statement
	Array.prototype.remove = function(elt){
		if(this[elt]){
			this.splice(elt,1);
			return true;
		}
		var i;
		for(i in this){
			if(this[i] == elt)
				return this.remove(i);
		}
		return false;
	};
	
	Array.prototype.contains = function(elt){
		var i;
		for(i in this){
			if(this[i] == elt)
				return true;
		}
		return false;
	}
	



var	Odge = function(args){
	args=args||{};
	//Class: ActiveElt, used to managed movable elements
	var	ActiveElt = function(id,x,y,nom,img){
			var	sprite = img;
			this.id = id;
			this.x = x;
			this.y = y;
			this.nom = nom;
			this.orientation = 'se';
			this.click = function(){self.move(nom);};
		
			this.setClick = function(fct){
				this.click = is_function(fct) ? fct : this.click;
			}
	
			this.contains = function(c){
				var	inner = {x:0,y:0},
					image = this.coord(),
					data = sprite.current(),
					pixel;
	
				inner.x = c.x-image.x;
				inner.y = c.y-image.y;
	
				ctx_process.clearRect(0,0,800,600);
				ctx_process.drawImage(data,0,0);
	
				pixel = [0,0,0,0];
				if(inner.x >= 0 && inner.x < data.width && inner.y >= 0 && inner.y < data.height){
					pixel = ctx_process.getImageData(inner.x, inner.y, 1, 1).data;
				}
	
				return pixel[3] != 0;
			}

			this.isUnder = function(elt){
				var	a = this.coord(),
					b = elt.coord(),
					data = sprite.current();
		
				return (a.x >= b.x && a.x <= b.x+data.width) && (a.y >= b.y && a.y <= b.y+data.height);
			}

			this.coord = function(){
				var pos = {x:0,y:0};
	
				pos.x = (resource.spriteSize.w/2)*((resource.map.row+this.y)-(this.x+1))+offset.x+sprite.offset().x;
				pos.y = (resource.spriteSize.h/2)*(this.y+this.x)+offset.y+sprite.offset().y;
	
				return pos;
			}

			this.lookTo = function(c){
				if(this.x == c.x && this.y == c.y+1)
					this.orientation = 'nw';
				else if(this.x == c.x && this.y == c.y-1)
					this.orientation = 'se';
				else if(this.x == c.x+1 && this.y == c.y)
					this.orientation = 'ne';
				else if(this.x == c.x-1 && this.y == c.y)
					this.orientation = 'so';
			}
		},
		self = this,
		resource = {spriteSize:{w:args&&args.sWidth||128,h:args&&args.sHeight||(args&&args.sWidth/2)||64},images:[],map:{index:-1}},
		layers = [],
		activeLayer = {
			selected:null,
			list:[],
			moving:[],
			layer:[],
			getNamed:function(s){
				var i;
				for(i in this.list)
					if(this.list[i].nom==s)
						return this.list[i];
				return null;
			}
		},
		collisionMap = [],
		debug = args&&args.debug||false,
		drawFPS = args&&args.drawfps||false,
		console = window.document.createElement('div'),
		maxFps = args&&args.maxfps||60,
		refreshDelay = 1000/maxFps;
		fps = 0,
		frames = 0,
		time = (new Date()).getTime(),
		timers = new TimerMgr(),
		timerDrawGame = 0,
		updateCache = true,
	
		parent = args&&args.parent||null,
		canvas = window.document.createElement('canvas'),
		ctx = canvas.getContext('2d'),
		process = window.document.createElement('canvas'),
		ctx_process = process.getContext('2d'),
		cache = window.document.createElement('canvas'),
		ctx_cache = cache.getContext('2d'),
		original = window.document.createElement('canvas'),
		ctx_original = original.getContext('2d'),
		width = args&&args.width||-1,
		height = args&&args.height||-1,
		
		loader = new ImgLoader(),
		keyPressed = {},

		offset = {x:0,y:0},
		zero = null,
		drag = false,
		move = false,
		action = '';
	console.id = 'log';
	timers.addTimer('moveActiveElt', new Timer(moveActiveElt, 150));
	
	function log(msg){
		if(!debug)
			return;
		var p = window.document.createElement('p');
		console.appendChild(p);
		p.appendChild(document.createTextNode(msg));
	};
	this.log = log;

	function loadMap(tMap){
		//The map to be loaded has to be a 3 dimensionnal Array
		//1st dimension is x coordinate
		//2nd dimension is y cooridnate
		//3rd dimension is data, length is number of layers-1
		if(!tMap)
			return;

		var i,j,k;
	
		resource.map.row = tMap.length;
		resource.map.col = tMap[0].length;
		resource.map.layers = tMap[0][0].length;
		
		for(k=0;k<resource.map.layers;k++)
			layers[k] = [];
		
		for(i=0;i<resource.map.row;i++){
			for(k=0;k<resource.map.layers;k++)
				layers[k][i] = [];
			activeLayer.layer[i] = []
			collisionMap[i] = [];
			for(j=0;j<resource.map.col;j++){
				activeLayer.layer[i][j] = -1;
				collisionMap[i][j] = true;
				for(k=0;k<resource.map.layers;k++)
					layers[k][i][j] = tMap[i][j][k];
			}
		}
		layers.push(activeLayer.layer);
		
		resource.map.layers = layers.length;
	};
	
	function updateCollisionMap(d){
		//d: boolean to show collisionMap or not
		var i,j,k,s='[';
		for(i=0;i<resource.map.layers;i++){
			s+='[';
			for(j=0;j<resource.map.row;j++){
				s+='[';
				for(k=0;k<resource.map.col;k++){
					img = layers[i][j][k];
					s+=(img>=0?!resource.images[img].collision():true)+',';
					collisionMap[j][k] = collisionMap[j][k] && (img == -1 || !resource.images[img].collision());
				}
				s+=']<br />';
			}
			s+=']<br />';
		}
		s+=']over <-----------------------';
		if(d)
			log(s);
	};
	
	function resize(){
		if(height==-1) canvas.height = window.innerHeight; else canvas.height = height; 
		if(width==-1) canvas.width = window.innerWidth; else canvas.width = width;
	};

	this.images = function(list){
		for(var i=0;i<list.length;i++){
			self.addImage(list[i]);
		}
	};
	
	this.addImage = function(image){
		var	index = image.bind===undefined?image.label:image.bind;
		if(!resource.images[index])
			resource.images[index] = new Sprite(image.label);
		resource.images[index].add(image.data, image.collides, image.height, image.offset||{x:0,y:0});
		loader.add(resource.images[image.bind]);
	};
	
	this.map = function(map){
		resource.map.index += 1;
		resource.map[resource.map.index] = map;
		loadMap(map);
	};
	
	this.addActiveElt = function(x,y,nom,img,offset){
		if(!validCoord({x:x,y:y}) || !reachableCoord({x:x,y:y}) || !is_string(img)) return;
		
		var	id = resource.images.length;
		self.addImage({label:id,data:img,bind:id,height:1,offset:offset||{x:0,y:0}});
		activeLayer.list[id] = new ActiveElt(id,x,y,nom,resource.images[id]);
		activeLayer.layer[x][y] = id;
		collisionMap[x][y] = false;
	};
	
	this.addEventListener = function(action, elt, fct){
		switch(action){
			case 'click':
				elt = activeLayer.getNamed(elt);
				if(elt!==null)
					elt.setClick(fct);
				break;
			default:
				break;
		}
	}
	
	this.interface = new Interface(ctx,ctx_process);
	
	document.oncontextmenu = function(){return false;}

	function onMouseDown(e){
		if(e.button == 2){
			zero = {x:e.offsetX,y:e.offsetY};
			canvas.style.cursor = 'url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9oGDA4iBv6oEe4AAAANSURBVAjXY2BgYGAAAAAFAAFe8yo6AAAAAElFTkSuQmCC"),auto';
		}
		drag = false;
	};

	function onMouseMove(e){
		if(zero == null) return;
		drag = true;
		offset.x += e.offsetX - zero.x;
		offset.y += e.offsetY - zero.y;
		zero = {x:e.offsetX,y:e.offsetY};
	};

	function onMouseUp(e){
		zero = null;
		canvas.style.cursor = 'auto';
	};

	var onMouseOut = onMouseUp;

	function onClick(e){	
		if(drag)
			return;
	
		if(activeLayer.selected && action=='move'){
			if(!movable(e)) return;	
			timers.setActive('moveActiveElt',activeLayer.selected.id);
			action = '';
			activeLayer.selected = null;
			return;
		}
	
		if(self.interface.click(e))
			return;

		if((activeLayer.selected=selectActiveElt(e)) == null)
			return;
		activeLayer.selected.click();
	};

	function onKeyDown(e){
		switch(e.which){
			case 16: //shift
				keyPressed.shift = true;
				break;
			case 17: //ctrl
				keyPressed.ctrl = true;
				break;
			case 18: //alt
				keyPressed.alt = true;
				return;
			case 37: //gauche
				offset.x += 50;
				break;
			case 38: //haut
				offset.y += 50;
				break;
			case 39: //droite
				offset.x -= 50;
				break;
			case 40: //bas
				offset.y -= 50;
				break;
			case 80: //p
				if(timerDrawGame!=0) 	self.pause();
				else 					self.resume();
				break;
			case 0: //firefox+linux
			case 192: //²
			case 222: //windows
				if(keyPressed.ctrl && debug){
					console.style.top = keyPressed.console ? '-300px' : '0px';
					keyPressed.console = !keyPressed.console;
				}break;
			default:
				return;
		}
	};

	function onKeyUp(e){
		switch(e.which){
			case 16: //shift
				keyPressed.shift = false;
				break;
			case 17: //ctrl
				keyPressed.ctrl = false;
				break;
			case 18: //alt
				keyPressed.alt = false;
				return;
			default:
				if(keyPressed.alt && (keyPressed.ctrl || keyPressed.shift))	
					log("Keycode: "+e.which);
				return;
		}
	};
	
	function onBlur(e){
		keyPressed = {};
	};

	canvas.addEventListener("mousedown", onMouseDown, false);
	canvas.addEventListener("mousemove", onMouseMove, false);
	canvas.addEventListener("mouseup", onMouseUp, false);
	canvas.addEventListener("mouseout", onMouseOut, false);
	canvas.addEventListener("click", onClick, false);
	window.addEventListener("keydown", onKeyDown, false);
	window.addEventListener("keyup", onKeyUp, false);
	window.addEventListener("blur", onBlur, false);
	window.addEventListener('resize', resize, false);


	 function drawGame(){
		var i,j,k,d,img,fTime,index,list,timer;
		fTime = (new Date()).getTime()-time;
		index = timers.index;
		list = timers.list;
		for(i=0;i<index.length;i++){
			timer = list[index[i]];
			timer.elapsed += fTime;
			if(timer.elapsed >= timer.limit){
				timer.callback();
				timer.elapsed -= timer.limit;
			}
		}
		if(updateCache){
			ctx_cache.fillStyle = '#222';
			ctx_cache.fillRect(0,0,cache.width,cache.height);
			for(j=0;j<resource.map.row;j++){
				for(k=0;k<resource.map.col;k++){
					for(i=0;i<resource.map.layers;i++){
						img = layers[i][j][k];
						if(img==-1)
							continue;
						d = resource.images[img].offset();
						ctx_cache.drawImage(resource.images[img].current(), (resource.spriteSize.w/2)*((resource.map.row+k)-(j+1))+d.x, (resource.spriteSize.h/2)*(k+j)+d.y);
						if(i!=resource.map.layers-1)
							ctx_original.drawImage(resource.images[img].current(), (resource.spriteSize.w/2)*((resource.map.row+k)-(j+1))+d.x, (resource.spriteSize.h/2)*(k+j)+d.y);
					}
				}
			}
			updateCache=false;
		}
		try{
			ctx.drawImage(cache, (-offset.x)>=cache.width-canvas.width?-(offset.x=-(cache.width-canvas.width)):offset.x>0?offset.x=0:(-offset.x), canvas.height-offset.y>cache.height?-(offset.y=-(cache.height-canvas.height)):offset.y>0?offset.y=0:(-offset.y), canvas.width, canvas.height, 0, 0, canvas.width, canvas.height);
		} catch(e) {
			self.pause();
		}
		if(drawFPS){
			frames++;
			if(fTime >= 500){
				var oldTime = time;
				time = (new Date()).getTime();
				fps = Math.round(frames*1000/(time - oldTime));
				frames = 0;
			}
			ctx.fillStyle = '#000';
			ctx.fillRect(0,canvas.height-20,50,20);
			ctx.fillStyle = '#0cc5fe';
			ctx.fillText(fps,7,canvas.height-7);
			ctx.fillText('fps',25,canvas.height-7);
		}
		self.interface.draw(); 
	};

	this.pause = function(){
		canvas.removeEventListener("mousedown", onMouseDown, false);
		canvas.removeEventListener("mousemove", onMouseMove, false);
		canvas.removeEventListener("mouseup", onMouseUp, false);
		canvas.removeEventListener("mouseout", onMouseOut, false);
		canvas.removeEventListener("click", onClick, false);
		clearInterval(timerDrawGame);
		timerDrawGame = 0;
		
		log('Cache: {w:'+cache.width+',h:'+cache.height+'}');
		log('Canvas: {w:'+canvas.width+',h:'+canvas.height+'}');
		log('Source: {x:'+offset.x+',y:'+offset.y+',w:'+canvas.width+',h:'+canvas.height+'}');
		log('AltSource: {x:'+(cache.width-canvas.width)+',y:'+(cache.height-canvas.height)+',w:'+canvas.width+',h:'+canvas.height+'}');
		log('MaxCoord: {w:'+(offset.x+canvas.width)+',h:'+(offset.y+canvas.height)+'}');
		log('pause');
	};

	this.resume = function(){
		canvas.addEventListener("mousedown", onMouseDown, false);
		canvas.addEventListener("mousemove", onMouseMove, false);
		canvas.addEventListener("mouseup", onMouseUp, false);
		canvas.addEventListener("mouseout", onMouseOut, false);
		canvas.addEventListener("click", onClick, false);
		timerDrawGame = setInterval(drawGame,refreshDelay);
		
	};
	
	this.move = function(elt){
		elt = activeLayer.getNamed(elt);
		if(!activeLayer.selected)
			activeLayer.selected = elt;
		else if(activeLayer.selected != elt)
			return;
		action = 'move';
	}
	
	function movable(e){
		var coord = getSquare(e);
		log('Destination: '+coord.x+';'+coord.y);
		if(reachableCoord(coord))
			return findPath(coord);
		log('Destination invalide.');
		return false;
	};

	function moveActiveElt(id){
		var	activeElt = activeLayer.list[id],
			path = activeLayer.moving[id];
		updatePlace(activeElt.x,activeElt.y,'active',-1);
		if(path != null){
			//On détermine l'orientation de l'élément actif
			if(path[0].y > activeElt.y)
				if(path[0].x > activeElt.x)
					activeElt.orientation = 'sw';
				else
					activeElt.orientation = 'se';
			else if(path[0].y < activeElt.y)
				if(path[0].x > activeElt.x)
					activeElt.orientation = 'sw';
				else
					activeElt.orientation = 'nw';
			else
				if(path[0].x > activeElt.x)
					activeElt.orientation = 'sw';
				else
					activeElt.orientation = 'ne';
		
			activeElt.x = path[0].x;
			activeElt.y = path[0].y;
		}
		path.shift();
		updatePlace(activeElt.x,activeElt.y,'active',activeElt.id);
//		log('Chemin en cours...');
		if(path.length == 0){
			log([activeElt.nom+' arrivé à destination.','Arrivée: '+activeElt.x+';'+activeElt.y]);
			activeLayer.moving.remove(id);
			timers.setIdle('moveActiveElt',id);
		}
	};
	
	function updatePlace(x,y,layer,id){
		var topX,topY,width,height,h,img,d,i,j,target={};
		if(layer=='active')
			layer = resource.map.layers - 1;
		
		if(id==-1){
			id = activeLayer.layer[x][y];
			activeLayer.layer[x][y] = -1;
			collisionMap[x][y] = true;
		} else {
			activeLayer.layer[x][y] = id;
			collisionMap[x][y] = false;
		}
		
		d = resource.images[id].offset();
		topX = (resource.spriteSize.w/2)*((resource.map.row+y)-(x+1))+d.x;
		topY = (resource.spriteSize.h/2)*(y+x)+d.y;
		
		img = resource.images[id].current();
		width = img.width;
		height = img.height;
		
		h = resource.images[id].height();
		
		if(activeLayer.layer[x][y]==-1){
			ctx_process.clearRect(0,0,800,600);
			var listover = [{x:-2,y:-2},{x:-2,y:-1},{x:-2,y:0},{x:-1,y:-2},{x:-1,y:-1},{x:-1,y:0},{x:-1,y:1},{x:0,y:-2},{x:0,y:-1},{x:0,y:0}]
			for(i=0;i<listover.length;i++){
				for(j=0;j<resource.map.layers;j++){
					if(x+listover[i].x<0 || x+listover[i].x>=resource.map.row || y+listover[i].y<0 || y+listover[i].y>=resource.map.col)
						continue;
					target.id = layers[j][x+listover[i].x][y+listover[i].y];
					if(target.id == -1)
						continue;
					target.img = resource.images[target.id].current();
					target.offset = resource.images[target.id].offset();
					ctx_process.drawImage(target.img,target.offset.x-d.x+(resource.spriteSize.w/2)*(listover[i].y-listover[i].x),target.offset.y-d.y+(resource.spriteSize.h/2)*(listover[i].y+listover[i].x));
				}
			}
			ctx_cache.drawImage(process,0,0,width,height,topX,topY,width,height);
		} else
			ctx_cache.drawImage(img,topX,topY);
		
		ctx_process.clearRect(0,0,800,600);
		var listover = [{x:0,y:1},{x:0,y:2},{x:1,y:-1},{x:1,y:0},{x:1,y:1},{x:1,y:2},{x:2,y:-1},{x:2,y:0},{x:2,y:1},{x:2,y:2}]
		for(i=0;i<listover.length;i++){
			for(j=0;j<resource.map.layers;j++){
				if(x+listover[i].x<0 || x+listover[i].x>=resource.map.row || y+listover[i].y<0 || y+listover[i].y>=resource.map.col)
					continue;
				target.id = layers[j][x+listover[i].x][y+listover[i].y];
				if(target.id == -1 || resource.images[target.id].height()<h)
					continue;
				target.img = resource.images[target.id].current();
				target.offset = resource.images[target.id].offset();
				ctx_process.drawImage(target.img,target.offset.x-d.x+(resource.spriteSize.w/2)*(listover[i].y-listover[i].x),target.offset.y-d.y+(resource.spriteSize.h/2)*(listover[i].y+listover[i].x));
			}
		}
		ctx_cache.drawImage(process,0,0,width,height,topX,topY,width,height);
	}

	function getAdjSquares(c){
		var i,j,atmp = [], tmp = null;
		for(i=-1;i<2;i++){
			for(j=-1;j<2;j++){
				tmp = {x:c.x+i,y:c.y+j};
				if(validCoord(tmp))
					atmp.push(tmp);
			}
		}
		return atmp;
	};

	function getSquare(e){
		var coord =	{y:Math.floor((e.offsetY-offset.y+(0.5*(e.offsetX-offset.x))-(resource.spriteSize.h*resource.map.row/2))/(resource.spriteSize.w/2)),
					 x:Math.floor((e.offsetY-offset.y-(0.5*(e.offsetX-offset.x))+(resource.spriteSize.h*resource.map.row/2))/(resource.spriteSize.w/2))};
		if(!validCoord(coord)) return null;
		return coord;
	};

	function selectActiveElt(e){
		var list = activeLayer.list,i,j;
		for(i=0;i<list.length;i++){
			if(list[i] && list[i].contains({x:e.offsetX,y:e.offsetY})){
				for(j=i+1;j<list.length;j++){
					if(list[j] && list[j].isUnder(list[i]) && list[j].contains({x:e.offsetX,y:e.offsetY})){
						log('isUnder i='+i+',j='+j+',length='+list.length);
						j=0;
						break;
					}
				}
				if(j==list.length)
					return list[i];
			}
		}
		return null;
	};

	function validCoord(c){
		return (c.x >= 0 && c.y >= 0 && c.x < resource.map.row && c.y < resource.map.col);
	};

	function reachableCoord(c){
		return collisionMap[c.x][c.y];
	};

	function manhattanDist(a,b){
		return Math.abs(a.x-b.x) + Math.abs(a.y-b.y);
	};

	function findPath(end){
		var opened = new List(),
			closed = new List(),
			start = {x:activeLayer.selected.x,y:activeLayer.selected.y},
			elt = null, adjacent = null, weight = 0, dist = manhattanDist(start,end), i, tmp, path;

		opened.push({coord:start,parent:null,f:weight+dist,g:weight,h:dist});
	
		while(!opened.isEmpty() && !closed.contains(end)){
			elt = opened.getLightest();
			closed.push(elt);
			adjacent = getAdjSquares(elt.coord);
			for(i=0;i<adjacent.length;i++){
				if(!closed.contains(adjacent[i]) && reachableCoord(adjacent[i])){
					if(opened.contains(adjacent[i])){
						weight = manhattanDist(adjacent[i],elt.coord);
						tmp = opened.retain(adjacent[i]);
						if(tmp.g > weight){
							dist = manhattanDist(adjacent[i],end);
							opened.push({coord:adjacent[i],parent:elt,f:dist+weight,g:weight,h:dist});
							opened.remove(tmp);
						}
					} else {
						weight = manhattanDist(adjacent[i],elt.coord);
						dist = manhattanDist(adjacent[i],end);
						opened.push({coord:adjacent[i],parent:elt,f:dist+weight,g:weight,h:dist});
					}
				}
			}
		}
	
		if(opened.isEmpty()){
			activeLayer.selected = null;
			return false;
		}
		
		tmp = closed.retain(end);
		path = [];
		do{
			path.unshift(tmp.coord);
			tmp = tmp.parent;
		}while(tmp.coord != start);
		
		activeLayer.moving[activeLayer.selected.id] = path;
	
		return true;
	};

	this.start = function(pargs){
		document.addEventListener("DOMContentLoaded", function(){	
			if(is_string(parent))
				parent = document.getElementById(parent);
			if(parent == null)
				parent = document.body;
			parent.appendChild(canvas);
			canvas.id = 'odge';
			process.id = 'process';
			process.width = 800;
			process.height = 600;
			process.style.display = 'none';
			cache.id = 'cache';
			cache.width = ((resource.map.col+resource.map.row)/2)*resource.spriteSize.w;
			cache.height = ((resource.map.col+resource.map.row)/2)*resource.spriteSize.h;
			cache.style.display = 'none';
			original.id = 'original';
			original.width = ((resource.map.col+resource.map.row)/2)*resource.spriteSize.w;
			original.height = ((resource.map.col+resource.map.row)/2)*resource.spriteSize.h;
			original.style.display = 'none';
			if(debug){
				document.body.appendChild(console);
				var cs = console.style;
				cs.position = 'absolute';
				cs.top = '-300px';
				cs.left = '0px';
				cs.width = '100%';
				cs.height = '300px';
				cs.backgroundColor = '#000';
				cs.color = '#0CC5FE';
				cs.overflow = 'auto';
				cs.zIndex = '1000';
				cs.opacity = '0.8';
			}
			
			if(pargs){
				debug = pargs.debug||debug;
				drawFPS = pargs.drawfps||drawFPS;
				maxFps = pargs.maxfps||maxFps;
				refreshDelay = 1000/maxFps;
			}

			loader.complete(function(){
				resize();
				offset = {x:(canvas.width/2)-((resource.map.col+resource.map.row)*resource.spriteSize.w/4),y:(canvas.height/2)-((resource.map.col+resource.map.row)*resource.spriteSize.h/4)};
				updateCollisionMap();
				setTimeout(dStart, 200);
			});
		}, false);
	};
	
	function dStart(){
		timerDrawGame = setInterval(drawGame,refreshDelay);
	}
};
