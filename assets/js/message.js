$_(".message .close").event.add("click", function(){
	$_(this.parentNode).dom.hide();
});