(function( $ ) {
	$( document ).ready(function( event ) {

		function empty_post_descendants( elems ){
			$.each( elems, function( index, element ){
				if( element.tagName !== 'P' ){
					element.remove();
				}
			})
		}

		var select = $( '#user_slide_categories' );
		var set_context = $( '#user_slides' );
		var notice = $( '#cat_selection_notice' );
		select.on( 'change', function(event){
			event.stopPropagation();
			var value = this.value;
			if( value !== ''){
				notice.hide();
				$.ajax({
					url : slide_cat_selection.url,
					type : 'POST',
					context : set_context,
					dataType: 'json',
					data: {
						'action' : 'slide_cat_selection',
						'category' : value
					},
					success : function(response){
						let descendants = this.children();
						empty_post_descendants( descendants );
						response.forEach( element => {
							var checkbox_container = document.createElement( 'div' );
							var label_checkbox = document.createElement( 'label' );
							var input_checkbox = document.createElement( 'input' );
							label_checkbox.htmlFor = element.post_title;
							label_checkbox.innerHTML = element.post_title;	
							input_checkbox.type = "checkbox";
							input_checkbox.id = element.post_title;
							input_checkbox.value = element.post_title;
							input_checkbox.name = "cape_admin[slides_selection][]";
							checkbox_container.append( input_checkbox );
							checkbox_container.append( label_checkbox );
							this.append(checkbox_container);
						});
					}
				});
			}
			else{
				let descendants = set_context.children();
				notice.show();	
				empty_post_descendants( descendants );
			}                  
		});
	});
  })(jQuery);
