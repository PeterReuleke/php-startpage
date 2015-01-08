window.addEvent('domready', function() {
	var z = 10;

	// Admin-EventHandler

	var Admin_EventHandler = new Class ({
	    initialize: function (ele) {
	    	var long_id = ele.get('id').split(':');
	    	
	    	if (long_id.length > 1) {
	    		var help_id = long_id[1].split('_');
	    		
	    		this.what = help_id[0];
	        	this.id = help_id[1];
	        	
	        	this.data = 'what=' + this.what + '&id=' + this.id;
	    	}	    	
	    	else
	    	{
	    		this.data = '';
	    	}

	        this.action = long_id[0];
	    },
	    do_Request: function () {	    
	    	var data2 = '',
	    		menu = '',
	    		value = '',
	    		actions = ['insert_box', 'insert_menu', 'update_menu', 'remove_link', 'update_link', 'insert_link', 'update_box', 'update_theme', 'insert_rss', 'update_rss', 'remove_rss'];
	    		
	    		
	    	
			if (this.action == "insert_box" || this.action == "insert_menu" || this.action == "update_menu" || this.action == "remove_link" || 
				this.action == "update_link" || this.action == "insert_link" || this.action == "update_box" || this.action == "update_theme" || this.action == "insert_rss" || this.action == "update_rss" || this.action == "remove_rss") {			
				$$('#admin_main .input').each(function(el) {
					value = string_to_url(el.get('value'));					
					
					data2+= '&' + el.get('id') + '=' + value;
				});
				
				if (this.action == "insert_box" || this.action == "update_box") {
					data2+= '&Menu=';
					
					$$('#admin_main .checkbox').each(function(el) {
						if (el.get('checked') == true) {
							data2+= el.get('value') + ',';
						}
					});
				}

				this.data+= data2;				
			}

			//alert(this.action + ' - ' + this.data + ' - ' + this.data.tidy());

			var AjaxReq = new Request({
				url : '?action=' + this.action,
				method: 'get',
				onSuccess: function(response) {							
					$('admin_main').set('html', response);

					start_admin();	
					start_sortieren();
					
					if ($defined($('set_color'))) {
						farb_tool();			
					}	
				}
			});	

			AjaxReq.send(this.data);
	    }
	});
	
	var start_admin = function () {
		$$('.action_span').removeEvents('click');
		
		$$('.action_span').each(function (el) {
			el.addEvents({
				'click': function() {
					var admin_event = new Admin_EventHandler(el);

					admin_event.do_Request();	
				}
			});
		});
	}
	
	// Farb Tool
	
	var farb_tool = function () {
		var el = $('Farbe'),
			color;	
		
		if ($('Farbe').get('value') != '') {
			color = $('Farbe').get('value').hexToRgb(true);
		} else {
			color = [0,0,0];
		}	
		
		var updateColor = function(){						
			$('Farbe').set('value', color.rgbToHex().substr(1,6));
			
			$('set_color').setStyle('color', color.rgbToHex());
		};
		
		$$('div.slider.advanced').each(function(el, i){			
			var slider = new Slider(el, el.getElement('.knob'), {
				steps: 255, 
				initialStep: color[i],
				wheel: true, 
				onChange: function(){				
					color[i] = this.step;
					updateColor();
				}
			}).set(color[i]);
		});
	}

	//	DragDrop
	
	var start_drag = function () {	
		$$('.box').each(function(drag) {	
			new Drag.Move(drag, {
				droppables: drag,
				handle: drag.getChildren('.box_head'),
				onStart: function(el) {
					z++;
					el.setStyle('z-index', z);
				},
				onComplete: function(el) {
					var fx = new Fx.Morph(el, {duration: 500, wait: false});
				
					var id = el.get('id').split('box');
					id = id[1];
					
					var menu_id = $$('.active').get('id');
					
					var top  = el.getStyle('top');
					var left = el.getStyle('left');
					
					if (top.split('px')[0] < 20 && left.split('px')[0] < 5) {
						top  = 50 + 'px';
						left = 20 + 'px';
						el.setStyle(top);
						el.setStyle(left);
						fx.start({ 'top': top, 'left': left });
					}
					
					if (top.split('px')[0] < 20) {
						top = 50 + 'px';
						el.setStyle(top);
						fx.start({ 'top': top });
					}
					if (left.split('px')[0] < 5) {
						left = 20+ 'px';
						el.setStyle(left);
						fx.start({ 'left': left });
					}
					
					var data = 'id=' + id + '&menu_id=' + menu_id + '&top=' + top + '&left=' + left;
	
					var AjaxReq = new Request({
						url : '?action=box_update',
						method: 'get',
						urlEncoded: true,
						onSuccess: function(response) {
							//alert(response);
						}
					});	
					AjaxReq.send(data);
				}
			});
			
			drag.addEvent('click', function() {
				z++;
				drag.setStyle('z-index', z);			
			});
		});
	}
	
	// Sort
	

var start_sortieren = function () {
//alert('sort');

/*
  sort.getElements('tr').each(function(tr){
    //var color = [step, 82, 87].hsbToRgb();
    tr.setStyles({
      'background-color': color,
      height: Number.random(20, 50)
    });
    step += 35;
  });*/

  //new Sortables(sort);
/*
  new Sortables('#sortierbar tr', {
    clone: true,
    revert: true,
    opacity: 0.7
  });
  */
}



	
	//	Edit Notiz-Box
/*
	var start_notizen = function () {
		var click = true;
	
		$$('.box .notiz_box').each(function(el) {
			el.addEvents({
				'click': function() {
					if (click == true) {
						click = false;
						
						var id = el.get('id').split('box');
						id = id[1];
						
						var text = el.get('html');					
						el.set('html', '');
			
						var notiz_form = new Element('form', {
							'id': 'notiz_form',
							'action': '?action=edit_notizen',
							'method': 'post'
						});
						
						var notiz_id = new Element('input', {
							'type': 'hidden',
							'name': 'id',
							'value': id
						});
			
						var notiz_text = new Element('textarea', {
							'name': 'notiz_text',
						    'html': text,
						    'cols': '24',
						    'rows': '6',
						    'events': {
						        'blur': function(e){
									e.stop();
	
									$('notiz_form').set('send', { onComplete: function(response) { 								
										notiz_text.destroy();
										notiz_id.destroy();
										notiz_form.destroy();
										
										$(el).set('html', response);
									}});
									
									$('notiz_form').send();			            
	
						            click = true;
						        }
						    }
						});
						
						notiz_form.inject(el);
						notiz_id.inject(notiz_form);
						notiz_text.inject(notiz_form);
					}
				}
			});	
		});
	}
*/	
	// Termin-Box

	var start_termin = function() {
		$$('.month').each(function(el) {
			el.addEvent('click', function() {								
				var long_id = el.get('id').split(':'),
					help_id = long_id[1].split('_'),
					id = help_id[0],
					month = help_id[1],
					termin_id = 'termin' + id;
					
				var AjaxReq = new Request({
					url : '?action=get_termin',
					method: 'get',
					onSuccess: function(response) {							
						$(termin_id).set('html', response);
	
						start_termin();					
					}
				});	
	
				AjaxReq.send('id=' + id + '&month=' + month);							
			});
		});
	}

	// Kalender

	var start_kalender = function() {	
		$$('.kalender').each(function(el) {
			el.addEvent('click', function() {									
				var id = el.get('id').split(':'),
					month = id[1];
					
				var AjaxReq = new Request({
					url : '?action=show_calender',
					method: 'get',
					onSuccess: function(response) {	
											
						$('main').set('html', response);
	
						start_kalender();					
					}
				});	
	
				AjaxReq.send('month=' + month);							
			});
		});
	}

	
	// Menu
	
	$$('#menu #navi li').each(function(el) {
		el.addEvents({
			'click': function() {						
				$$('#menu #navi li').each(function(ele){
					if (ele == el) {
						ele.set('class', 'active');
					} else {
						ele.set('class', 'inactive');
					}
				});
				
				if (el.get('id') == 'admin_navi') {
					var AjaxReq = new Request({
						url : '?action=show_admin',
						method: 'get',
						onSuccess: function(response) {
							$('main').set('html', response);
							start_drag();
							//start_notizen();
							start_admin();
							start_debug();
						}
					});	
					
					AjaxReq.send();
				} else {
					var id = el.get('id').split('navi');
					id = id[1];
					
					var data = 'id=' + id;
	
					var AjaxReq = new Request({
						url : '?action=change_menu',
						method: 'get',
						onSuccess: function(response) {					
							$('main').set('html', response);
							start_drag();
							//start_notizen();
							start_termin();
							start_rss();
							start_kalender();
							start_debug();
						}
					});	
					
					AjaxReq.send(data);					
				}
			}			
		});
	});
	
	// Rss
	
	var start_rss = function () {
		$$('#feed_menu span').each(function(el) {
		
			el.addEvent('click', function() {				
				$$('#feed_menu span').each(function(ele){
					if (ele == el) {
						ele.set('class', 'active');
					} else {
						if (ele.getOffsetParent().get('id') == el.getOffsetParent().get('id')) {
							ele.set('class', 'inactive');
						}						
					}
				});
						
				var long_id = el.get('id').split(':'),
					help_id = long_id[1].split('_'),
					id = help_id[0],
					feed = help_id[1];		
		
				var AjaxReq = new Request({
					url : '?action=get_rss',
					method: 'get',
					onSuccess: function(response) {							
						$(id).getElement('#feed_news').set('html', response);				
					}
				});	
	
				AjaxReq.send('feed=' + feed);
			});
		});	
	}
	
	// Debug
	
	var start_debug = function () {
		$$('.debug').each(function(el){
			el.addEvent('dblclick', function(){
				//alert('test');
				el.destroy();
				//ele.setStyle('visibility', 'hidden');
				//el.set('html', '<p>test</p>');
			});
		});
	}
	
	// string_to_url
	
	var string_to_url = function (str) {
		return str.replace(/&/gi, '%26')
				  .replace(/#/gi, '%23')
				  .replace(/\//gi, '%2F')
				  .replace(/\?/gi, '%3F');
	}
	
	// Starten der Funktionen
	
	start_drag();
	//start_notizen();
	start_termin();
	start_kalender();
	start_debug();
	
});