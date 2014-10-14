<?php

function openlayers_openlayers__layer__image_openlayers_layer() {
  return array(
    'handler' => array(
      'class' => 'openlayers__layer__image',
      'file' => 'openlayers__layer__image.inc',
    )
  );
}

class openlayers__layer__image extends openlayers_layer {

  function options_form(&$form, &$form_state) {
      $form['options']['file'] = array(
        '#type' => 'managed_file',
        '#title' => t('Image'),
        '#default_value' => $this->getOption('file'),
        '#upload_location' =>  'public://',
      );
      $form['options']['image_style'] = array(
        '#type' => 'select',
        '#options' => array(''=> 'None (original image)') + image_style_options(FALSE),
        '#default_value' => $this->getOption('image_style'),
        '#description' => t('Apply an image style.'),
      );
      $form['options']['factors'] = array(
        '#tree' => TRUE,
        'x' => array(
          '#title' => 'Width divider',
          '#type' => 'textfield',
          // Todo: Find a way to replace this properly with getOption() method.
          '#default_value' => isset($this->options['factors']['x']) ? $this->options['factors']['x'] : 1,
          '#description' => t('Width divider.'),
        ),
        'y' => array(
          '#title' => 'Height divider',
          '#type' => 'textfield',
          '#default_value' => isset($this->options['factors']['y']) ? $this->options['factors']['y'] : 1,
          '#description' => t('Height divider.'),
        ),
      );
  }

  function options_form_validate($form, &$form_state) {
    if ($file = file_load($form_state['options']['file'])) {
      // Do something with the file if needed.
    } else {

      //form_set_error($form_state['item']['options'], 'Cannot access the file.');
    }
  }

  /**
   * hook_submit() of the form.
   */
  function options_form_submit($form, &$form_state) {
    global $user;

    $item = $form_state['item'];
    if ($item->getOption('file') && $file = file_load($item->getOption('file'))) {
      file_delete($file);
    }

    if (isset($form_state['values']['options']['file']) && $file = file_load($form_state['values']['options']['file'])) {
      $file->status = FILE_STATUS_PERMANENT;
      file_save($file);
      file_usage_add($file, 'openlayers', 'openlayers_layer_type', $user->uid);
    }

    $style = $form_state['values']['options']['image_style'];

    if (!empty($style)) {
      $uri = image_style_path($style, $file->uri);
      image_style_create_derivative(image_style_load($style), $file->uri, $uri);
    } else {
      $uri = $file->uri;
    }
  }

  /*
   * What to do when we delete the layer: delete the file.
   */
  function delete($item) {
    // todo
  }

}


