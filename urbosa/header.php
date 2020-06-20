<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="icon" href="<?= get_template_directory_uri() . '/assets/img/favicon.ico' ?>">
  <?php wp_head(); ?>

</head>
<body <?php body_class('page-' . $post->post_name); ?>>
