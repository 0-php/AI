// Load the components and engine objects
Engine.include("/components/component.transform2d.js");
Engine.include("/engine/engine.object2d.js");

Engine.initObject("Creature", "Object2D", function(){

	var Creature = Object2D.extend({

		// The width of the creature
		radius: 10,			// The width of the creature
		color: "#ffff00",	// The color of the creature
		velocity: null,		// The velocity of our creature
		shape: null,		// Our creature's shape
		FOVradius: 50,		// Field of view, radius in pixels (angle is 360)
		FOVshape: null,
		FOVcolor: '#ffffaa',

		constructor: function(){
			this.base("Creature");
			// Add the component to move the creature
			this.add(Transform2DComponent.create("move"));
			// Pick a random position to start at
			var fBox = WildLife.getFieldBox().get();
			var start = Point2D.create(50 + (Math.floor(Math2.random() * fBox.w - this.radius)), 50 + (Math.floor(Math2.random() * fBox.h - this.radius)));
			// Pick a random velocity for each axis
			this.velocity = this.getRandomVelocity();
			// Set our creature's shape
			this.shape = Circle2D.create(0, 0, this.radius);
			// Set our creature's FOV shape
			this.FOVshape = Circle2D.create(0, 0, this.FOVradius);
			// Position the creature
			this.setPosition(start);
		},
		
		randomRange: function(minVal, maxVal, whole){
			var randVal = minVal + (Math.random() * (maxVal - minVal));
			return whole ? Math.floor(randVal) : randVal;
		},

		getRandomVelocity: function(){
			return Point2D.create(this.randomRange(-1, 2, true), this.randomRange(-1, 2, true));
		},

		/**
		* Update the creature within the rendering context.  This calls the transform components to position the creature on the playfield.
		*
		* @param renderContext {RenderContext} The rendering context
		* @param time {Number} The engine time in milliseconds
		*/
		update: function(renderContext, time){
			renderContext.pushTransform();
			// The the "update" method of the super class
			this.base(renderContext, time);
			// Update the creature's position
			this.move();
			// Draw the creature on the render context
			this.draw(renderContext);
			renderContext.popTransform();
		},

		/**
		* Get the position of the creature from the transform component.
		* @return {Point2D}
		*/
		getPosition: function(){
			return this.getComponent("move").getPosition();
		},

		/**
		* Set the position of the creature through transform component
		* @param point {Point2D} The position to draw the text in the playfield
		*/
		setPosition: function(point){
			this.base(point);
			this.getComponent("move").setPosition(point);
		},

		/**
		 * Calculate and perform a move for our creature.  We'll use the field dimensions from our playfield to determine when to "bounce".
		*/
		move: function(){
			var pos = this.getPosition();
			pos.add(this.getRandomVelocity());
			this.setPosition(pos);
			
			// Determine if we hit a "wall" of our playfield
			/*var fieldBox = WildLife.getFieldBox().get();
			if((pos.x + this.width > fieldBox.r) || (pos.x < 0)){ // Reverse the X velocity
				this.velocity.setX(this.velocity.get().x * -1); console.info('velocity reversed to ' + this.velocity.get().x * -1);
			}if((pos.y + this.height > fieldBox.b) || (pos.y < 0)){ // Reverse the Y velocity
				this.velocity.setY(this.velocity.get().y * -1); console.info('velocity reversed to ' + this.velocity.get().y * -1); }*/
		},

		/**
		 * Draw our game creature onto the specified render context.
		 * @param renderContext {RenderContext} The context to draw onto
		*/
		draw: function(renderContext){
			// Generate a rectangle to represent our creature
			var pos = this.getPosition();
			
			// Draw shape
			renderContext.setFillStyle(this.color);
			renderContext.drawFilledCircle(this.shape);
			// Draw field of view
			renderContext.setFillStyle(this.FOVcolor);
			renderContext.drawFilledCircle(this.FOVshape);
			
		}

	}, { // Static
		/**
		* Get the class name of this creature
		* @return {String} The string MyObject
		*/
		getClassName: function(){
			return "Creature";
		}
	});
return Creature;
});
