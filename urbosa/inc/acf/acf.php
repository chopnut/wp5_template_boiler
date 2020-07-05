<?php if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
  'key' => 'group_5f01059657403',
  'title' => 'Object',
  'fields' => 
  array (
    0 => 
    array (
      'key' => 'field_5f0105bfb56e1',
      'label' => 'Gateway Image',
      'name' => 'gateway_image',
      'type' => 'image',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'return_format' => 'array',
      'preview_size' => 'thumbnail',
      'library' => 'all',
      'min_width' => '',
      'min_height' => '',
      'min_size' => '',
      'max_width' => '',
      'max_height' => '',
      'max_size' => '',
      'mime_types' => '',
    ),
  ),
  'location' => 
  array (
    0 => 
    array (
      0 => 
      array (
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'post',
      ),
    ),
    1 => 
    array (
      0 => 
      array (
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'page',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'side',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
));

acf_add_local_field_group(array (
  'key' => 'group_5f010daf8efcb',
  'title' => 'Options',
  'fields' => 
  array (
    0 => 
    array (
      'key' => 'field_5f010dd608e1b',
      'label' => 'Theme',
      'name' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'left',
      'endpoint' => 0,
    ),
    1 => 
    array (
      'key' => 'field_5f010dfe08e1c',
      'label' => 'Logo',
      'name' => 'logo',
      'type' => 'image',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'return_format' => 'url',
      'preview_size' => 'medium',
      'library' => 'all',
      'min_width' => '',
      'min_height' => '',
      'min_size' => '',
      'max_width' => '',
      'max_height' => '',
      'max_size' => '',
      'mime_types' => '',
    ),
    2 => 
    array (
      'key' => 'field_5f010e3d08e1d',
      'label' => 'Favicon',
      'name' => 'favicon',
      'type' => 'image',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'return_format' => 'url',
      'preview_size' => 'medium',
      'library' => 'all',
      'min_width' => '',
      'min_height' => '',
      'min_size' => '',
      'max_width' => 100,
      'max_height' => 100,
      'max_size' => '',
      'mime_types' => '',
    ),
    3 => 
    array (
      'key' => 'field_5f010e8cf3fbd',
      'label' => 'Socials',
      'name' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
    4 => 
    array (
      'key' => 'field_5f010ebbf3fbf',
      'label' => 'Entry',
      'name' => 'socials',
      'type' => 'repeater',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'collapsed' => '',
      'min' => 0,
      'max' => 0,
      'layout' => 'table',
      'button_label' => 'Add Social',
      'sub_fields' => 
      array (
        0 => 
        array (
          'key' => 'field_5f010fcf7cae7',
          'label' => 'Title',
          'name' => 'social_title',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'maxlength' => '',
        ),
        1 => 
        array (
          'key' => 'field_5f010fd97cae8',
          'label' => 'Link',
          'name' => 'social_link',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'maxlength' => '',
        ),
        2 => 
        array (
          'key' => 'field_5f010fec7cae9',
          'label' => 'Icon',
          'name' => 'icon',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'url',
          'preview_size' => 'thumbnail',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
        ),
      ),
    ),
    5 => 
    array (
      'key' => 'field_5f010eedeee11',
      'label' => 'Settings',
      'name' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
  ),
  'location' => 
  array (
    0 => 
    array (
      0 => 
      array (
        'param' => 'options_page',
        'operator' => '==',
        'value' => 'urbosa_theme_option',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
));

acf_add_local_field_group(array (
  'key' => 'group_5f00e2e4a1338',
  'title' => 'Post - Slider',
  'fields' => 
  array (
    0 => 
    array (
      'key' => 'field_5f00e2efc7647',
      'label' => 'Main',
      'name' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
    1 => 
    array (
      'key' => 'field_5f00e328c7649',
      'label' => 'Sliders',
      'name' => 'sliders',
      'type' => 'repeater',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'collapsed' => '',
      'min' => 0,
      'max' => 0,
      'layout' => 'block',
      'button_label' => 'Add Slider',
      'sub_fields' => 
      array (
        0 => 
        array (
          'key' => 'field_5f00f7c293079',
          'label' => 'Background Type',
          'name' => 'background_type',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => 
          array (
            'image' => 'Image',
            'video' => 'Video',
          ),
          'default_value' => 
          array (
            0 => 'image',
          ),
          'allow_null' => 0,
          'multiple' => 0,
          'ui' => 0,
          'return_format' => 'value',
          'ajax' => 0,
          'placeholder' => '',
        ),
        1 => 
        array (
          'key' => 'field_5f00e3a1c764b',
          'label' => 'Image',
          'name' => 'image',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 
          array (
            0 => 
            array (
              0 => 
              array (
                'field' => 'field_5f00f7c293079',
                'operator' => '==',
                'value' => 'image',
              ),
            ),
          ),
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'array',
          'preview_size' => 'thumbnail',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => '',
          'max_height' => '',
          'max_size' => '',
          'mime_types' => '',
        ),
        2 => 
        array (
          'key' => 'field_5f00f78993078',
          'label' => 'Video File',
          'name' => 'video_file',
          'type' => 'group',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 
          array (
            0 => 
            array (
              0 => 
              array (
                'field' => 'field_5f00f7c293079',
                'operator' => '==',
                'value' => 'video',
              ),
            ),
          ),
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => 
            array (
              'key' => 'field_5f0103df021d4',
              'label' => 'Desktop',
              'name' => 'video_file_desktop',
              'type' => 'file',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => 
              array (
                'width' => '50',
                'class' => '',
                'id' => '',
              ),
              'return_format' => 'array',
              'library' => 'all',
              'min_size' => '',
              'max_size' => '',
              'mime_types' => '',
            ),
            1 => 
            array (
              'key' => 'field_5f010415021d5',
              'label' => 'Mobile',
              'name' => 'video_file_mobile',
              'type' => 'file',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => 
              array (
                'width' => '50',
                'class' => '',
                'id' => '',
              ),
              'return_format' => 'array',
              'library' => 'all',
              'min_size' => '',
              'max_size' => '',
              'mime_types' => '',
            ),
          ),
        ),
        3 => 
        array (
          'key' => 'field_5f01044f021d6',
          'label' => 'Video Embed',
          'name' => 'video_embed',
          'type' => 'group',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 
          array (
            0 => 
            array (
              0 => 
              array (
                'field' => 'field_5f00f7c293079',
                'operator' => '==',
                'value' => 'video',
              ),
            ),
          ),
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'layout' => 'block',
          'sub_fields' => 
          array (
            0 => 
            array (
              'key' => 'field_5f01044f021d7',
              'label' => 'Desktop',
              'name' => 'video_embed_desktop',
              'type' => 'textarea',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => 
              array (
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'placeholder' => '',
              'maxlength' => '',
              'rows' => '',
              'new_lines' => '',
            ),
            1 => 
            array (
              'key' => 'field_5f01044f021d8',
              'label' => 'Mobile',
              'name' => 'video_embed_mobile',
              'type' => 'textarea',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => 
              array (
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'placeholder' => '',
              'maxlength' => '',
              'rows' => '',
              'new_lines' => '',
            ),
          ),
        ),
        4 => 
        array (
          'key' => 'field_5f00e3c1c764c',
          'label' => 'Content',
          'name' => 'content',
          'type' => 'wysiwyg',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'tabs' => 'all',
          'toolbar' => 'full',
          'media_upload' => 1,
          'delay' => 0,
        ),
        5 => 
        array (
          'key' => 'field_5f00e402c764d',
          'label' => 'Content Position',
          'name' => 'content_position',
          'type' => 'select',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'choices' => 
          array (
            'topleft' => 'Top Left',
            'topcenter' => 'Top Center',
            'topright' => 'Top Right',
            'left' => 'Left',
            'center' => 'Center',
            'right' => 'Right',
            'bottomleft' => 'Bottom Left',
            'bottomcenter' => 'Bottom Center',
            'bottomright' => 'Bottom Right',
          ),
          'default_value' => 
          array (
            0 => 'center',
          ),
          'allow_null' => 0,
          'multiple' => 0,
          'ui' => 0,
          'return_format' => 'value',
          'ajax' => 0,
          'placeholder' => '',
        ),
      ),
    ),
    2 => 
    array (
      'key' => 'field_5f00e2ffc7648',
      'label' => 'Style',
      'name' => '',
      'type' => 'tab',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'placement' => 'top',
      'endpoint' => 0,
    ),
    3 => 
    array (
      'key' => 'field_5f00ed87a75ad',
      'label' => 'Slider Properties',
      'name' => 'slider_properties',
      'type' => 'group',
      'instructions' => '',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'layout' => 'block',
      'sub_fields' => 
      array (
        0 => 
        array (
          'key' => 'field_5f00ee6ea75ae',
          'label' => 'Show Arrows',
          'name' => 'show_arrows',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '33',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 0,
          'ui' => 1,
          'ui_on_text' => '',
          'ui_off_text' => '',
        ),
        1 => 
        array (
          'key' => 'field_5f00f3fdb9716',
          'label' => 'Arrow Icon',
          'name' => 'arrow_icon',
          'type' => 'image',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 
          array (
            0 => 
            array (
              0 => 
              array (
                'field' => 'field_5f00ee6ea75ae',
                'operator' => '==',
                'value' => '1',
              ),
            ),
          ),
          'wrapper' => 
          array (
            'width' => '66',
            'class' => '',
            'id' => '',
          ),
          'return_format' => 'url',
          'preview_size' => 'thumbnail',
          'library' => 'all',
          'min_width' => '',
          'min_height' => '',
          'min_size' => '',
          'max_width' => 100,
          'max_height' => 100,
          'max_size' => '',
          'mime_types' => '',
        ),
        2 => 
        array (
          'key' => 'field_5f00ee9ea75b0',
          'label' => 'Show Navigation',
          'name' => 'show_navigation',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '33',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 1,
          'ui' => 1,
          'ui_on_text' => '',
          'ui_off_text' => '',
        ),
        3 => 
        array (
          'key' => 'field_5f00ef6031b57',
          'label' => 'Auto Play',
          'name' => 'auto_play',
          'type' => 'true_false',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => 
          array (
            'width' => '33',
            'class' => '',
            'id' => '',
          ),
          'message' => '',
          'default_value' => 1,
          'ui' => 1,
          'ui_on_text' => '',
          'ui_off_text' => '',
        ),
        4 => 
        array (
          'key' => 'field_5f00efaea0e62',
          'label' => 'Auto Play Speed',
          'name' => 'auto_play_speed',
          'type' => 'text',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 
          array (
            0 => 
            array (
              0 => 
              array (
                'field' => 'field_5f00ef6031b57',
                'operator' => '==',
                'value' => '1',
              ),
            ),
          ),
          'wrapper' => 
          array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => 5000,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'maxlength' => '',
        ),
      ),
    ),
    4 => 
    array (
      'key' => 'field_5f00e4cce68b1',
      'label' => 'Feature',
      'name' => 'feature',
      'type' => 'checkbox',
      'instructions' => 'For image only.',
      'required' => 0,
      'conditional_logic' => 0,
      'wrapper' => 
      array (
        'width' => '',
        'class' => '',
        'id' => '',
      ),
      'choices' => 
      array (
        'progressive' => 'Progressive',
        'parallax' => 'Parallax',
        'lazyload' => 'Lazy Load',
        'preload' => 'Pre-load',
        'lightbox' => 'Light Box',
      ),
      'allow_custom' => 0,
      'default_value' => 
      array (
      ),
      'layout' => 'vertical',
      'toggle' => 0,
      'return_format' => 'value',
      'save_custom' => 0,
    ),
  ),
  'location' => 
  array (
    0 => 
    array (
      0 => 
      array (
        'param' => 'post_type',
        'operator' => '==',
        'value' => 'theme_slider',
      ),
    ),
  ),
  'menu_order' => 0,
  'position' => 'normal',
  'style' => 'default',
  'label_placement' => 'top',
  'instruction_placement' => 'label',
  'hide_on_screen' => '',
  'active' => true,
  'description' => '',
));

endif; ?>