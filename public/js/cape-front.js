function cape_animation(){
	const container = document.getElementById('cape_slide_container');
	const slide_objects = container.getElementsByClassName('cape_slides');
	const container_properties = container.properties;
	const max_dim = container_properties.maximum_dimension , 
		slide_offsets = container_properties.x_offsets,
		container_y = container_properties.y_position;
	const slide_length = slide_objects.length;
	var slide_state = [ false, false, false, false, 0, 1, 2, 3, 4 ];
	const state_length = 8;
	var mid_index = 4, last_index = 8;
	const slide_left = container.querySelector('.next-left-arrow'),
 		slide_right = container.querySelector('.next-right-arrow');
	var push = Array.prototype.push,
	 splice = Array.prototype.splice,
	 unshift = Array.prototype.unshift;
	function rotate_right( end_value){
		splice.call( this, 0, 1 );
		push.apply( this, end_value );
	}
	function rotate_left( start_value ){
		splice.call( this, ( this.length - 1 ), 1);
		unshift.apply( this, start_value )
	}
	function decimal_precision(value){
		return parseFloat(value.toFixed(2));
	}
	var properties, x_travel, the_slide, the_animation, scale_flag, set_zindex, slide_direction, end_value, start_value;
	function shift_slide_right(){
		slide_direction = "right";
		if( slide_state[ mid_index ] !== ( slide_length - 1 ) ){
			slide_state.forEach(( element, index ) => {
				if( element !== false && Number.isInteger(element) && (element <= slide_length) ){
					the_slide = slide_objects[ element ];
					properties = the_slide.cape_state;
					set_zindex = properties.zindex;
					if( index <= 4 ){
						scale_flag = -1;
						--set_zindex;
					}
					else{
						scale_flag = 1;
						++set_zindex;
					}
					the_slide.style.zIndex = set_zindex;
					x_travel = properties.horizontal_offset - slide_offsets[ index - 1 ];
					if( properties.horizontal_offset !== slide_offsets[0] ){ //slide at first index doesn't need to be animated
						the_animation = new slide_animation( x_travel, properties, the_slide, scale_flag, set_zindex, slide_direction );
					}
				}
			});
			//just pretending here, dont know if this is good
			( slide_length <= 5 ) && rotate_right.call( slide_state, [false] );
			if( slide_length > 5){
				end_value = slide_state[ last_index ] + 1;
				end_value = ( end_value > ( slide_length - 1  ) || slide_state[ last_index ] === false ) ? false : end_value;
				rotate_right.call( slide_state, [ end_value ]);
			}  
		}
		else{
			console.log("slide end");
		}
	}
	function shift_slide_left(){
		slide_direction = "left";
		if( slide_state[ mid_index ] !== 0 ){
			slide_state.forEach(( element, index ) => {
				if( element !== false && Number.isInteger(element) && (element <= slide_length) ){
					the_slide = slide_objects[ element ];
					properties = the_slide.cape_state;
					set_zindex = properties.zindex;
					if( index >= 4 ){	
						scale_flag = -1;
						--set_zindex;
					}
					else{
						scale_flag = 1;
						++set_zindex;
					}
					the_slide.style.zIndex = set_zindex;
					x_travel = slide_offsets[ index + 1 ] - properties.horizontal_offset;
					if( properties.horizontal_offset !== slide_offsets[8] ){ //slide at last index doesn't need to be animated
						the_animation = new slide_animation( x_travel, properties, the_slide, scale_flag, set_zindex, slide_direction );
					} 
				}
			});
			( slide_length <= 5 ) && rotate_left.call( slide_state, [false] );
			if( slide_length > 5){
				start_value = slide_state[0] - 1;
				start_value = ( start_value < 0 || slide_state[0] === false ) ? false : start_value;
				rotate_left.call( slide_state, [ start_value ]);
			}
		}
		else{
			console.log( "slide_end");
		}
	}
	function remove_click_listener(){
		slide_right.removeEventListener( 'click', shift_slide_right, false );
		slide_left.removeEventListener( 'click', shift_slide_left, false );
	}
	function reattach_click_listener(){
		slide_right.addEventListener( 'click', shift_slide_right, false );
		slide_left.addEventListener( 'click', shift_slide_left, false );
	}
	
	function slide_animation( x, properties, elem, scale_flag, z_index, direction ){
		this.x_travel = x;
		this.x_offset = properties.horizontal_offset;
		this.scale = properties.scale;
		this.height = properties.slide_height;
		this.width = properties.slide_width;
		this.opacity = properties.opacity;
		this.scale_flag = scale_flag;
		this.z_index = z_index;
		this.startTime = window.performance.now();
		this.elem = elem;
		this.start_stamp = null;
		this.scale_dx = ( 0.15 / 400);
		this.travel_dx = ( x / 400 );
		this.opacity_dx = ( 0.2 / 400);
		this.animation = ( direction === "left" ) ? requestAnimationFrame(this.slide_frame_left.bind(this)) : requestAnimationFrame(this.slide_frame_right.bind(this));
	}
	slide_animation.prototype.slide_frame_right = function( time ){
		remove_click_listener();
		now = time;
		this.start_stamp = ( !this.start_stamp ) ? now : this.start_stamp;
		elapsed = now - this.start_stamp;
		if( elapsed > 400){
			elapsed = 400;
		}
		var dx_height, dx_width, x_travel, set_top, set_opacity, request, scale_slide, set_zindex;
		
		if( this.scale_flag === -1){
			scale_slide = this.scale - (elapsed * this.scale_dx) ;
			dx_width = dx_height = ( scale_slide  * max_dim);
			x_travel = ( this.x_offset - ( elapsed * this.travel_dx ));
			set_top = ( container_y + ( ( max_dim - dx_height ) / 2));
			set_opacity = this.opacity - ( elapsed * this.opacity_dx );
		}
		else if(this.scale_flag === 1) {
			scale_slide = this.scale + (elapsed * this.scale_dx);
			dx_width = dx_height = ( scale_slide * max_dim);
			x_travel = (this.x_offset - ( elapsed * this.travel_dx ));
			set_top = ( container_y + ((max_dim - dx_height ) / 2) );
			set_opacity = this.opacity + ( elapsed * this.opacity_dx );
		}
		this.elem.style.height = dx_height + 'px';
		this.elem.style.width = dx_width + 'px';
		this.elem.style.left = x_travel + 'px';
		this.elem.style.top = set_top + 'px';
		this.elem.style.opacity = set_opacity;
		if( elapsed === 400 ){
			reattach_click_listener();
			scale_slide = decimal_precision(scale_slide);
			set_opacity = decimal_precision(set_opacity);
			set_zindex = decimal_precision(this.z_index);
			//overwrite properties
			this.elem.cape_state = {
				opacity : set_opacity,
				scale : scale_slide,
				slide_width : dx_height,
				slide_height : dx_width,
				horizontal_offset : x_travel,
				zindex : this.z_index
			};
			if ( this.elem.cape_state.horizontal_offset === slide_offsets[4] ){
				this.elem.querySelector('.cape_post_content').style.display = "block";
			}
			else{
				this.elem.querySelector('.cape_post_content').style.display = "none";
			} 
			cancelAnimationFrame(request);
		}
		else{
			request = requestAnimationFrame(this.slide_frame_right.bind(this));
		}
	}
	// yes, I know the code repeates except for the additive x_travel but a single ternary operator is faster than two
	slide_animation.prototype.slide_frame_left = function( time ){
		remove_click_listener();
		now = time;
		this.start_stamp = ( !this.start_stamp ) ? now : this.start_stamp;
		elapsed = now - this.start_stamp;
		if( elapsed > 400){
			elapsed = 400;
		}
		var dx_height, dx_width, x_travel, set_top, set_opacity, request, scale_slide, set_zindex;
		
		if( this.scale_flag === -1){
			scale_slide = this.scale - (elapsed * this.scale_dx) ;
			dx_width = dx_height = ( scale_slide  * max_dim);
			x_travel = ( this.x_offset + ( elapsed * this.travel_dx ));
			set_top = ( container_y + ( ( max_dim - dx_height ) / 2));
			set_opacity = this.opacity - ( elapsed * this.opacity_dx );
		}
		else if(this.scale_flag === 1) {
			scale_slide = this.scale + (elapsed * this.scale_dx);
			dx_width = dx_height = ( scale_slide * max_dim);
			x_travel = (this.x_offset + ( elapsed * this.travel_dx ));
			set_top = ( container_y + ((max_dim - dx_height ) / 2) );
			set_opacity = this.opacity + ( elapsed * this.opacity_dx );
		}
		this.elem.style.height = dx_height + 'px';
		this.elem.style.width = dx_width + 'px';
		this.elem.style.left = x_travel + 'px';
		this.elem.style.top = set_top + 'px';
		this.elem.style.opacity = set_opacity;
		if( elapsed === 400 ){
			reattach_click_listener();
			scale_slide = decimal_precision(scale_slide);
			set_opacity = decimal_precision(set_opacity);
			set_zindex = decimal_precision(this.z_index);
			this.elem.cape_state = {
				opacity : set_opacity,
				scale : scale_slide,
				slide_width : dx_height,
				slide_height : dx_width,
				horizontal_offset : x_travel,
				zindex : this.z_index
			}; //overwrite properties
			if ( this.elem.cape_state.horizontal_offset === slide_offsets[4] ){
				this.elem.querySelector('.cape_post_content').style.display = "block";
			}
			else{
				this.elem.querySelector('.cape_post_content').style.display = "none";
			} 
			cancelAnimationFrame(request);
		}
		else{
			request = requestAnimationFrame(this.slide_frame_left.bind(this));
		}
		
	}
	slide_right.addEventListener( "click", shift_slide_right );
	slide_left.addEventListener( "click", shift_slide_left );
}
if (document.readyState === 'loading') {  
	document.addEventListener( 'DOMContentLoaded', cape_animation );
}
else{
	cape_animation();
} 