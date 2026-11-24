<a<?php
	echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
	echo $this->data->class ? ' class="' . $this->data->class . '"' : '';
	echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
	echo $this->data->href ? ' href="' . $this->data->href . '"' : '';
	echo $this->data->dataset ? $this->data->dataset : '';
	echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
	echo $this->data->target ? ' target="' . $this->data->target . '"' : '';
	?>><?php echo $this->data->html; ?></a>
