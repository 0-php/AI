// Load all required engine components
Engine.include("/rendercontexts/context.canvascontext.js");

// Load the game objects
Game.load("/Creature.js");
Game.load("/Terrain.js");

Engine.initObject("WildLife", "Game", function(){

	var WildLife = Game.extend({

		constructor: null,

		// The rendering context
		renderContext: null,

		// Engine frames per second
		engineFPS: 30,

		// The play field
		fieldBox: null,
		fieldWidth: 640,
		fieldHeight: 640,
		
		terrainData: {
			'grass':	[0, 0, 240, 240, '#82E69B'],
			'prairie':	[320, 0, 240, 240, '#AC933C'],
			'forest':	[0, 320, 240, 240, '#148F36'],
			'lake':		[320, 320, 240, 240, '#88B9E6']
		},

		// Called to set up the game, download any resources, and initialize the game to its running state.
		setup: function(){
			// Set the FPS of the game
			Engine.setFPS(this.engineFPS);

			$("#loading").remove();

			// Create the render context
			this.fieldBox = Rectangle2D.create(0, 0, this.fieldWidth, this.fieldHeight);
			this.renderContext = CanvasContext.create("Playfield", this.fieldWidth, this.fieldHeight);
			this.renderContext.setBackgroundColor("black");

			// Add the new rendering context to the default engine context
			Engine.getDefaultContext().add(this.renderContext);

			// Create the game object and add it to the render context. It'll start animating immediately.
			for(tData in this.terrainData){
				t = this.terrainData[tData];
				this.renderContext.add(Terrain.create(t[0], t[1], t[2], t[3], t[4]));
			}
			this.renderContext.add(Creature.create());
		},

		// Called when a game is being shut down to allow it to clean up any objects, remove event handlers, destroy the rendering context, etc.
		teardown: function(){
			this.renderContext.destroy();
		},

		// Return a reference to the render context
		getRenderContext: function(){
			return this.renderContext;
		},

		// Return a reference to the playfield box
		getFieldBox: function(){
			return this.fieldBox;
		}

   });
   return WildLife;
});