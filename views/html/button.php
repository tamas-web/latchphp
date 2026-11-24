<button<?php
	echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
	echo $this->data->class ? ' class="' . $this->data->class . '"' : '';
	echo $this->data->type ? ' type="' . $this->data->type . '"' : '';
	echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
	echo $this->data->value ? ' value="' . $this->data->value . '"' : '';
	echo $this->data->dataset ? $this->data->dataset : '';
	echo $this->data->disabled ? ' disabled' : '';
	echo $this->data->readonly ? ' readonly' : '';
	echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
	?>>
<?php
	if( $this->data->icon ){
		echo '<i class="', $this->data->icon, $this->data->html ? ' me-2' : '' , '"></i> ';
	} // endif icon
echo $this->data->html;
?></button>
