<label<?php
	echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
	echo $this->data->class ? ' class="' . $this->data->class . '"' : '';
	echo $this->data->for ? ' for="' . $this->data->for . '"' : '';
	echo $this->data->dataset ? $this->data->dataset : '';
	echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
	?>><?php echo $this->data->html; ?></label>
