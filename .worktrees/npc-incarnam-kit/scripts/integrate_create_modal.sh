#!/bin/bash

# Script pour intégrer CreateEntityModal dans toutes les pages Index d'entités

ENTITY_TYPES=(
    "monster:monster"
    "npc:npc"
    "classe:classe"
    "panoply:panoply"
    "campaign:campaign"
    "scenario:scenario"
    "creature:creature"
    "resource:resource"
    "consumable:consumable"
    "attribute:attribute"
    "capability:capability"
    "specialization:specialization"
    "shop:shop"
)

for entity_info in "${ENTITY_TYPES[@]}"; do
    IFS=':' read -r entity_type entity_route <<< "$entity_info"
    file_path="resources/js/Pages/Pages/entity/${entity_type}/Index.vue"
    
    if [ -f "$file_path" ]; then
        echo "Traitement de $file_path..."
        
        # Vérifier si CreateEntityModal est déjà importé
        if ! grep -q "CreateEntityModal" "$file_path"; then
            # Ajouter l'import après EntityModal
            sed -i "/import EntityModal/a import CreateEntityModal from '@/Pages/Organismes/entity/CreateEntityModal.vue';" "$file_path"
            
            # Ajouter createModalOpen dans l'état
            sed -i "/const modalView = ref('large');/a const createModalOpen = ref(false);" "$file_path"
            
            # Remplacer handleCreate
            sed -i "s/router\.visit(route('entities\.${entity_route}s\.create'));/createModalOpen.value = true;/" "$file_path"
            
            # Ajouter les handlers après handleCreate
            sed -i "/const handleCreate = () => {/a\\
const handleCloseCreateModal = () => {\\
    createModalOpen.value = false;\\
};\\
\\
const handleEntityCreated = () => {\\
    createModalOpen.value = false;\\
};" "$file_path"
            
            # Ajouter le modal de création avant le modal de visualisation
            sed -i "/<!-- Modal -->/i\\
        <!-- Modal de création -->\\
        <CreateEntityModal\\
            :open=\"createModalOpen\"\\
            entity-type=\"${entity_type}\"\\
            @close=\"handleCloseCreateModal\"\\
            @created=\"handleEntityCreated\"\\
        />\\
\\
" "$file_path"
            
            # Remplacer le commentaire Modal par Modal de visualisation
            sed -i "s/<!-- Modal -->/<!-- Modal de visualisation -->/" "$file_path"
            
            echo "✓ $file_path mis à jour"
        else
            echo "⚠ $file_path déjà mis à jour"
        fi
    else
        echo "✗ $file_path n'existe pas"
    fi
done

echo "Terminé !"

