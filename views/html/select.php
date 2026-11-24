<select class="<?php echo $this->data->class; ?>"<?php
	echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
	echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
	echo $this->data->placeholder ? ' data-placeholder="' . $this->data->placeholder . '"' : '';
	echo $this->data->required ? ' required' : '';
	echo $this->data->disabled ? ' disabled' : '';
	echo $this->data->readonly ? ' readonly' : '';
	echo $this->data->multiple ? ' multiple' : '';
	echo $this->data->dataset ? $this->data->dataset : '';
	echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
	?>><?php echo $this->data->html; ?></select>
