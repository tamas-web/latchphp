<input class="form-control <?php echo $this->data->class; ?>"<?php
		echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
		echo $this->data->type ? ' type="' . $this->data->type . '"' : '';
		echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
		echo $this->data->value ? ' value="' . $this->data->value . '"' : '';
		echo $this->data->placeholder ? ' placeholder="' . $this->data->placeholder . '"' : '';
		echo $this->data->autocomplete ? ' autocomplete="' . $this->data->autocomplete . '"' : '';
		echo $this->data->required ? ' required' : '';
		echo $this->data->disabled ? ' disabled' : '';
		echo $this->data->readonly ? ' readonly' : '';
		echo $this->data->checked ? ' checked' : '';
		echo $this->data->multiple ? ' multiple' : '';
		if( 'number' === $this->data->type || 'date' === $this->data->type ){
			echo ! is_null( $this->data->min ) ? ' min="' . $this->data->min . '"' : '';
			echo ! is_null( $this->data->max ) ? ' max="' . $this->data->max . '"' : '';
			if( 'number' === $this->data->type ){
				echo ! is_null( $this->data->step ) ? ' step="' . $this->data->step . '"' : '';
			}
		} else {
			echo ! is_null( $this->data->minlength ) ? ' minlength="' . $this->data->minlength . '"' : '';
			echo ! is_null( $this->data->maxlength ) ? ' maxlength="' . $this->data->maxlength . '"' : '';
		}
		echo $this->data->dataset ? $this->data->dataset : '';
		echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
		?>>
