function cape_interactive(event){
	var container = document.getElementById('cape_slide_container');
	function container_offsets(){
		let offsets = container.getBoundingClientRect();
		let top = offsets.top;
		let left = offsets.left;
		return [top, left];
	}
	function get_computed_styles(elem){
		return window.getComputedStyle(elem);
	}
	var slides = document.getElementsByClassName('cape_slides');
	window.addEventListener("load", function(event){
		var new_slide_width = 700;
		//set slide widths
		Array.prototype.forEach.call(slides, function(element) {
			let slide_styles = get_computed_styles(element);
			let slide_height = parseInt(slide_styles.getPropertyValue('height')); 
			let image_height = parseInt(element.dataset.height);
			let image_width = parseInt(element.dataset.width);
			let aspect_ratio = image_width / image_height;
			new_slide_width = slide_height * aspect_ratio;
			element.style.width = new_slide_width+'px';
		});
	})
	
}
if (document.readyState === 'loading') {  
	document.addEventListener('DOMContentLoaded', cape_interactive );
}
else{
	cape_interactive();
} 