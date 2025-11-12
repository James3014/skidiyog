<?php

function renderLeftnav($config = array())
{
    $displayName = !empty($config['display_name']) ? $config['display_name'] : '';
    $slug = !empty($config['slug']) ? $config['slug'] : '';
    $sections = !empty($config['sections']) && is_array($config['sections']) ? $config['sections'] : array();
    ?>
    <div class="col l3 hide-on-med-and-down leftnav">
      <div class="leftnav-brand">
        <img src="https://diy.ski/assets/images/logo-skidiy.png" alt="SKIDIY">
        <span>SKIDIY</span>
      </div>
      <?php if($displayName){ ?>
        <p class="resort-name"><?=$displayName?> <span><?=($slug && $slug!=='iski') ? ucfirst($slug) : ''?></span></p>
      <?php } ?>
      <?php if(!empty($sections)){ ?>
        <ul class="tabs tabs-transparent">
          <?php foreach($sections as $section){
              if(empty($section['key']) || empty($section['title'])) continue;
          ?>
            <a href="#<?=$section['key']?>" class="tab"><li><?=$section['title']?></li></a>
          <?php } ?>
        </ul>
      <?php } ?>
    </div>
    <?php
}
