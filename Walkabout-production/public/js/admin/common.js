jQuery(document).ready(function($) {
	Walkabout.admin.initialize();
});

if (typeof Walkabout === "undefined") Walkabout = {};

Walkabout.admin = {};

Walkabout.fillSelect=function(el,options){
	var html=[],
	data=options.data;
   for(var i=0; i<data.length; i++){
    	html.push('<option value='+data[i][options.id_field]+'>'+data[i][options.description_field]+'</option>');
    }
	el.html(html.join(""));
};

Walkabout.admin.initialize = function() {
	var self = Walkabout.admin;
	var activeMn = activeMn || null;
	if (activeMn) $('#mn' + activeMn).addClass("active");
	self.binUIEventHandlers();
};

Walkabout.admin.binUIEventHandlers = function() {
	$('body')
		.on('click', '.cancel_but', function(ev) {
			ev.preventDefault();
			var href = $(this).data('href');
			window.location = href;

			if (window.parent && window.parent.closeModal) {
				window.parent.closeModal();
			}
		})

		.on('click', '#submit_but', function(ev) {
			ev.preventDefault();
			$('form').submit();

			if (window.parent && window.parent.closeModal) {
				window.parent.closeModal();
			}
		})

		.on('click', '.mnItem', function(ev) {
			ev.preventDefault();
			var href = $(this).find('a').attr('href');
			window.location = href;
		});
};