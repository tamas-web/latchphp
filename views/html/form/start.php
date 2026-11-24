<form class="dynamic-form <?php echo $this->data->class; ?>"<?php echo $this->data->id ? ' id="' . $this->data->id . '-form"' : ''; ?> action="<?php echo $this->data->action; ?>" method="<?php echo $this->data->method; ?>" enctype="multipart/form-data" spellcheck="false">
<?php echo $this->data->html; ?>
