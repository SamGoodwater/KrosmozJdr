<?php
// Obligatoire
    if(!isset($obj)) {throw new Exception("obj is not set");}else{if(!is_object($obj)) {throw new Exception("obj is not set");}}

// Conseillé
    if(!isset($user)) {$user = ControllerConnect::getCurrentUser();}else{if(get_class($user) != "User") {$user = ControllerConnect::getCurrentUser();}}
    if(!isset($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}else{if(!is_string($bookmark_icon)) {$bookmark_icon =  Style::ICON_REGULAR;}}
    if(!isset($size)){ $size = "300"; }else{ if(!is_numeric($size)){ $size = "300"; } }
?>

<div style="width: <?=$size?>px;">
    <div style="position:relative;">
        <div ondblclick="Mob.open('<?=$obj->getUniqid()?>');" class="card-hover-linked card border-secondary-d-2 border p-2 m-1" style="width: <?=$size?>px;" >
            <div class="d-flex flew-row flex-nowrap justify-content-start">
                <div>
                    <?=$obj->getFile('logo', new Style(['format' => Content::FORMAT_VIEW, "class" => "img-back-50"]))?>
                </div>
                <div class="m-1 p-0">
                    <p class="bold ms-1"><?=$obj->getName()?></p>
                    <div class="d-flex flex-wrap justify-content-around align-items-baseline">
                        <p class="mt-1 text-level short-badge-150"><?=$obj->getLevel(Content::FORMAT_BADGE)?></p> 
                        <div class="mt-1 mx-2"> <?=$obj->getPowerful(Content::FORMAT_ICON)?></div>
                        <p class="mt-1 short-badge-100"><?=$obj->getHostility(Content::FORMAT_BADGE)?></p>
                    </div>
                </div>
                <div class="d-flex flex-column justify-content-between ms-auto">
                    <a onclick='User.changeBookmark(this);' data-classe='mob' data-uniqid='<?=$obj->getUniqid()?>'><i class='<?=$bookmark_icon?> fa-bookmark text-main-d-2 text-main-hover'></i></a>
                    <p class="align-self-end"><a class="btn-text-secondary" title="Afficher les sorts" onclick="Mob.getSpellList('<?=$obj->getUniqid()?>');"><i class="fas fa-magic"></i></a></p>
                </div>
            </div>
            <div class="justify-content-center flex-wrap d-flex short-badge-150"><?=$obj->getTrait(Content::FORMAT_BADGE)?></div>
            <div class="card-hover-showed">
                <div class="d-flex justify-content-around flex-wrap">
                    <div class="col-auto">
                        <div><?=$obj->getPa(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getPm(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getPo(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getIni(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getLife(Content::FORMAT_ICON)?></div>
                        <div><?=$obj->getTouch(Content::FORMAT_ICON)?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getVitality(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getSagesse(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getStrong(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getIntel(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getAgi(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getChance(Content::FORMAT_ICON);?></div>
                    </div>
                    <div class="col-auto">
                        <div><?=$obj->getCa(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getFuite(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getTacle(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getDodge_pa(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getDodge_pm(Content::FORMAT_ICON);?></div>
                    </div> 
                    <div class="col-auto">
                        <div><?=$obj->getRes_neutre(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getRes_terre(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getRes_feu(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getRes_air(Content::FORMAT_ICON);?></div>
                        <div><?=$obj->getRes_eau(Content::FORMAT_ICON);?></div>
                    </div> 
                </div>
                <?php $spells = $obj->getSpell(Content::DISPLAY_LIST); 
                    if(!empty($spells)){ ?>
                        <p class="text-main-d-2 text-bold mt-3 text-center">Sorts</p>
                        <div><?=$spells?></div>
                <?php } ?>
                <?php $aptitudes = $obj->getCapability(Content::DISPLAY_LIST); 
                if(!empty($aptitudes)){ ?>
                    <p class="text-main-d-2 text-bold mt-3 text-center">Aptitudes</p>
                    <div><?=$aptitudes?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>