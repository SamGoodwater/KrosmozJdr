# Script PowerShell d'automatisation de génération des artefacts Laravel pour Krosmoz JDR
# À lancer depuis le dossier 'project/'
# Nécessite Laravel installé et configuré

Write-Host "========================="
Write-Host " ENTITÉS PRINCIPALES"
Write-Host "========================="
php artisan make:model User --all
php artisan make:model Capability --all
php artisan make:model Classe --all
php artisan make:model Creature --all
php artisan make:model Npc --all
php artisan make:model Shop --all
php artisan make:model Item --all
php artisan make:model Consumable --all
php artisan make:model Resource --all
php artisan make:model Spell --all
php artisan make:model Attribute --all
php artisan make:model Panoply --all
php artisan make:model Monster --all
php artisan make:model Scenario --all
php artisan make:model Campaign --all
php artisan make:model Page --all
php artisan make:model Section --all
php artisan make:model Specialization --all

Write-Host "========================="
Write-Host " TYPES (tables de référence)"
Write-Host "========================="
php artisan make:model ItemType -mf
php artisan make:model ConsumableType -mf
php artisan make:model ResourceType -mf
php artisan make:model MonsterRace -mf
php artisan make:model SpellType -mf

Write-Host "========================="
Write-Host " FIN DU SCRIPT"
Write-Host "========================="
Write-Host "\nTous les artefacts Laravel ont été générés. Pense à vérifier et compléter les relations dans les modèles !" 