<div class="switch-row <?php echo $this->data->class; ?>">
	<div class="switch-row-text">
		<label class="title"<?php echo $this->data->id ? ' for="' . $this->data->id . '"' : ''; ?>><?php echo $this->data->html; ?></label>
		<?php if( $this->data->sub ){ ?>
			<div class="sub"><?php echo $this->data->sub; ?></div>
		<?php } ?>
	</div>
	<div class="form-check form-switch m-0">
		<input class="form-check-input" role="switch"<?php
			echo $this->data->id ? ' id="' . $this->data->id . '"' : '';
			echo $this->data->type ? ' type="' . $this->data->type . '"' : '';
			echo $this->data->name ? ' name="' . $this->data->name . '"' : '';
			echo $this->data->value ? ' value="' . $this->data->value . '"' : '';
			echo $this->data->required ? ' required' : '';
			echo $this->data->disabled ? ' disabled' : '';
			echo $this->data->readonly ? ' readonly' : '';
			echo $this->data->checked ? ' checked' : '';
			echo $this->data->dataset ? $this->data->dataset : '';
			echo $this->data->style ? ' style="' . $this->data->style . '"' : '';
			?>>
	</div>
</div>
