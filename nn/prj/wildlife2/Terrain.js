Engine.include("/components/component.transform2d.js");
Engine.include("/engine/engine.object2d.js");

Engine.initObject("Terrain", "Object2D", function(){

	var Terrain = Object2D.extend({

		// The width of the object
		width: 50,			// The width of the object
		height: 50,			// The height of the object
		color: "#ffff00",	// The color of the object
		shape: null,		// Our object's shape

		constructor: function(x, y, width, height, color){
			this.base("Terrain");
			// Add the component to move the object
			this.add(Transform2DComponent.create("move"));
			// Pick a random position to start at
			var start = Point2D.create(x, y);
			// Set our object's shape
			this.shape = Rectangle2D.create(0, 0, width, height);
			this.color = color || this.randomColor();
			// Position the object
			this.setPosition(start);
		},
		
		/**
		* Get the position of the object from the transform component.
		* @return {Point2D}
		*/
		getPosition: function(){
			return this.getComponent("move").getPosition();
		},
		
		/**
		* Set the position of the object through transform component
		* @param point {Point2D} The position to draw the text in the playfield
		*/
		setPosition: function(point){
			this.base(point);
			this.getComponent("move").setPosition(point);
		},
		
		/**
		 * Calculate and perform a move for our object.  We'll use the field dimensions from our playfield to determine when to "bounce".
		*/
		move: function(){
			var pos = this.getPosition();
			this.setPosition(pos);
		},
		
		/**
		* Update the object within the rendering context. This calls the transform components to position the object on the playfield.
		*
		* @param renderContext {RenderContext} The rendering context
		* @param time {Number} The engine time in milliseconds
		*/
		update: function(renderContext, time){
			renderContext.pushTransform();
			// The the "update" method of the super class
			this.base(renderContext, time);
			// Draw the object on the render context
			this.draw(renderContext);
			renderContext.popTransform();
		},
		
		draw: function(renderContext){
			// Generate a rectangle to represent our object
			var pos = this.getPosition();
			
			// Set the color to draw with
			renderContext.setFillStyle(this.color);
			renderContext.drawFilledRectangle(this.shape);
		},
		
		
		randomColor: function(){
			var letters = '0123456789ABCDEF'.split('');
			var color = '#';
			for(var i=0; i<6; i++)
				color += letters[Math.round(Math.random() * 15)];
			return color;
		}

});
return Terrain;
});