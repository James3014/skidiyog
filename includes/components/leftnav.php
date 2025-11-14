<?php

function renderLeftnav($config = array())
{
    $displayName = !empty($config['display_name']) ? $config['display_name'] : '';
    $slug = !empty($config['slug']) ? $config['slug'] : '';
    $sections = !empty($config['sections']) && is_array($config['sections']) ? $config['sections'] : array();
    ?>
    <div class="col l3 leftnav leftnav--desktop hide-on-med-and-down">
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
    <!-- Tablet navigation (medium screens) - Hide sidebar, show mobile nav instead -->
    <div class="col s12 leftnav-mobile hide-on-small-only hide-on-large-only">
      <div class="leftnav-mobile__scroll">
        <?php if(!empty($sections)){ foreach($sections as $section){
          if(empty($section['key']) || empty($section['title'])) continue;
        ?>
          <a class="leftnav-mobile__chip" href="#<?=$section['key']?>"><?=$section['title']?></a>
        <?php }} ?>
      </div>
    </div>
    <div class="col s12 leftnav-mobile hide-on-large-only">
      <div class="leftnav-mobile__scroll">
        <?php if(!empty($sections)){ foreach($sections as $section){
          if(empty($section['key']) || empty($section['title'])) continue;
        ?>
          <a class="leftnav-mobile__chip" href="#<?=$section['key']?>"><?=$section['title']?></a>
        <?php }} ?>
      </div>
    </div>
    <?php
}
