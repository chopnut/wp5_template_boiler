<?php

// ========================================
//     Render dynamically-blocks 
//        Edit contents here
// ========================================
function urbosa_custom($attributes)
{
  global $post;
  ob_start();
  ?>
  <div class="urbosa_custom">
    <?php
    echo 'Hello';
    ?>
  </div>
  <?php
  return ob_get_clean();
}

register_block_type('urbosa-cb/urbosa-custom', array(
  'style' => 'urbosa_custom_blocks_front',
  'editor_style' => 'urbosa_custom_editor_css',
  'editor_script' => 'urbosa_custom_blocks',
  'render_callback' => 'urbosa_custom',
  'supports' => array('align' => ["wide", "full"]),
  'attributes' => array(
    'content' => array(
      'type' => 'string',
      'default' => ''
    ),
    'align' => array(
      'type' => 'string',
      'default' => 'wide'
    ),
    'post' => array(
      'type' => 'object',
      'default' => new stdClass()
    )
  )

));
// ========================================
