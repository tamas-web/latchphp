<textarea class="form-control <?php echo $this->data->class; ?>"<?php
		echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
		echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
		echo $this->data->required ? ' required' : '';
		echo $this->data->disabled ? ' disabled' : '';
		echo $this->data->readonly ? ' readonly' : '';
		echo $this->data->checked ? ' checked' : '';
		echo ! is_null( $this->data->minlength ) ? ' minlength="' . $this->data->minlength . '"' : '';
		echo ! is_null( $this->data->maxlength ) ? ' maxlength="' . $this->data->maxlength . '"' : '';
		echo $this->data->dataset ? $this->data->dataset : '';
		echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
		echo $this->data->rows ? ' rows="' . $this->data->rows . '"' : '';
		?>><?php echo $this->data->value ? $this->data->value : '';?></textarea>
