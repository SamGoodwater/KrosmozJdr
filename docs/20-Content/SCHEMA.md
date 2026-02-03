# Schéma relationnel global (généré automatiquement)

```mermaid
erDiagram
  ATTRIBUTES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CACHE {
    key : varchar(255)
    value : mediumtext
    expiration : int(11)
  }
  CACHE_LOCKS {
    key : varchar(255)
    owner : varchar(255)
    expiration : int(11)
  }
  CAMPAIGNS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    slug : varchar(255)
    keyword : varchar(255)
    is_public : tinyint(1)
    state : int(11)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CAPABILITIES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    effect : varchar(255)
    level : varchar(255)
    pa : varchar(255)
    po : varchar(255)
    po_editable : tinyint(1)
    time_before_use_again : varchar(255)
    casting_time : varchar(255)
    duration : varchar(255)
    element : varchar(255)
    is_magic : tinyint(1)
    ritual_available : tinyint(1)
    powerful : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CLASSES {
    id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    name : varchar(255)
    description_fast : varchar(255)
    description : varchar(255)
    life : varchar(255)
    life_dice : varchar(255)
    specificity : varchar(255)
    dofus_version : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    icon : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CONSUMABLE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CONSUMABLES {
    id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    name : varchar(255)
    description : varchar(255)
    effect : varchar(255)
    level : varchar(255)
    recipe : varchar(255)
    price : varchar(255)
    rarity : int(11)
    usable : tinyint(4)
    is_visible : varchar(255)
    dofus_version : varchar(255)
    image : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    consumable_type_id : bigint(20) unsigned
    created_by : bigint(20) unsigned
  }
  CREATURES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    hostility : int(11)
    location : varchar(255)
    level : varchar(255)
    other_info : varchar(255)
    life : varchar(255)
    pa : varchar(255)
    pm : varchar(255)
    po : varchar(255)
    ini : varchar(255)
    invocation : varchar(255)
    touch : varchar(255)
    ca : varchar(255)
    dodge_pa : varchar(255)
    dodge_pm : varchar(255)
    fuite : varchar(255)
    tacle : varchar(255)
    vitality : varchar(255)
    sagesse : varchar(255)
    strong : varchar(255)
    intel : varchar(255)
    agi : varchar(255)
    chance : varchar(255)
    do_fixe_neutre : varchar(255)
    do_fixe_terre : varchar(255)
    do_fixe_feu : varchar(255)
    do_fixe_air : varchar(255)
    do_fixe_eau : varchar(255)
    res_fixe_neutre : text
    res_fixe_terre : text
    res_fixe_feu : text
    res_fixe_air : text
    res_fixe_eau : text
    res_neutre : varchar(255)
    res_terre : varchar(255)
    res_feu : varchar(255)
    res_air : varchar(255)
    res_eau : varchar(255)
    acrobatie_bonus : varchar(255)
    discretion_bonus : varchar(255)
    escamotage_bonus : varchar(255)
    athletisme_bonus : varchar(255)
    intimidation_bonus : varchar(255)
    arcane_bonus : varchar(255)
    histoire_bonus : varchar(255)
    investigation_bonus : varchar(255)
    nature_bonus : varchar(255)
    religion_bonus : varchar(255)
    dressage_bonus : varchar(255)
    medecine_bonus : varchar(255)
    perception_bonus : varchar(255)
    perspicacite_bonus : varchar(255)
    survie_bonus : varchar(255)
    persuasion_bonus : varchar(255)
    representation_bonus : varchar(255)
    supercherie_bonus : varchar(255)
    acrobatie_mastery : tinyint(4)
    discretion_mastery : tinyint(4)
    escamotage_mastery : tinyint(4)
    athletisme_mastery : tinyint(4)
    intimidation_mastery : tinyint(4)
    arcane_mastery : tinyint(4)
    histoire_mastery : tinyint(4)
    investigation_mastery : tinyint(4)
    nature_mastery : tinyint(4)
    religion_mastery : tinyint(4)
    dressage_mastery : tinyint(4)
    medecine_mastery : tinyint(4)
    perception_mastery : tinyint(4)
    perspicacite_mastery : tinyint(4)
    survie_mastery : tinyint(4)
    persuasion_mastery : tinyint(4)
    representation_mastery : tinyint(4)
    supercherie_mastery : tinyint(4)
    kamas : varchar(255)
    drop_ : varchar(255)
    other_item : varchar(255)
    other_consumable : varchar(255)
    other_resource : varchar(255)
    other_spell : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  FAILED_JOBS {
    id : bigint(20) unsigned
    uuid : varchar(255)
    connection : text
    queue : text
    payload : longtext
    exception : longtext
    failed_at : timestamp
  }
  ITEM_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  ITEMS {
    id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    name : varchar(255)
    level : varchar(255)
    description : varchar(255)
    effect : varchar(255)
    bonus : varchar(255)
    recipe : varchar(255)
    price : varchar(255)
    rarity : int(11)
    dofus_version : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    item_type_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
    created_by : bigint(20) unsigned
  }
  JOB_BATCHES {
    id : varchar(255)
    name : varchar(255)
    total_jobs : int(11)
    pending_jobs : int(11)
    failed_jobs : int(11)
    failed_job_ids : longtext
    options : mediumtext
    cancelled_at : int(11)
    created_at : int(11)
    finished_at : int(11)
  }
  JOBS {
    id : bigint(20) unsigned
    queue : varchar(255)
    payload : longtext
    attempts : tinyint(3) unsigned
    reserved_at : int(10) unsigned
    available_at : int(10) unsigned
    created_at : int(10) unsigned
  }
  MIGRATIONS {
    id : int(10) unsigned
    migration : varchar(255)
    batch : int(11)
  }
  MONSTER_RACES {
    id : bigint(20) unsigned
    name : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
    id_super_race : bigint(20) unsigned
  }
  MONSTERS {
    id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    dofus_version : varchar(255)
    auto_update : tinyint(1)
    size : int(11)
    is_boss : tinyint(1)
    boss_pa : varchar(255)
    monster_race_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  NPCS {
    id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
    story : varchar(255)
    historical : varchar(255)
    age : varchar(255)
    size : varchar(255)
    classe_id : bigint(20) unsigned
    specialization_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  PAGES {
    id : bigint(20) unsigned
    title : varchar(255)
    slug : varchar(255)
    is_visible : varchar(255)
    in_menu : tinyint(1)
    state : varchar(255)
    parent_id : bigint(20) unsigned
    menu_order : int(11)
    created_by : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
  }
  PANOPLIES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    bonus : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  PASSWORD_RESET_TOKENS {
    email : varchar(255)
    token : varchar(255)
    created_at : timestamp
  }
  RESOURCE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  RESOURCES {
    id : bigint(20) unsigned
    dofusdb_id : varchar(255)
    official_id : int(11)
    name : varchar(255)
    description : varchar(255)
    level : varchar(255)
    price : varchar(255)
    weight : varchar(255)
    rarity : int(11)
    dofus_version : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    resource_type_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
    created_by : bigint(20) unsigned
  }
  SCENARIOS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    slug : varchar(255)
    keyword : varchar(255)
    is_public : tinyint(1)
    state : int(11)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  SESSIONS {
    id : varchar(255)
    user_id : bigint(20) unsigned
    ip_address : varchar(45)
    user_agent : text
    payload : longtext
    last_activity : int(11)
  }
  SHOPS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    location : varchar(255)
    price : int(11)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
    npc_id : bigint(20) unsigned
  }
  SPECIALIZATIONS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  SPELL_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    color : varchar(255)
    icon : varchar(255)
    usable : tinyint(4)
    is_visible : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  SPELLS {
    id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    name : varchar(255)
    description : varchar(255)
    effect : varchar(255)
    area : int(11)
    level : varchar(255)
    po : varchar(255)
    po_editable : tinyint(1)
    pa : varchar(255)
    cast_per_turn : varchar(255)
    cast_per_target : varchar(255)
    sight_line : tinyint(1)
    number_between_two_cast : varchar(255)
    number_between_two_cast_editable : tinyint(1)
    element : int(11)
    category : int(11)
    is_magic : tinyint(1)
    powerful : int(11)
    usable : tinyint(4)
    is_visible : varchar(255)
    image : varchar(255)
    auto_update : tinyint(1)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  USERS {
    id : bigint(20) unsigned
    name : varchar(255)
    email : varchar(255)
    email_verified_at : timestamp
    password : varchar(255)
    remember_token : varchar(100)
    role : int(11)
    avatar : varchar(255)
    notifications_enabled : tinyint(1)
    notification_channels : longtext
    deleted_at : timestamp
    created_at : timestamp
    updated_at : timestamp
  }
  ATTRIBUTES }o--|| USERS : "FK created_by"
  CAMPAIGNS }o--|| USERS : "FK created_by"
  CAPABILITIES }o--|| USERS : "FK created_by"
  CLASSES }o--|| USERS : "FK created_by"
  CONSUMABLE_TYPES }o--|| USERS : "FK created_by"
  CONSUMABLES }o--|| CONSUMABLE_TYPES : "FK consumable_type_id"
  CONSUMABLES }o--|| USERS : "FK created_by"
  CREATURES }o--|| USERS : "FK created_by"
  ITEM_TYPES }o--|| USERS : "FK created_by"
  ITEMS }o--|| USERS : "FK created_by"
  ITEMS }o--|| ITEM_TYPES : "FK item_type_id"
  MONSTER_RACES }o--|| USERS : "FK created_by"
  MONSTER_RACES }o--|| MONSTER_RACES : "FK id_super_race"
  MONSTERS }o--|| CREATURES : "FK creature_id"
  MONSTERS }o--|| MONSTER_RACES : "FK monster_race_id"
  NPCS }o--|| CLASSES : "FK classe_id"
  NPCS }o--|| CREATURES : "FK creature_id"
  NPCS }o--|| SPECIALIZATIONS : "FK specialization_id"
  PAGES }o--|| USERS : "FK created_by"
  PAGES }o--|| PAGES : "FK parent_id"
  PANOPLIES }o--|| USERS : "FK created_by"
  RESOURCE_TYPES }o--|| USERS : "FK created_by"
  RESOURCES }o--|| USERS : "FK created_by"
  RESOURCES }o--|| RESOURCE_TYPES : "FK resource_type_id"
  SCENARIOS }o--|| USERS : "FK created_by"
  SHOPS }o--|| USERS : "FK created_by"
  SHOPS }o--|| NPCS : "FK npc_id"
  SPECIALIZATIONS }o--|| USERS : "FK created_by"
  SPELL_TYPES }o--|| USERS : "FK created_by"
  SPELLS }o--|| USERS : "FK created_by"
```
