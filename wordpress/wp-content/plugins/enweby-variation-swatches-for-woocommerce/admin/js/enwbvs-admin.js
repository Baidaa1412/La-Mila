/**
 * All of the js for your admin-facing functionality should be.
 * included in this file.
 *
 * @link              https://www.enweby.com/
 * @since             1.0.0
 * @package           Enweby_Variation_Swatches_For_Woocommerce
 */
// phpcs:ignoreFile
var enwbvs_init = (function($, window, document) {
	'use strict';	

	function setup_tiptip_tooltips(){
		var tiptip_args = {
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		};

		$('.tips').tipTip( tiptip_args );
	}

	function isValidHexColor(value) {      
		if ( preg_match( '/^#[a-f0-9]{6}$/i', value ) ) { // if user insert a HEX color with #     
			return true;
		}     
		return false;
	}	

	function escapeHTML(html) {
	   var fn = function(tag) {
		   var charsToReplace = {
			   '&': '&amp;',
			   '<': '&lt;',
			   '>': '&gt;',
			   '"': '&#34;'
		   };
		   return charsToReplace[tag] || tag;
	   }
	   return html.replace(/[&<>"]/g, fn);
	}
	 	 
	function isHtmlIdValid(id) {
		//var re = /^[a-z]+[a-z0-9\_]*$/;
		var re = /^[a-z\_]+[a-z0-9\_]*$/;
		return re.test(id.trim());
	}
		
	function setup_color_picker(form) {
	 	
 		var i = 0;
        form.find(".enwbvs-admin-colorpick--not-used").iris({

            change: function(event, ui) {

                $(this).parent().find(".enwbvs-admin-colorpickpreview").css({
                    backgroundColor: ui.color.toString()
                })
                
            },
            hide: !0,
            border: !0
        }).click(function() {
        	if($(this).closest(".enwbvs_settings_fields_form").length  > 0){
        		$(".iris-picker").hide(), $(this).closest(".enwbvs_settings_fields_form").find(".iris-picker").show()
        	}else{
        		  $(".iris-picker").hide(), $(this).closest("td").find(".iris-picker").show()
        	}
          
           
        }), $("body").click(function() {
            $(".iris-picker").hide()
        }), $(".enwbvs-admin-colorpick").click(function(event) {
            event.stopPropagation()
        })
        i++;
    }

	
	function setup_popup_tabs(form, selector_prefix){
		$("."+selector_prefix+"-tabs-menu a").click(function(event) {
			event.preventDefault();
			$(this).parent().addClass("current");
			$(this).parent().siblings().removeClass("current");
			var tab = $(this).attr("href");
			$("."+selector_prefix+"-tab-content").not(tab).css("display", "none");
			$(tab).fadeIn();
		});
	}
	
	function open_form_tab(elm, tab_id, form_type){
		var tabs_container = $("#enwbvs-tabs-container_"+form_type);
		
		$(elm).parent().addClass("current");
		$(elm).parent().siblings().removeClass("current");
		var tab = $("#"+tab_id+"_"+form_type);
		tabs_container.find(".enwbvs-admin-tab-content").not(tab).css("display", "none");
		$(tab).fadeIn();
	}
	
	function prepare_field_order_indexes(elm) {
		$(elm+" tbody tr").each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index(elm+" tbody tr") ) );
		});
	}

	
	function get_property_field_value(form, type, name){
		var value = '';
		
		switch(type) {
			case 'select':
				value = form.find("select[name=i_"+name+"]").val();
				value = value == null ? '' : value;
				break;
				
			case 'checkbox':
				value = form.find("input[name=i_"+name+"]").prop('checked');
				value = value ? 1 : 0;
				break;
				
			default:
				value = form.find("input[name=i_"+name+"]").val();
				value = value == null ? '' : value;
		}	
		
		return value;
	}
	
		
	function set_property_field_value(form, type, name, value, multiple){
		
		switch(type) {
			case 'select':
				if(multiple == 1 && typeof(value) === 'string'){
					value = value.split(",");
					name = name+"[]";
				}
				form.find('select[name="i_'+name+'"]').val(value);
				break;
				
			case 'checkbox':
				value = value == 'yes' || value == 1 ? true : false;
				form.find("input[name=i_"+name+"]").prop('checked', value);
				break;

			case 'colorpicker':

				form.find("input[name=i_"+name+"]").val(value);
				form.find('span.'+name+'_preview').css('background-color',value);
				break;

			case 'radio' : 

				form.find("input[name=i_"+name+"]").val(value);
				form.find($('.'+value)).addClass('rad-selected').siblings('.rad-selected').removeClass('rad-selected');
				break;

			default:
				form.find("input[name=i_"+name+"]").val(value);
		}	
	}

	var active_tab = 0;
	function setup_form_side_popup(){

		$('.pp_nav_tabs > li').click(function(){
			var index = $(this).data('index');
			var popup = $(this).closest('.popup-wrapper');
			open_tab(popup, $(this), index);
			active_tab = index;
		});
	}

	function open_tab(popup, link, index){
		var panel = popup.find('.data_panel_'+index);

		close_all_data_panel(popup);
		link.addClass('active');
		panel.css("display", "block");
	}

	
	function close_all_data_panel(popup){

		popup.find('.pp_nav_tabs > li').removeClass('active');

		popup.find('.data-panel').css("display", "none");

		popup.find('.global-tabs > li').removeClass('active');
	}
		
	return {
		escapeHTML : escapeHTML,
		isHtmlIdValid : isHtmlIdValid,
		isValidHexColor : isValidHexColor,
		setup_tiptip_tooltips : setup_tiptip_tooltips,
		setupColorPicker : setup_color_picker,
		setupPopupTabs : setup_popup_tabs,
		openFormTab : open_form_tab,
		get_property_field_value : get_property_field_value,
		set_property_field_value : set_property_field_value,
		setup_form_side_popup : setup_form_side_popup,
   	};
}(window.jQuery, window, document));


function enwbvsfwOpenFormTab(elm,tab_id, form_type){
    enwbvs_init.openFormTab(elm, tab_id, form_type)
}
var enwbvs_settings = (function($, window, document) {
   
    'use strict';
    var mediaUploader;
  
    var MSG_INVALID_NAME = 'NAME/ID must begin with a lowercase letter ([a-z]) and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")';
      
    /*------------------------------------
    *---- ON-LOAD FUNCTIONS - SATRT ----- 
    *------------------------------------*/

    $(function() {

        var settings_div = $('#edittag'),
          add_tag_div = $('#addtag'),
          advanced_settings_div = $('#advanced_settings_form'),
          custom_attr_div = $('.enwbvs-custom-table'),
          design_settings_div = $('#enwbvsfw_design_form');

        enwbvs_init.setupColorPicker(advanced_settings_div);
        enwbvs_init.setupColorPicker(settings_div);
        enwbvs_init.setupColorPicker(add_tag_div);
        enwbvs_init.setupColorPicker(custom_attr_div);
        enwbvs_init.setupColorPicker(design_settings_div);

        var tabs_wrapper = $('.enwbvsfwadmin-wrapper');
        var last_active_tab = $('#last_active_tab').val();

        enwbvs_init.setup_form_side_popup();
    });


    function upload_icon_image(elm, e){
        
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
            text: 'Choose Image'
        },  multiple: false });
        // When a file is selected, grab the URL and set it as the text field's value
        var $image_div =  $(elm).parents('.enwbvs-upload-image'),
            $index_media_image = $image_div.find('.i_index_media_img'),
            $index_media = $image_div.find('.i_index_media'),
            $remove_button = $image_div.find('.enwbvs_remove_image_button');
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();      
            $index_media_image.attr('src', attachment.url);
            $index_media.val(attachment.id);
            $('.enwbvs_remove_uploaded').show();
            $remove_button.show();

        });

        mediaUploader.open();
    }

    var placeholder = enwbvs_var.placeholder_image;
	
	//var placeholder="";
    function remove_icon_image(elm,e){
	    var $image_div =  $(elm).parents('.enwbvs-upload-image'),
            $index_media_image = $image_div.find('.i_index_media_img'),
            $index_media = $image_div.find('.i_index_media'),
            $remove_button = $image_div.find('.enwbvs_remove_image_button');

        $index_media_image.attr( 'src',placeholder);
        $index_media.val( '' );
        $remove_button.hide();
        return false;
    }

    function change_term_type(elm,e){
        var type = $(elm).val(),
            form = $(elm).closest('.enwbvs_custom_attribute');

        var custom_attr_div = $('.enwbvs-custom-table');
        enwbvs_init.setupColorPicker(custom_attr_div);

        if(type == 'select'){
            form.find($(".enwbvs-custom-table")).hide();
        }else{
            form.find($(".enwbvs-custom-table")).hide();
            form.find($(".enwbvs-custom-table-"+ type)).show();
            form.find($(".enwbvs-tooltip-row")).show();
        }

        if(type == 'select'){
            form.find($(".enwbvs-tooltip-row")).hide();
        }else{
            form.find($(".enwbvs-tooltip-row")).show();
        }
    }

    function open_term_body(elm, e){
        var element = $(elm);
        var parent = $(elm).closest('td');
        var parent_table = $(elm).closest('table');

        if(!element.hasClass('open')){
            parent_table.find('.enwbvs-local-body').hide();
            parent.find('.enwbvs-local-body').show('slow');

            parent_table.find('.enwbvs-local-head').removeClass('open');
            element.addClass('open');
        }else{
            element.removeClass('open');
            parent.find('.enwbvs-local-body').hide();
        }
    }

    var DESIGN_FORM_FIELDS = {

        design_name : {name : 'design_name', label : ' Design Name', type : 'text', value : ''},

        icon_height : {name : 'icon_height',  type : 'text', value : '45px'},
        icon_width  : {name : 'icon_height',  type : 'text', value : '45px'},
        icon_shape  : {name : 'icon_shape',type : 'select', value : '0px'},

        icon_label_height : {name : 'icon_height',  type : 'text', value : '45px'},
        icon_label_width  : {name : 'icon_height',  type : 'text', value : 'auto'},
        label_size             : {name : 'label_size', type : 'text', value : '16px'},
        label_background_color : {name : 'label_background_color', type : 'colorpicker', value :'#fff'},
        label_text_color       : {name : 'label_text_color', type : 'colorpicker', value :'#000'},

        icon_border_color    : {name : 'icon_border_color', type : 'colorpicker', value :'#d1d7da'},
        icon_border_color_hover    : {name : 'icon_border_color_hover', type : 'colorpicker', value :'#aaaaaa'},
        icon_border_color_selected : {name : 'icon_border_color_selected', type : 'colorpicker', value :'#827d7d'},

        common_selection_style           : {name : 'common_selection_style', type : 'select', value :'border'},
        tick_color                       : {name : 'tick_color', type : 'colorpicker', value :'#ffffff'},
        tick_size                        : {name : 'tick_size', type : 'text', value :'15px'},
        label_selection_style            : {name : 'label_selection_style', type : 'select', value :'border'},
        label_background_color_hover     : {name : 'label_background_color_hover', type : 'colorpicker', value :'#ffffff'},
        label_text_color_hover           : {name : 'label_text_color_hover', type : 'colorpicker', value :'#000000'},
        label_background_color_selection : {name : 'label_background_color_selection', type : 'colorpicker', value :'#000000'},
        label_text_color_selection       : {name : 'label_text_color_selection', type : 'colorpicker', value :'#ffffff'},
        label_tick_color                 : {name : 'label_tick_color', type : 'colorpicker', value :'#000000'},
        label_tick_size                  : {name : 'label_tick_size', type : 'text', value :'15px'},
        enable_swatch_dropdown           : {name : 'enable_swatch_dropdown', type : 'checkbox', value :0},

        tooltip_enable                : {name : 'tooltip_enable', type : 'checkbox', value :0},
        tooltip_text_background_color : {name : 'tooltip_text_background_color', type : 'colorpicker', value :'#000000'},
        tooltip_text_color            : {name : 'tooltip_text_color', type : 'colorpicker', value :'#ffffff'},  

    };


    $( document ).ajaxComplete( function( event, request, options ) {
        if ( request && 4 === request.readyState && 200 === request.status
        && options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {

            var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
            if ( ! res || res.errors ) {
                return;
            }
            // Clear Thumbnail fields on submit
            $('.i_index_media_img' ).attr( 'src', placeholder);
            $('.i_index_media').val('');
            $('#product_cat_thumbnail_id' ).val( '' );
            $('.enwbvs_remove_image_button' ).hide();
            $('.enwbvs_settings_fields_form').find('.enwbvs-admin-colorpickpreview').css('background-color','');
            return;
        }

        if ( request && 4 === request.readyState && 200 === request.status
        && options.data && 0 <= options.data.indexOf( 'action=woocommerce_save_attributes' ) ) {
            var this_page = window.location.toString();
            this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );
            var custom_attr_div = $('.enwbvsfw-custom-table');

            $('#enwbvsfw-product-attribute-settings').load(this_page+' #custom_variations_inner',function(){
                $('#enwbvsfw-product-attribute-settings').trigger( 'reload' );
                enwbvs_init.setupColorPicker($('.enwbvs-custom-attr-color-td'));
                $('#enwbvsfw-product-attribute-settings').trigger('init_tooltips');
            });
        }

    });

    function open_attribute_form(elm, id, design_type){

        var form = $('#enwbvsfw_attribute_form_pp');
        open_design_popup(elm, form);

         var terms_json = $(elm).data('terms');

        var type = terms_json['type'],
            name = terms_json['name'],
            label     = terms_json['label'];

        form.find('.attr-label').text(label);
        enwbvs_init.set_property_field_value(form, "hidden", "attr_id",id, 0);
        enwbvs_init.set_property_field_value(form,"text", "label",label, 0);
        enwbvs_init.set_property_field_value(form,"text", "name",name, 0);
        enwbvs_init.set_property_field_value(form,"select","type", type, 0); 
        enwbvs_init.set_property_field_value(form, "select", "swatch_design_style", design_type, 0); 

        populate_attribute_term_fields(form, terms_json, id, type);
        enwbvs_init.setupColorPicker(form);
    }

    function populate_attribute_term_fields(form, terms_json, id, attr_type) {
 
        attr_type = attr_type === 'image_with_label' ? 'image' : attr_type;
        var terms = terms_json['terms'];
        
        populate_color_swatch_terms_html(terms, form);
        populate_label_swatch_terms_html(terms, form);
        populate_image_swatch_terms_html(terms, form);

        form.find(".enwbvsfw_attribute_terms_settings").hide();
        form.find("#enwbvsfw_attribute_terms_"+attr_type).show();
    }
    function swatch_type_change_listener(elm){
        var type         = $(elm).val(),
            form         = $('#enwbvsfw_attribute_form');
        form.find(".enwbvsfw_attribute_terms_settings").hide();
        form.find("#enwbvsfw_attribute_terms_"+type).show();
    }

    function populate_color_swatch_terms_html(terms, form){
        var termHtml = '';
        termHtml += '<tr><td class="terms-label" colspan="3">Set Terms Color</td> </tr>';
        jQuery.each(terms,function(key,value){

            termHtml += '<tr>';
            termHtml += '<td class="titledesc" style="width:35%">'+value['term_name']+'</td>';
            termHtml += '<td style="width: 26px; padding:0px;"></td>';
            
            termHtml += '<td class ="forminp inp-color">';
            termHtml += '<input type="text" name="i_single_color_'+value['slug']+'" value="'+value['color']+'" style="width: 260px;" class="enwbvs-admin-colorpick"/>';
            termHtml += '<span class="enwbvsfw-admin-colorpickpreview enwbvs-admin-colorpickpreview '+value['slug']+'_preview"  style="background-color:'+value['color']+'"></span>';
            termHtml += '</td>';
            termHtml += '</tr>'

        });
        var termTable = form.find("#enwbvsfw_attribute_terms_color");
        termTable.html(termHtml ); 
    }

    function populate_image_swatch_terms_html(terms, form){

        var termHtml = '';
        var placeholder_image = enwbvs_var.placeholder_image,
            upload_img        = enwbvs_var.upload_image,
            remove_img        = enwbvs_var.remove_image;

        termHtml += '<tr><td class="terms-label" colspan="3">Set Terms Image</td> </tr>';
        jQuery.each(terms,function(key,value){

            var remove_icon_style = value['image'] ? '' : 'display:none;' ,
             image = value['image'] ? value['image'] : placeholder_image;
          
            termHtml += '<tr>';
            termHtml += '<td class="titledesc" style="width:35%">'+value['term_name']+'</td>';
            termHtml += '<td style="width: 26px; padding:0px;"></td>';
            termHtml += '<td>';
            termHtml += '<div class = "enwbvs-upload-image"> <div class="tawcvs-term-image-thumbnail" style="float:left;margin-right:10px;">';
            termHtml += '<img  class="i_index_media_img" src="'+image+'" width="60px" height="60px" alt="term-image" />';
            termHtml += '</div>';
            termHtml += '<div style="line-height:30px;">';
            termHtml += '<input type="hidden" class="i_index_media"  name= "i_product_image_'+value['slug']+'" value="'+value['term_value']+'">';
            termHtml += '<button type="button" class="enwbvsfw-upload-image-button button " onclick="enwbvs_upload_icon_image(this,event)">';
            termHtml += '<img class="enwbvs-upload-button" src="'+upload_img+'" alt="upload"></button>';                                   
            termHtml += '<button type="button" style ="'+remove_icon_style+'"  class="enwbvs_remove_image_button button " onclick="enwbvs_remove_icon_image(this,event)">';                               
            termHtml += '<img class="enwbvs-remove-button" src="'+remove_img+'" alt="remove"></button>';
            termHtml += '</div>';
            termHtml += '</div>';
            termHtml += '</td>';
            termHtml += '</tr>';

        });
        var termTable = form.find("#enwbvsfw_attribute_terms_image");
        termTable.html(termHtml ); 
    }

    function populate_label_swatch_terms_html(terms, form){
        var termHtml = '';
        termHtml += '<tr><td class="terms-label" colspan="3">Set Terms Label</td> </tr>';
        jQuery.each(terms,function(key,value){
          
            termHtml += '<tr>';
            termHtml += '<td class="titledesc" style="width:35%">'+value['term_name']+'</td>';
            termHtml += '<td style="width: 26px; padding:0px;"></td>';
            termHtml += '<td class ="forminp">';
            termHtml += '<input type="text" name="i_label_'+value['slug']+'" value="'+value['label']+'" style="width: 260px;" />';
            termHtml += '</td>';
            termHtml += '</tr>'

        });
        termHtml += '</div>';
        var termTable = form.find("#enwbvsfw_attribute_terms_label");
        termTable.html(termHtml );
    }

    function edit_design_form(elm, design_styles, design_id, des_title){

        open_design_form('edit', design_styles, design_id, elm, des_title);
    }

    function open_design_form(type, valueJson, design_id, elm, des_title ){

        des_title = $('<div/>').html(des_title).text();

        var container = $('#enwbvsfw_design_form_pp'),
            form = $('#enwbvsfw_design_form');

        populate_design_form(form,type, valueJson, container, des_title );
        form.find("input[name=enwbvs_design_id]").val(design_id);

        open_design_popup(elm, container);
        form.find("input[name=i_design_name]").val(des_title);
    }

    function populate_design_form(form, type, valueJson, container, des_title){        

        var title = (type === 'edit' &&  des_title) ? des_title : 'New Design Style';

        container.find('.pp-title').text(title);
        if(type === 'new'){

            set_form_field_values(form, DESIGN_FORM_FIELDS, false);
        }else{

            set_form_field_values(form, DESIGN_FORM_FIELDS, valueJson);
        }
    }


    function set_form_field_values(form, fields, valuesJson){

        var sname = valuesJson && valuesJson['name'] ? valuesJson['name'] : '';
        
        $.each( fields, function( name, field ) {
            var type     = field['type'],                                 
                value    = valuesJson && valuesJson[name] ? valuesJson[name] : field['value'],
                multiple = field['multiple'] ? field['multiple'] : 0;

            if(type === 'checkbox'){
                if(!valuesJson && field['checked']){
                    value = field['checked'];
                }
            }
            name = name;

            enwbvs_init.set_property_field_value(form, type, name, value, multiple);
        });


        form.find("select[name=i_attr_selection_style]").change();
        form.find("select[name=i_common_selection_style]").change();
        form.find("select[name=i_label_selection_style]").change();
    }

    function open_design_popup(elm, popup){

        //var popup = $("#enwbvsfw_design_form_pp");

        if ($('.popup-wrapper').hasClass('dismiss')) {

           $('.popup-wrapper').removeClass('dismiss').addClass('selected').show();
        }

        $('.enwbvsfw-template-preview-wrapper .enwbvsfw-template-box').removeClass('design-active');

        $('.enwbvsfw-design-templates.enwbvsfw-template-popup').addClass('pop-active');
        $('.product_page_th_product_variation_swatches_for_woocommerce').addClass('enwbvsfw-body-deactive');

        $(elm).closest('.enwbvsfw-template-box').addClass('design-active');
        popup.find('ul.pp_nav_tabs li').first().click();
    }

    function close_design_popup(elm){
        if ($('.popup-wrapper').hasClass('selected')) {
            
            $('.popup-wrapper').removeClass('selected').addClass('dismiss');
        }

        $('.enwbvsfw-design-templates.enwbvsfw-template-popup').removeClass('pop-active');
        $('.product_page_th_product_variation_swatches_for_woocommerce').removeClass('enwbvsfw-body-deactive');
        $('.enwbvsfw-template-preview-wrapper .enwbvsfw-template-box').removeClass('design-active');
    }

    function trigger_attribute_tab(elm){
        $('ul.wc-tabs .attribute_options.attribute_tab a').trigger('click');
    }

    $( document ).on( 'click', '.enwbvs-admin-notice .notice-dismiss', function() {
        var wrapper = $(this).closest('div.enwbvs-admin-notice');
        var nonce   = wrapper.data("nonce");
       
        var data = {
            enwbvs_review_nonce: nonce,
            action: 'dismiss_enwbvs_review_request_notice',
        };
        $.post( ajaxurl, data, function() {

        });
    });

    function show_check_styles(elm){

        var sel_type = $(elm).val(),
            tick_style = $('.tick_prop');

        if(sel_type == 'border_with_tick'){
            tick_style.show();
        }else{
            tick_style.hide();
        }
    }

    function label_selection_syles(elm){
        var sel_lab_type = $(elm).val(),
            lab_back_elm = $('.label_background_prop'),
            lab_tick_elm = $('.label_tick_prop');

        if(sel_lab_type == 'border_with_tick'){
            lab_back_elm.hide();
            lab_tick_elm.show();
        }else if(sel_lab_type == 'background_color'){
            lab_tick_elm.hide();
            lab_back_elm.show();
        }else{
            lab_tick_elm.hide();
            lab_back_elm.hide();
        }
    }

    return{

        upload_icon_image : upload_icon_image, 
        remove_icon_image : remove_icon_image,
        change_term_type  : change_term_type,
        open_term_body    : open_term_body,

        EditDesignForm    :  edit_design_form,
        CloseDesignPopup  : close_design_popup,
        TriggerAttributeTab : trigger_attribute_tab,
        OpenAttributeForm   : open_attribute_form,
        SwatchTypeChangeListner : swatch_type_change_listener,
        show_check_styles     : show_check_styles,
        label_selection_syles : label_selection_syles
    };

}(window.jQuery, window, document));  

function enwbvs_upload_icon_image(elm,e){
    enwbvs_settings.upload_icon_image(elm,e);
}
function enwbvs_remove_icon_image(elm,e){
    enwbvs_settings.remove_icon_image(elm,e);
}
function enwbvs_change_term_type(elm,e){
    enwbvs_settings.change_term_type(elm,e);
}
function enwbvs_open_body(elm,e){
    enwbvs_settings.open_term_body(elm,e);
}
function enwbvsEditDesignForm(elm, design_styles, design_id, des_title){
    enwbvs_settings.EditDesignForm(elm, design_styles, design_id, des_title);
}
function enwbvsCloseDesignPopup(elm){
    enwbvs_settings.CloseDesignPopup(elm);
}

function enwbvsTriggerAttributeTab(elm){
    enwbvs_settings.TriggerAttributeTab(elm);
}
function enwbvsOpenAttributeForm(elm, id, design_type){
    enwbvs_settings.OpenAttributeForm(elm, id, design_type);
}
function enwbvsSwatchTypeChangeListner(elm){
    enwbvs_settings.SwatchTypeChangeListner(elm);
}
function enwbvsShowcheckStyles(elm){
    enwbvs_settings.show_check_styles(elm);
}
function enwbvsShowLabelSelectionStyles(elm){
    enwbvs_settings.label_selection_syles(elm);
}




jQuery( function( $ ) {

	'use strict';

	var _extends = Object.assign || function (target) {
 		for (var i = 1; i < arguments.length; i++) {
  			var source = arguments[i]; for (var key in source) {
	   			if (Object.prototype.hasOwnProperty.call(source, key)) {
	    			target[key] = source[key]; 
	    		} 
	    	} 
    	}
    	 
    	return target; 
    };

	$('.product_attributes').on('click', 'button.enwbvs_add_new_attribute', function (event) {
		event.preventDefault();

		$('.enwbvs-class').val('');
		var placeholder = enwbvs_var.placeholder_image;
		$('.i_index_media_img').attr( 'src',placeholder);
		$('.enwbvs-admin-colorpickpreview').css('background-color','');

		var popup_outer = $('.enwbvs-attribte-dialog');
		popup_outer.find("input[type=text]").val("");

		if(popup_outer.hasClass('enwbvs-attribte-dialog-image')){
			var remove_button = popup_outer.find('.enwbvs_remove_image_button');
			remove_button.hide();
		}

		var $wrapper  = $( this ).closest( '.woocommerce_attribute' ),
			attribute = $wrapper.data( 'taxonomy' ),
			taxonomy = $(this).data('attr_taxonomy'),
			type = ($(this).data('attr_type')),
			settings_div = $('.enwbvs_settings_fields_form');

		enwbvs_init.setupColorPicker(settings_div);
		var $popup_div = $('.enwbvs-attribte-dialog-'+type),
			height = type == 'color' ? 395 : 250;

		if($popup_div.length > 0){
			$popup_div.dialog({ 

		       'dialogClass'   	: 'wp-dialog enwbvs-popup',  
		       'title'         	: 'Add new term',         
		       'modal'         	: true,
		       'autoOpen'      	: false, 
		       'width'       	: 500, 
		       'minHeight'      : height,

		       'buttons': [{
	               text:'save',
	               "class":"button_class",
	               click: function() {
	               		save_new_term($wrapper, $(this), attribute);
	                	$(this).dialog('close');
	                }
	           }]
	 		});
			
			$( '.product_attributes' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
				
			$popup_div.dialog('open');
			$( '.product_attributes' ).unblock();	

			$('.ui-dialog.enwbvs-popup').css('z-index',99999999);
					
		}
	});

	function save_new_term($wrapper, $dialog, attribute){
		
		var new_attribute_name = '';
		var term_spec = {};

		new_attribute_name = $dialog.find('input[name = "attribute_name"]').val();
		term_spec['product_'+attribute] = $dialog.find('input[name = "attribute_type"]').val();
		
		
		if(new_attribute_name){
		    var ajax_data = _extends({
                action: 'woocommerce_add_new_attribute',
                taxonomy: attribute,
                term:new_attribute_name,
                security: woocommerce_admin_meta_boxes.add_attribute_nonce
            },term_spec);

			$.post(woocommerce_admin_meta_boxes.ajax_url, ajax_data, function (response) {
				
			
                if (response.error) {
                    window.alert(response.error);
                } else if (response.slug) {
                    $wrapper.find('select.attribute_values').append('<option value="' + response.term_id + '" selected="selected">' + response.name + '</option>');
                    $wrapper.find('select.attribute_values').change();
                }

                $('.product_attributes').unblock();
                    
			});
		} else {
			$( '.product_attributes' ).unblock();
		}
	}
           

});


(function( $ ) {
	jQuery(function(){
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #styling_section_swatch_indicator_enwbvs-selected-swatch-border-width').after('<span class="afterpx">PX</span>');
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #styling_section_swatch_indicator_enwbvs-swatch-inner-padding').after('<span class="afterpx">PX</span>');
		
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #product_page_section_product_page_swatch_size_enwbvs-product-swatch-width').after('<span class="afterpx">PX</span>');
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #product_page_section_product_page_swatch_size_enwbvs-product-swatch-height').after('<span class="afterpx">PX</span>');
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #product_page_section_product_page_swatch_size_enwbvs-product-swatch-font-size').after('<span class="afterpx">PX</span>');
		
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #shop_archive_section_shop_archive_swatch_size_enwbvs-arhive-swatch-width').after('<span class="afterpx">PX</span>');
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #shop_archive_section_shop_archive_swatch_size_enwbvs-arhive-swatch-height').after('<span class="afterpx">PX</span>');
		jQuery('.toplevel_page_enweby-variation-swatches-for-woocommerce-settings #shop_archive_section_shop_archive_swatch_size_enwbvs-archive-swatch-font-size').after('<span class="afterpx">PX</span>');
		
		// This code being used for color picker in latet version
		jQuery(".enwbvs-admin-colorpick").wpColorPicker();
		jQuery(document).on('click', 'body', function(e) {
			e.stopPropagation();
			jQuery(".iris-picker").hide();
		});
		jQuery(document).on('click', '.woocommerce_attribute h3', function(e) {
			e.stopPropagation();
			jQuery(".handlediv-enwbvs span").toggleClass("arrow-up arrow-down");
		});
	
	
		//Adding pro feature class
		jQuery('.pro-feature').closest('tr').addClass('pro-feature-row');
		jQuery('.pro-feature-row th').prepend("<a class='pro-link' href='#'><span class='locked-icon'></span></a>");
		
		/*jQuery(document).on('click', '.pro-link', function(e) {
			e.preventDefault();
		});*/
		
		jQuery(document).on('mousedown', '.close-upgrade-notice-box', function(e) {
			jQuery(".upgrade-notice-box").remove();
			jQuery(".enwbvs-model-overlay").remove();
		});
		jQuery(document).on('click', '.pro-feature-row th', function(e) {
			jQuery(".upgrade-notice-box").remove();
			jQuery(".enwbvs-model-overlay").remove();
			jQuery("body").prepend("<div class='enwbvs-model-overlay'>&nbsp;</div>");
			jQuery(this).append("<div class='upgrade-notice-box'><span class='close-upgrade-notice-box'><span class='dashicons dashicons-dismiss'></span></span><h3>Premium Feature</h3><p>This is premium feature, not available in light version.</p><p>Upgrade today for all premium features and premium support. Click below button to upgrade.<p class='moneyback-guarentee'><span class='dashicons dashicons-awards'></span>&nbsp;30 days money back guarentee.</p><p class='button-class'><span class='button-left'><a class='upgrade-buttton' href='https://checkout.freemius.com/mode/dialog/plugin/11585/plan/19754/'>Upgrade Now</a></span> <span class='button-right'><a target='_blank' href='https://www.enweby.com/product/variation-swatches-for-woocommerce/'>View All Features</a></span></p></div>");
			
			//setting up model window in middle
	
			var winWidth=jQuery(document).width();
			var winHeight=jQuery(document).height();
			var modelWidth=jQuery(".upgrade-notice-box").width();
			var modelHeight=jQuery(".upgrade-notice-box").width();
			var left = (winWidth - modelWidth) / 2;
            var top = (winHeight - modelHeight) / 4;
			//alert(winWidth+""+modelWidth);
			//var effectiveWidth = parseInt(winWidth)-parseInt(modelWidth);
			//var leftWidth=effectiveWidth/2;
			if(winWidth <='764') {
			jQuery(".upgrade-notice-box").css("top","50px");
			} else {
			jQuery(".upgrade-notice-box").css("top",top+"px");
			}
			jQuery(".upgrade-notice-box").css("left",left+"px")
			
		});
		
		jQuery(document).on('click', '.switch', function(e) {
			e.stopPropagation();
			
			var elm_id = jQuery(this).find("input").attr('id');
			
			if( 'general_section_general_enwbvs-default-dropdown-to-buttons' == elm_id && !jQuery('#'+elm_id).is(':checked'))
			{	
				
				jQuery("#general_section_general_enwbvs-default-dropdown-to-images").closest('switch').trigger('click');
				jQuery("#general_section_general_enwbvs-default-dropdown-to-images").prop('checked',false);
			}
			
			if( 'general_section_general_enwbvs-default-dropdown-to-images' == elm_id && !jQuery('#'+elm_id).is(':checked'))
			{
			
				jQuery("#general_section_general_enwbvs-default-dropdown-to-buttons").closest('switch').trigger('click');
				jQuery("#general_section_general_enwbvs-default-dropdown-to-buttons").prop('checked',false);
			}
			
		});
		
		// Color type dropdown change effect.
		jQuery('.enwbvs_settings_fields_form .enwbvs-admin-color-type').change(function(){						
			if( 'dual' == jQuery(this).val() ) {
				jQuery('.enwbvs_settings_fields_form .product_term_color2').show();
			} else {
				jQuery('.enwbvs_settings_fields_form .product_term_color2').hide();
			}
		});
					
		jQuery(document).on('change', '.enwbvs_settings_fields_form .enwbvsfs-admin-color-type', function(e) {
			if( 'dual' == jQuery(this).val() ) {
				jQuery(this).closest('.enwbvs-local-body-table').find('tr:last-child .enwbvs-admin-colorpick-term-color2').show();
			} else {
				jQuery(this).closest('.enwbvs-local-body-table').find('tr:last-child .enwbvs-admin-colorpick-term-color2').hide();
			}
		});
		
		jQuery(document).on('ajaxComplete', function(e, xhr, settings) {
		//jQuery(document).on('mouseup','#addtag #submit', function(e) {
			var settings_data = settings.data;
			var data_array = settings_data.split('&');
	
			//var searched_data = data_array.find('action=add-tag');
			
			if( 'action=add-tag' == data_array[0] ) {
				jQuery('#addtag .enwbvs_settings_fields_form button.wp-color-result').css('background','transparent');
				jQuery('#addtag .enwbvs_settings_fields_form input.wp-color-picker').val('');
			}
		});
	
	});

})( jQuery );