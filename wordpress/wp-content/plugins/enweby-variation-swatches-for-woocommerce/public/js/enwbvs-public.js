/**
 * All of the js for your public-facing functionality should be.
 * included in this file.
 *
 * @link              https://www.enweby.com/
 * @since             1.0.0
 * @package           Enweby_Variation_Swatches_For_Woocommerce
 */

(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 */
	jQuery(
		function() {
			/*jQuery( document ).on(
				"click",
				".woocommerce-product-gallery__image a",
				function(e){
					e.preventDefault();
					var prod_url    = jQuery( this ).closest( '.woocommerce-product-gallery__wrapper' ).children( '.woocommerce-loop-product__link' ).attr( 'href' );
					window.location = prod_url;
				}
			);*/

			jQuery( document ).on(
				"click",
				".enwebyvs-attribute li",
				function(){
					if ( ! jQuery( this ).hasClass( 'attr-option-disabled' )) {
						if ( ! jQuery( this ).hasClass( 'click-disabled-outofstock' )) {
							
							/* new item data variation for stock info */
							if( jQuery( this ).closest('.variations').find( '.enwebyvs-attribute' ).length > 1 ) {
								
								jQuery( this ).closest('.variations').find( '.enwebyvs-attribute-child' ).removeClass( 'out-of-stock-swatch-item click-disabled-outofstock' );
								jQuery( this ).closest('.variations').find( '.enwebyvs-attribute-child .enwbvs-stock-left-alert' ).html('');														
								jQuery( this ).closest('.variations').find( '.enwebyvs-attribute' ).removeClass('just-clicked');
								jQuery( this ).closest( '.enwebyvs-attribute' ).addClass('just-clicked');
								var str_cur_variation = jQuery( this).attr( 'data-item-variation' );
								var array_cur_variation = str_cur_variation.split(',');
								for (var ctr_variation in array_cur_variation) {
									var val_array_cur_variation = array_cur_variation[ctr_variation];										  
									jQuery( this ).closest( '.variations' ).find( '.enwebyvs-attribute li' ).each(
										function (i_1, item_1) {
																			
												var each_variation = jQuery( item_1).attr( 'data-item-variation' );
												//alert(each_variation);
												var array_each_variation =  each_variation.split(',');
												if ( array_each_variation.includes( val_array_cur_variation ) )
												{
													var for_stock_data = val_array_cur_variation.split('_');
													if( !jQuery(item_1).closest('ul').hasClass('just-clicked') ) {											
														if( 'Sold' == for_stock_data[1] ) {
															var enwbvs_disable_oos = ( 1 == enwbvs_config_var.enwbvs_disable_outofstock ) ? 'out-of-stock-swatch-item' : '';
															if(1 == enwbvs_config_var.enwbvs_disable_outofstock ) {
																var enwbvs_clickable_oos = ( 1 == enwbvs_config_var.enwbvs_clickable_outofstock ) ? '' : 'click-disabled-outofstock';
															} else {
																var enwbvs_clickable_oos = '';
															}
															//jQuery( item_1 ).addClass( 'out-of-stock-swatch-item click-disabled-outofstock' );
															jQuery( item_1 ).addClass( '' + enwbvs_disable_oos + ' ' + enwbvs_clickable_oos + '' );
															jQuery( item_1 ).find('.enwbvs-stock-left-alert').html( '<span class="soldout-span">'+for_stock_data[1]+'</span>' );
														} else {
															if( '' != for_stock_data[1] ) {
																jQuery( item_1 ).find('.enwbvs-stock-left-alert').html( '<span class="stock-left-span">'+for_stock_data[1]+' left </span>' );
															}
														}
													}
												}										
										}
									);
								}
							}	
						/* end new item data variation stock info*/
							
							
							var elem_cpid = ".cpid-" + jQuery( this ).parent().data( 'rel-pid' );
							var cur_pid   = jQuery( this ).parent().data( 'rel-pid' );
							
							jQuery( this ).closest( '.variations' ).find( '.enwbvs_fields ul li' ).removeClass( "attr-option-enabled" );
							jQuery( this ).closest( '.variations' ).find( '.enwbvs_fields ul li' ).addClass( "attr-option-disabled" );

							jQuery( this ).closest( 'ul' ).children( 'li' ).removeClass( "enwbvs-selected-elm" );

							jQuery( this ).addClass( "enwbvs-selected-elm" );

							jQuery( this ).closest( '.variations' ).find( '.enwbvs_fields select[name="attribute_' + jQuery( this ).parent().data( 'rel-id' ) + '"]' ).val( jQuery( this ).attr( 'data-attr-option-value' ) ).change();

							var active_attr_options = [];

							jQuery( this ).closest( '.variations' ).find( '.enwebyvs-attribute' ).each(
								function (i, item) {
									var attribute_id = jQuery( item ).data( 'rel-id' );

									jQuery( this ).closest( '.variations' ).find( "select#" + attribute_id + " option" ).map(
										function(index, val) {
											if (val.value != "") {
												active_attr_options.push( attribute_id + ":" + val.value );
											}
										}
									);

									jQuery( item ).attr( "data-active-options",active_attr_options );

								}
							);

							jQuery( this ).closest( '.variations' ).find( '.enwebyvs-attribute' ).each(
								function (i2, item2) {
									var data_active_options = jQuery( item2 ).attr( 'data-active-options' );
									var data_rel_id         = jQuery( item2 ).attr( 'data-rel-id' );
									jQuery( jQuery( item2 ).children() ).each(
										function(i3,item3){
											var	data_active_options_array = data_active_options.split( ',' );
											for (let ctr in data_active_options_array) {
												var	data_active_options_array_array = data_active_options_array[ctr].split( ':' );
												if ( data_active_options_array_array[0] == data_rel_id ) {
													if (jQuery( item3 ).attr( 'data-attr-option-value' ) == data_active_options_array_array[1]) {

														jQuery( item3 ).removeClass( "attr-option-disabled" );
														jQuery( item3 ).addClass( "attr-option-enabled" );

													}

												}

											}
										
										}
									);
								}
							);

								// adding attribute term to term title.
								jQuery( this ).closest( '.variations' ).find( 'th.label label[for=' + jQuery( this ).closest( '.enwebyvs-attribute' ).attr( 'data-rel-id' ) + '] .label-extended' ).remove();
								jQuery( this ).closest( '.variations' ).find( 'th.label label[for=' + jQuery( this ).closest( '.enwebyvs-attribute' ).attr( 'data-rel-id' ) + ']' ).append( '<span class="label-extended"> : ' + jQuery( this ).attr( 'data-attr-option-term-name' ) + '</span>' )

								// only works for single product page.
								var url_variation_id = jQuery( this ).closest( 'form.variations_form' ).find( 'input[name="variation_id"]' ).val();
							if ( jQuery( this ).closest( 'form.variations_form' ).find( 'input[name="variation_id"]' ).val() != "" ) {

								set_variation_url( url_variation_id );
							}

								// price change.
							if (jQuery( this ).closest( 'form.variations_form' ).find( '.single_variation_wrap span.price span.amount' ).length !== 0 && jQuery( this ).closest( 'form.variations_form' ).find( 'input[name="variation_id"]' ).val() != "" ) {

								jQuery( this ).closest( 'form.variations_form' ).find( '.single_variation_wrap .single_variation .woocommerce-variation-price' ).hide();
								var variation_html = jQuery( this ).closest( 'form.variations_form' ).find( '.single_variation_wrap .single_variation .woocommerce-variation-price' ).html();
								jQuery( this ).closest( '.product' ).children( 'a, div' ).find( '.enwbvs-cat-variation-price-wrapper' ).remove();
								jQuery( this ).closest( '.product' ).children( '.enwbvs-cat-variation-price-wrapper' ).remove();
								jQuery( this ).closest( '.product' ).children( 'span.price' ).hide();
								jQuery( this ).closest( '.product' ).children( 'a, div' ).find( 'span.price' ).hide();
								jQuery( this ).closest( '.product' ).children( 'span.price' ).after( '<span class="enwbvs-cat-variation-price-wrapper">' + variation_html + '</span>' );
								jQuery( this ).closest( '.product' ).children( 'a, div' ).find( 'span.price' ).after( '<span class="enwbvs-cat-variation-price-wrapper">' + variation_html + '</span>' );
								// fixing single product page.
								jQuery( '.enwbvs-single-product .product' ).find( '.single_variation_wrap .single_variation .woocommerce-variation-price' ).show();

							}

								// change image.
								var cur_variation_id = jQuery( this ).closest( 'form.variations_form' ).find( 'input[name="variation_id"]' ).val();
								/*var cur_product      = jQuery( this ).closest( '.product' ).find( 'a.woocommerce-loop-product__link img' );*/
								var cur_product = jQuery( this ).closest( '.product' ).find( '.attachment-woocommerce_thumbnail' );
							if ( cur_variation_id != "" ) {
								var product_variations = "";
								product_variations     = jQuery( this ).closest( 'form.variations_form' ).data( 'product_variations' );

								jQuery.each(
									product_variations,
									function(key,variation) {
										if ( variation.variation_id == cur_variation_id ) {
											if (variation.image && variation.image.src && variation.image.src.length > 1) {
												enwbvs_change_product_image( cur_product, variation );
											}
										}

									}
								);
							} else {
								enwbvs_reset_product_image( cur_product );
							}

						} //hasClass condition ends here.
					} //hasClass condition ends here.

				}
			);

			jQuery( document ).on(
				"click",
				".reset_variations",
				//to remove disabled class from all except only those who has class attr-option-disabled-real.
				function(){
					 jQuery( this ).closest( '.variations' ).find( '.enwebyvs-option-wrapaper .enwebyvs-attribute-child' ).each(			
						function (i, item) {
							
							if( jQuery( item ).hasClass('attr-option-disabled-real') ) {
								jQuery( item ).removeClass( 'enwbvs-selected-elm attr-option-enabled' );
							} else {
								jQuery( item ).removeClass( 'enwbvs-selected-elm attr-option-enabled attr-option-disabled' );
							}
							
							if( jQuery( item ).closest('.variations').find( '.enwebyvs-attribute' ).length > 1 ) {
								jQuery( item ).removeClass( 'out-of-stock-swatch-item click-disabled-outofstock' );
								jQuery( item ).find( '.enwbvs-stock-left-alert' ).html('');
							}
						}
					);
					

					//jQuery( this ).closest( '.variations' ).find( '.enwebyvs-option-wrapaper .enwebyvs-attribute-child' ).removeClass( 'enwbvs-selected-elm attr-option-enabled attr-option-disabled' );
					jQuery( this ).closest( '.variations' ).find( '.enwebyvs-option-wrapaper .enwebyvs-attribute-child .enwbvsfw-radio' ).prop( "checked", false );
					jQuery( this ).closest( '.variations' ).find( 'th.label .label-extended' ).remove();
					/*enwbvs_reset_product_image( jQuery( this ).closest( '.product' ).find( 'a.woocommerce-loop-product__link img' ) );*/
					enwbvs_reset_product_image( jQuery( this ).closest( '.product' ).find( '.attachment-woocommerce_thumbnail' ) );
					jQuery( this ).closest( '.product' ).children( 'span.price' ).show();
					jQuery( this ).closest( '.product' ).children( '.enwbvs-cat-variation-price-wrapper' ).remove();;
					jQuery( this ).closest( '.product' ).children( 'a, div' ).find( 'span.price' ).show();
					jQuery( this ).closest( '.product' ).children( 'a, div' ).find( '.enwbvs-cat-variation-price-wrapper' ).remove();;
				}
			);

			// To fix quickview issue.
			/*jQuery(document).ajaxComplete(function(){
			jQuery('.enwebyvs-option-wrapaper ul li[selected=selected]').each(function (index, liItem) {

				if( 1 != jQuery(".enwbvs-single-product table.variations").data('ajax_complete-status') ) {
					// used SetTimeout for Making click asynchronous
					setTimeout( () => jQuery(this).trigger('click'), 500);
					jQuery(".enwbvs-single-product table.variations").attr('data-ajax_complete-status',1);
				}
			});

			});*/

		}
	);

})( jQuery );


// To fix verbos issue on chrome.
(function () {
	if (typeof EventTarget !== "undefined") {
		let func                               = EventTarget.prototype.addEventListener;
		EventTarget.prototype.addEventListener = function (type, fn, capture) {
			this.func = func;
			if (typeof capture !== "boolean") {
				capture         = capture || {};
				capture.passive = false;
			}
			this.func( type, fn, capture );
		};
	};
}());

