/**
* Alert Shake is a jQuery validation plugin that shakes pieces of a form that did not validate
* similar to Apple's MacBook login screen.
* \version 1.0
* \author Anna Drazich
* \copyright (c) 2012 Anna Drazich
* Dual licensed under the MIT and GPL licenses.
* MIT License: https://github.com/adrazich/alert-shake-jquery/blob/master/MIT-License.txt
* GPL License: https://github.com/adrazich/alert-shake-jquery/blob/master/GPL-License.txt
* Website: http://www.initanna.com/alert-shake/
* Github: https://github.com/adrazich/alert-shake-jquery
*/

;(function($, window, document, undefined){
  var settings = _defaults = null;
  var object = [];
  
  // Available methods user can call
  var methods = {
    
    init: function(options){
	  settings = _defaults = $.extend({
		easing:'linear',
		number:8,
		speed:10,
		amount:10,
		selector:this.selector
	  }, options);
	  
	  if (settings.number <= 0) settings.number = 1;
	  
	  $(this.selector).css('position', 'relative');
	  
	  object.push({ o:this.selector, position:$(this.selector).css('left') });
	  methods.reset(this.selector);
    },
	
	reset: function(o){
	  for (var i = 0; i < object.length; i++){
		if (object[i].o == o){
		  object[i].direction = '-=';
		  object[i].animating = false;
		  object[i].execute = false;
		  object[i].interval = 0;
		  break;
		}
	  }
	},
	
	start: function(){
	  methods.reset(this.selector);
	  
	  for (var i = 0; i < object.length; i++){
		if (object[i].o == this.selector){
		  object[i].execute = true;
		  break;
		}
	  }
	},
	
	// reset and set the object back to its original location
	stop: function(){
	  methods.reset(this.selector);
	  
	  for (var i = 0; i < object.length; i++){
		if (object[i].o == this.selector){
		  $(this.selector).animate({marginLeft:object[i].position+'px'}, settings.speed, settings.easing);
		  break;
		}
	  }
	}
	
  };
  
  function animate(o){
	for (var i = 0; i < object.length; i++){
	  if (object[i].o == o){
		// if we're not animating, lets animate
		if (!object[i].animating){
		  animating = true;
		  $(o).animate({left:object[i].direction+settings.amount+'px'}, settings.speed, settings.easing, function(){
			object[i].direction = object[i].direction == '-=' ? '+=' : '-=';
			object[i].animating = false;
			object[i].interval++;
			
			// last animate, lets set it back to normal
			if (object[i].interval >= settings.number){
			  $(o).animate({left:object[i].position+'px'}, settings.speed, settings.easing, function(){
				// Done!
				object[i].execute = false;
			  });
			}
		  });
		}
		break;
	  }
	}
  }
  
  function update(o){
	// go go go
	animate(o);
  }
  
  // if we're executing the effect update
  setInterval(function(){
	$.grep(object, function(i, n){
	  if (i.execute)
		update(i.o);
	});
  }, 50);
  
  // Are they trying to call a method or initialize?
  $.fn.alertShake= function(method){
    if (methods[method])
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    else if (typeof method === 'object' || !method)
      return methods.init.apply(this, arguments);
    else
      $.error('Method '+method+' does not exist on jQuery.alertShake.');
    
    return false;
  };
  
})(jQuery, window, document);