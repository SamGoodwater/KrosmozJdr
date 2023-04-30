<?php
/* 
__________________________________________________________________________
-------------------------------- SECTION ---------------------------------
    Nom : Capability
    Version 1
    
    Une section est une partie d'une page.
    $titre : Aide à l'utilisateur pour choisir quel section utilisé
    $description : Aide à l'utilisateur pour choisir quel section utilisé
    $content : contenu affiché dans la page. La variable $this->getContent(), fait référence à l'enregistrement des options dans la base de donnée.
    $option : input permettant d'envoyer des données lors de la création de la section. L'input name doit être "option"
    $editable : booléen permettant de savoir si la section est éditable ou non.
    
    $template_vars est la valeur contenant les options de la section. Elle peut être utilisé dans la variable $content.
    Elle se décompose : 
    $template_vars['content'] : contenu de la section
    $template_vars['uniqid'] : identifiant de la section
*/
if(!isset($template_vars) || !is_array($template_vars)){$template_vars = array();}
if(!isset($template_vars['content'])){ $template_vars['content'] = "";}
if(!isset($template_vars['uniqid'])){ $template_vars['uniqid'] = "";}
if(!isset($template_vars['uniqid_page'])){ $template_vars['uniqid_page'] = "";}
if(!isset($template_vars['get'])){ $template_vars['get'] = Section::GET_SECTION_DESCRIPTION;}

    $template = array(
        "title" => "Aptitudes",
        "description" => "Section de gestion des aptitudes",
        "content" => "",
        "option" => "",
        "editable" => false,
        "editOnDblClick" => false
    );
    
if($template_vars['get'] == Section::GET_SECTION_CONTENT){

    ob_start(); ?>
        <div class="d-flex flex-row justify-content-between align-items-end" id='sortableItems'>
            <button type="button" class="me-2 btn-sm btn btn-back-secondary" onclick="Page.build(true, 'Création d\'une aptitude', $('#addCapability'), Page.SIZE_MD, true);">Ajouter une aptitude</button>
            <div id="selectorCategoryListCheckbox" class="dropdown">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Catégories des aptitudes</a>
                <ul class="dropdown-menu p-3" aria-labelledby="typesort">
                    <?php $checked = "";
                    foreach (Capability::CATEGORY as $key => $category_) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input selectorCategory" type="checkbox" id="CheckboxCategory<?=$category_?>" value="<?=$category_?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($category_)?>-d-2 text-white" for="CheckboxCategory<?=$category_?>"><?=ucfirst($key)?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div id="selectorElementListCheckbox" class="dropdown mx-2">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Elements des aptitudes</a>
                <ul class="dropdown-menu p-3" aria-labelledby="typesort">
                    <?php $checked = "";
                    foreach (Spell::ELEMENT as $id_element => $element_) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input selectorElement" type="checkbox" id="CheckboxElement<?=$id_element?>" value="<?=$id_element?>">
                                <label class="form-check-label badge back-<?=$element_['color']?> text-white" for="CheckboxElement<?=$id_element?>"><?=ucfirst($element_['name'])?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            
            <div id="selectorLevelListCheckbox" class="dropdown mx-2">
                <a class="btn btn-sm btn-border-secondary dropdown-toggle" type="button" id="levelsort" data-bs-toggle="dropdown" aria-expanded="false">Affichage par niveau</a>
                <ul class="dropdown-menu p-4" aria-labelledby="levelsort">
                    <?php $checked = "";
                    for ($i=1; $i <= 20 ; $i++) { ?>
                        <li>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input selectorLevel" type="checkbox" id="CheckboxLevel<?=$i?>" value="<?=$i?>">
                                <label class="form-check-label badge back-<?=Style::getColorFromLetter($i, true)?>-d-3" for="CheckboxLevel<?=$i?>"><?=$i?></label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input back-main-d-1 border-main-d-1" type="checkbox" role="switch" id="toggleUsableSwitch" checked>
                <label class="form-check-label" for="toggleUsableSwitch">Afficher seulement les aptitudes compatibles avec le JDR</label>
            </div>
        </div>
        <table 
            id="table"
            class="table table-striped"
            data-bs-toggle="table" 
            data-virtual-scroll="true"
            data-classes="table-no-bordered" 
            data-remember-order='true'
            data-locale="fr-FR"
            data-show-export="true" 
            data-show-refresh="true"
            data-show-fullscreen="true"
            data-show-columns="true"
            data-show-multi-sort="true"
            data-pagination="true" 
            data-page-size="30" 
            data-page-list="[10, 25, 50, 100, 200, All]"
            data-search="true"
            data-search-accent-neutralise="true"
            data-search-align="left"
            data-search-highlight="true"
            data-search-on-enter-key="true"
            data-advanced-search="true"
            data-filter-control="true"
            data-sort-empty-last="true"
            data-sort-reset="true"
            data-toolbar="#toolbar"
            data-show-toggle="true"
            data-detail-view="true"
            data-detail-view-icon="false"
            data-detail-view-by-click="true"
            data-resizable="true"
            data-detail-formatter="detailFormatter"
            >
            
            <thead>
                <tr>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="edit"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="pdf"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="id">ID</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="uniqid"></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="bookmark"></th>
                    <th data-sortable="false" data-visible="true" data-field="path_img"><i class="fas fa-image"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="name">Nom</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="resume"></th>
                    <th class="text-center" data-sortable="true" data-visible="false" data-field="is_magic"><i class="fas fa-fist-raised text-brown-d-2"></i> | <i class="fas fa-magic text-purple-d-2"></i></th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="category">Catégorie</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="element">Elèment</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="powerful">Puissance</th>
                    <th class="text-center" data-sortable="true" data-visible="false"  data-field="type">Type</th>
                    <th class="text-center" data-sortable="true" data-visible="true"  data-field="level"><span class="text-level">Niveau</span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="time_before_use_again"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Durée avant réutilisation"><img class='icon-sm' src='medias/icons/time_before_use_again.svg'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Portée de l'aptitude"><img class='icon' src='medias/icons/po.png'></span></th>
                    <th class="text-center" data-sortable="false" data-visible="true" data-field="po_editable"><span data-bs-toggle='tooltip' data-bs-placement='bottom' title="Aptitude au corps à corps, à distance avec une portée modifiable ou non"><img class='icon' src='medias/icons/po_editable.png'></span></th>
                    <th data-sortable="false" data-visible="false"  data-field="description">Description</th>
                    <th data-sortable="false" data-visible="false"  data-field="effect">Effets</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_add">Date de création</th>
                    <th class="text-center" data-sortable="false" data-visible="false" data-field="timestamp_updated">Date de mise à jour</th>
                    <th class="text-center" data-sortable="true" data-visible="true" data-field="usable"><span data-bs-toggle='tooltip' data-bs-placement='top' title="L'objet est adapté au jdr"><i class='fas fa-check text-green-d-3'></i> JDR</span></th>
                </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
        <p class="mt-2"><i class="fas fa-info-circle"></i> Il y a <span class="total_obj"></span> aptitudes. Le chargement du tableau peut prendre quelques minutes.</p>

        <div id="addCapability" style="display:none;">
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-main-focus" id="name" placeholder="Nom du sort">
                <label for="floatingInput">Nom de l'aptitude</label>
            </div>
            <div class="modal-footer d-flex flex-row justify-content-between">
                <button type="button" class="btn btn-sm btn-border-grey" data-bs-dismiss="modal">Close</button>
                <button type="button" onclick="Capability.add();" class="btn btn-sm btn-border-secondary">Créer</button>
            </div>
        </div>

        <script>
            Capability.createAndLoadDataBootstrapTable(
                [
                    {
                        selector:"#toggleUsableSwitch",
                        name:"usable",
                        type:IS_CHECKBOX
                    },
                    {
                        selector:"#selectorLevelListCheckbox",
                        name:"level",
                        type:IS_LIST_OF_CHECKBOX
                    },
                    {
                        selector:"#selectorElementListCheckbox",
                        name:"element",
                        type:IS_LIST_OF_CHECKBOX
                    },
                    {
                        selector:"#selectorCategoryListCheckbox",
                        name:"category",
                        type:IS_LIST_OF_CHECKBOX
                    }
                ]
            );
        </script>
    <?php $template["content"] = ob_get_clean();

}