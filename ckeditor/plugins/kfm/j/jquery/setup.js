var $j = jQuery.noConflict();
$j.tablesorter.addParser({ 
	id: 'kfmobject', 
	is: function(s) { 
		return false; 
	}, 
	format: function(s) {
		return $j(s).text().toLowerCase();
	}, 
	type: 'text' 
}); 
