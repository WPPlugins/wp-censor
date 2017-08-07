function v43v (data){
	this.data = jQuery.extend(v43v_data,data);
};

v43v.prototype.log = function(value) {
	if(this.data.dodebug==1)
	{
		console.log(value);
	}
}
v43v.prototype.json = function(url,data,success) {
	jQuery.ajax({
			type:'POST',
			url:url,
			data:data,
			success:success,
			dataType:'json'
	});
}
v43v.prototype.void = function(value,def_value) {
	var retval =  '';
	if(typeof(def_value) == 'undefined')
	{
		retval =  typeof(value) == 'undefined';
	}
	else
	{
		if(typeof(value) == 'undefined')
		{
			retval = def_value;
		}
		else
		{
			retval = value;
		}
	}
	return retval;
}
v43v.prototype.entitydecode = function(encodedStr) {
	return jQuery("<div/>").html(encodedStr).text();
}