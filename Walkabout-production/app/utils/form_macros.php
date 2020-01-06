<?php
Form::macro('check',function($label, $name,$class="",$checked=false){
return '
  <div class="form-group '.$class.'">
    <div class="col-sm-offset-2 col-sm-10">
      <div class="checkbox">
        <label>
         '.Form::checkbox($name,1,$checked).' '.$label.'
        </label>
      </div>
    </div>
  </div>

';
});

Form::macro('save', function()
{
    return 
    '<div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
		    <button class="btn btn-primary" type="submit">Save</button>
    	</div>
    </div>';
});

Form::macro('modelField',function($params){
	return Form::{$params['ctype']}($params);
});
Form::macro('group',function($label,$field){
		return 
 '<div class="form-group">
    <label >'.$label.'</label>
	'.$field.'
  </div>';
});

Form::macro('btextarea',function($params){
	$label=$params["label"];
	$name=$params["name"];
	$width = isset($params['width'])?$params['width'] : 12;
	unset($params['label']);
	unset($params['name']);
	unset($params['width']);
	unset($params['ctype']);
	$otherParams=array();
	foreach ($params as $key => $param) {
		$otherParams[]=$key.'='.'"'.$param.'"';
	}
	return 

  '<div class="form-group col-md-'.$width.'">
    <label for="'.$name.'" class="col-sm-2 control-label">'.$label.'</label>
    <div class="col-sm-10">
      <textarea '.implode(' ',$otherParams).' class="form-control" name="'.$name.'"" id="'.$name.'"></textarea>
    </div>
  </div>';
});

Form::macro('btext', function($params)
{
	$label=$params["label"];
	$name=$params["name"];
	$width = isset($params['width'])?$params['width'] : 12;
	unset($params['label']);
	unset($params['name']);
	unset($params['width']);
	unset($params['ctype']);

	$otherParams=array();
	foreach ($params as $key => $param) {
		$otherParams[]=$key.'='.'"'.$param.'"';
	}
	return 

  '<div class="form-group col-md-'.$width.'">
    <label for="'.$name.'" class="col-sm-2 control-label">'.$label.'</label>
    <div class="col-sm-10">
      <input type="text" '.implode(' ',$otherParams).' class="form-control" name="'.$name.'" id="'.$name.'" placeholder="'.$label.'">
    </div>
  </div>';

});