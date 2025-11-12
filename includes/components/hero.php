<?php

function renderHero($config = array())
{
    $classes = 'site-hero';
    if (!empty($config['modifier'])) {
        $classes .= ' ' . $config['modifier'];
    }
    $background = !empty($config['image']) ? "style=\"--hero-image:url('{$config['image']}');\"" : '';
    $pill = !empty($config['pill']) ? $config['pill'] : '';
    $title = !empty($config['title']) ? $config['title'] : '';
    $subtitle = !empty($config['subtitle']) ? $config['subtitle'] : '';
    $titleSuffix = !empty($config['title_suffix']) ? $config['title_suffix'] : '';
    $cta = isset($config['cta']) ? $config['cta'] : null;
    ?>
    <div class="<?=$classes?>" <?=$background?>>
      <div class="site-hero__overlay"></div>
      <div class="site-hero__content">
        <?php if($pill){ ?><span class="hero-pill"><?=$pill?></span><?php } ?>
        <?php if($title){ ?>
          <h1 class="hero-title"><?=$title?><?php if($titleSuffix){ ?><span><?=$titleSuffix?></span><?php } ?></h1>
        <?php } ?>
        <?php if($subtitle){ ?><p class="hero-subtitle"><?=$subtitle?></p><?php } ?>
        <?php if(is_array($cta) && !empty($cta['label'])){ ?>
          <button class="btn waves-effect waves-light btn-primary space-top-2"
                  type="button"
                  id="<?=!empty($cta['id']) ? $cta['id'] : ''?>"
                  name="<?=!empty($cta['name']) ? $cta['name'] : ''?>"
                  data-target-url="<?=!empty($cta['target']) ? htmlspecialchars($cta['target'], ENT_QUOTES) : ''?>">
            <?=$cta['label']?> <i class="material-icons">arrow_forward</i>
          </button>
        <?php } ?>
      </div>
    </div>
    <?php
}
