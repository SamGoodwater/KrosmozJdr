/**
 * Index des modèles frontend
 * 
 * @description
 * Export centralisé de tous les modèles pour faciliter les imports.
 */

// Base
export { BaseModel, default as BaseModelDefault } from './BaseModel';

// Core
export { Page, default as PageModel } from './Page';
export { Section, default as SectionModel } from './Section';
export { User, default as UserModel } from './User';
export { File, default as FileModel } from './File';

// Entities
export { Item, default as ItemModel } from './Entity/Item';
export { Spell, default as SpellModel } from './Entity/Spell';
export { Campaign, default as CampaignModel } from './Entity/Campaign';
export { Scenario, default as ScenarioModel } from './Entity/Scenario';
export { Creature, default as CreatureModel } from './Entity/Creature';
export { Monster, default as MonsterModel } from './Entity/Monster';
export { Npc, default as NpcModel } from './Entity/Npc';
export { Classe, default as ClasseModel } from './Entity/Classe';
export { Capability, default as CapabilityModel } from './Entity/Capability';
export { Specialization, default as SpecializationModel } from './Entity/Specialization';
export { Attribute, default as AttributeModel } from './Entity/Attribute';
export { Panoply, default as PanoplyModel } from './Entity/Panoply';
export { Resource, default as ResourceModel } from './Entity/Resource';
export { Consumable, default as ConsumableModel } from './Entity/Consumable';
export { Shop, default as ShopModel } from './Entity/Shop';

