# Schéma relationnel global (généré automatiquement)

```mermaid
erDiagram
  ATTRIBUTE_CREATURE {
    attribute_id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
  }
  ATTRIBUTES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
  CAMPAIGN_PAGE {
    campaign_id : bigint(20) unsigned
    page_id : bigint(20) unsigned
  }
  CAMPAIGN_PANOPLY {
    panoply_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  CAMPAIGN_SCENARIO {
    campaign_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
    order : int(11)
  }
  CAMPAIGN_SHOP {
    shop_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  CAMPAIGN_SPELL {
    spell_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  CAMPAIGN_USER {
    campaign_id : bigint(20) unsigned
    user_id : bigint(20) unsigned
  }
  CAMPAIGNS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    slug : varchar(255)
    keyword : varchar(255)
    is_public : tinyint(1)
    progress_state : int(11)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CAPABILITY_CREATURE {
    capability_id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
  }
  CAPABILITY_SPECIALIZATION {
    capability_id : bigint(20) unsigned
    specialization_id : bigint(20) unsigned
  }
  CHARACTERISTIC_ENTITIES {
    id : bigint(20) unsigned
    characteristic_id : varchar(64)
    entity : varchar(16)
    min : int(11)
    max : int(11)
    formula : text
    formula_display : text
    default_value : varchar(255)
    required : tinyint(1)
    validation_message : text
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTICS {
    id : varchar(64)
    db_column : varchar(64)
    name : varchar(255)
    short_name : varchar(64)
    description : text
    type : varchar(16)
    unit : varchar(32)
    icon : varchar(64)
    color : varchar(32)
    sort_order : smallint(5) unsigned
    forgemagie_allowed : tinyint(1)
    forgemagie_max : tinyint(3) unsigned
    applies_to : longtext
    is_competence : tinyint(1)
    characteristic_id : varchar(64)
    alternative_characteristic_id : varchar(64)
    skill_type : varchar(32)
    value_available : longtext
    labels : longtext
    validation : longtext
    mastery_value_available : longtext
    mastery_labels : longtext
    base_price_per_unit : decimal(12,2)
    rune_price_per_unit : decimal(12,2)
    created_at : timestamp
    updated_at : timestamp
  }
  CLASS_SPELL {
    classe_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    icon : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  CONSUMABLE_CAMPAIGN {
    consumable_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  CONSUMABLE_CREATURE {
    consumable_id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
    quantity : varchar(255)
  }
  CONSUMABLE_RESOURCE {
    consumable_id : bigint(20) unsigned
    resource_id : bigint(20) unsigned
    quantity : varchar(255)
  }
  CONSUMABLE_SCENARIO {
    consumable_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
  }
  CONSUMABLE_SHOP {
    consumable_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
    quantity : varchar(255)
    price : varchar(255)
    comment : varchar(255)
  }
  CONSUMABLE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    dofus_version : varchar(255)
    image : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    consumable_type_id : bigint(20) unsigned
    created_by : bigint(20) unsigned
  }
  CREATURE_ITEM {
    creature_id : bigint(20) unsigned
    item_id : bigint(20) unsigned
    quantity : varchar(255)
  }
  CREATURE_RESOURCE {
    creature_id : bigint(20) unsigned
    resource_id : bigint(20) unsigned
    quantity : varchar(255)
  }
  CREATURE_SPELL {
    creature_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  DOFUSDB_CONVERSION_FORMULAS {
    id : bigint(20) unsigned
    characteristic_id : varchar(255)
    entity : varchar(32)
    formula_type : varchar(64)
    parameters : longtext
    formula_display : text
    created_at : timestamp
    updated_at : timestamp
  }
  EQUIPMENT_SLOT_CHARACTERISTICS {
    id : bigint(20) unsigned
    equipment_slot_id : varchar(32)
    characteristic_id : varchar(64)
    bracket_max : longtext
    forgemagie_max : tinyint(3) unsigned
    base_price_per_unit : decimal(12,2)
    rune_price_per_unit : decimal(12,2)
    created_at : timestamp
    updated_at : timestamp
  }
  EQUIPMENT_SLOTS {
    id : varchar(32)
    name : varchar(255)
    sort_order : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
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
  FILE_CAMPAIGN {
    file_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
    order : int(11)
  }
  FILE_SCENARIO {
    file_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
    order : int(11)
  }
  FILE_SECTION {
    file_id : bigint(20) unsigned
    section_id : bigint(20) unsigned
    order : int(11)
  }
  FILES {
    id : bigint(20) unsigned
    file : varchar(255)
    title : varchar(255)
    comment : varchar(255)
    description : text
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
  }
  ITEM_CAMPAIGN {
    item_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  ITEM_PANOPLY {
    item_id : bigint(20) unsigned
    panoply_id : bigint(20) unsigned
  }
  ITEM_RESOURCE {
    item_id : bigint(20) unsigned
    resource_id : bigint(20) unsigned
    quantity : varchar(255)
  }
  ITEM_SCENARIO {
    item_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
  }
  ITEM_SHOP {
    item_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
    quantity : varchar(255)
    price : varchar(255)
    comment : varchar(255)
  }
  ITEM_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
  MONSTER_CAMPAIGN {
    monster_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  MONSTER_RACES {
    id : bigint(20) unsigned
    dofusdb_race_id : int(11)
    name : varchar(255)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
    id_super_race : bigint(20) unsigned
  }
  MONSTER_SCENARIO {
    monster_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
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
  NOTIFICATIONS {
    id : char(36)
    type : varchar(255)
    notifiable_type : varchar(255)
    notifiable_id : bigint(20) unsigned
    data : text
    read_at : timestamp
    created_at : timestamp
    updated_at : timestamp
  }
  NPC_CAMPAIGN {
    npc_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  NPC_PANOPLY {
    npc_id : bigint(20) unsigned
    panoply_id : bigint(20) unsigned
  }
  NPC_SCENARIO {
    npc_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
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
  PAGE_USER {
    page_id : bigint(20) unsigned
    user_id : bigint(20) unsigned
  }
  PAGES {
    id : bigint(20) unsigned
    title : varchar(255)
    slug : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    dofusdb_id : varchar(255)
    name : varchar(255)
    description : varchar(255)
    bonus : varchar(255)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  PANOPLY_SHOP {
    panoply_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
  }
  PASSWORD_RESET_TOKENS {
    email : varchar(255)
    token : varchar(255)
    created_at : timestamp
  }
  RESOURCE_CAMPAIGN {
    resource_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  RESOURCE_SCENARIO {
    resource_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
  }
  RESOURCE_SHOP {
    resource_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
    quantity : varchar(255)
    price : varchar(255)
    comment : varchar(255)
  }
  RESOURCE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    auto_update : tinyint(1)
    deleted_at : timestamp
    resource_type_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
    created_by : bigint(20) unsigned
  }
  SCENARIO_LINK {
    id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
    next_scenario_id : bigint(20) unsigned
    condition : text
  }
  SCENARIO_PAGE {
    scenario_id : bigint(20) unsigned
    page_id : bigint(20) unsigned
  }
  SCENARIO_PANOPLY {
    scenario_id : bigint(20) unsigned
    panoply_id : bigint(20) unsigned
  }
  SCENARIO_SHOP {
    scenario_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
  }
  SCENARIO_SPELL {
    scenario_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
  }
  SCENARIO_USER {
    scenario_id : bigint(20) unsigned
    user_id : bigint(20) unsigned
  }
  SCENARIOS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    slug : varchar(255)
    keyword : varchar(255)
    is_public : tinyint(1)
    progress_state : int(11)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  SCRAPPING_PENDING_RESOURCE_TYPE_ITEMS {
    id : bigint(20) unsigned
    dofusdb_type_id : int(10) unsigned
    dofusdb_item_id : int(10) unsigned
    context : varchar(255)
    source_entity_type : varchar(255)
    source_entity_dofusdb_id : int(10) unsigned
    quantity : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SECTION_USER {
    section_id : bigint(20) unsigned
    user_id : bigint(20) unsigned
  }
  SECTIONS {
    id : bigint(20) unsigned
    page_id : bigint(20) unsigned
    title : varchar(255)
    slug : varchar(255)
    order : int(11)
    template : varchar(255)
    type : varchar(255)
    settings : longtext
    data : longtext
    read_level : tinyint(4)
    write_level : tinyint(4)
    params : longtext
    state : varchar(255)
    created_by : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
    created_by : bigint(20) unsigned
  }
  SPELL_INVOCATION {
    spell_id : bigint(20) unsigned
    monster_id : bigint(20) unsigned
  }
  SPELL_TYPE {
    spell_id : bigint(20) unsigned
    spell_type_id : bigint(20) unsigned
  }
  SPELL_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : varchar(255)
    color : varchar(255)
    icon : varchar(255)
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    is_system : tinyint(1)
    avatar : varchar(255)
    notifications_enabled : tinyint(1)
    notification_channels : longtext
    deleted_at : timestamp
    created_at : timestamp
    updated_at : timestamp
  }
  ATTRIBUTE_CREATURE }o--|| ATTRIBUTES : "FK attribute_id"
  ATTRIBUTE_CREATURE }o--|| CREATURES : "FK creature_id"
  ATTRIBUTES }o--|| USERS : "FK created_by"
  CAMPAIGN_PAGE }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_PAGE }o--|| PAGES : "FK page_id"
  CAMPAIGN_PANOPLY }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_PANOPLY }o--|| PANOPLIES : "FK panoply_id"
  CAMPAIGN_SCENARIO }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  CAMPAIGN_SHOP }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_SHOP }o--|| SHOPS : "FK shop_id"
  CAMPAIGN_SPELL }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_SPELL }o--|| SPELLS : "FK spell_id"
  CAMPAIGN_USER }o--|| CAMPAIGNS : "FK campaign_id"
  CAMPAIGN_USER }o--|| USERS : "FK user_id"
  CAMPAIGNS }o--|| USERS : "FK created_by"
  CAPABILITIES }o--|| USERS : "FK created_by"
  CAPABILITY_CREATURE }o--|| CAPABILITIES : "FK capability_id"
  CAPABILITY_CREATURE }o--|| CREATURES : "FK creature_id"
  CAPABILITY_SPECIALIZATION }o--|| CAPABILITIES : "FK capability_id"
  CAPABILITY_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
  CHARACTERISTIC_ENTITIES }o--|| CHARACTERISTICS : "FK characteristic_id"
  CHARACTERISTICS }o--|| CHARACTERISTICS : "FK alternative_characteristic_id"
  CHARACTERISTICS }o--|| CHARACTERISTICS : "FK characteristic_id"
  CLASS_SPELL }o--|| CLASSES : "FK classe_id"
  CLASS_SPELL }o--|| SPELLS : "FK spell_id"
  CLASSES }o--|| USERS : "FK created_by"
  CONSUMABLE_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  CONSUMABLE_CAMPAIGN }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_CREATURE }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_CREATURE }o--|| CREATURES : "FK creature_id"
  CONSUMABLE_RESOURCE }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_RESOURCE }o--|| RESOURCES : "FK resource_id"
  CONSUMABLE_SCENARIO }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  CONSUMABLE_SHOP }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_SHOP }o--|| SHOPS : "FK shop_id"
  CONSUMABLE_TYPES }o--|| USERS : "FK created_by"
  CONSUMABLES }o--|| CONSUMABLE_TYPES : "FK consumable_type_id"
  CONSUMABLES }o--|| USERS : "FK created_by"
  CREATURE_ITEM }o--|| CREATURES : "FK creature_id"
  CREATURE_ITEM }o--|| ITEMS : "FK item_id"
  CREATURE_RESOURCE }o--|| CREATURES : "FK creature_id"
  CREATURE_RESOURCE }o--|| RESOURCES : "FK resource_id"
  CREATURE_SPELL }o--|| CREATURES : "FK creature_id"
  CREATURE_SPELL }o--|| SPELLS : "FK spell_id"
  CREATURES }o--|| USERS : "FK created_by"
  DOFUSDB_CONVERSION_FORMULAS }o--|| CHARACTERISTICS : "FK characteristic_id"
  EQUIPMENT_SLOT_CHARACTERISTICS }o--|| CHARACTERISTICS : "FK characteristic_id"
  EQUIPMENT_SLOT_CHARACTERISTICS }o--|| EQUIPMENT_SLOTS : "FK equipment_slot_id"
  FILE_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  FILE_CAMPAIGN }o--|| FILES : "FK file_id"
  FILE_SCENARIO }o--|| FILES : "FK file_id"
  FILE_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  FILE_SECTION }o--|| FILES : "FK file_id"
  FILE_SECTION }o--|| SECTIONS : "FK section_id"
  ITEM_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  ITEM_CAMPAIGN }o--|| ITEMS : "FK item_id"
  ITEM_PANOPLY }o--|| ITEMS : "FK item_id"
  ITEM_PANOPLY }o--|| PANOPLIES : "FK panoply_id"
  ITEM_RESOURCE }o--|| ITEMS : "FK item_id"
  ITEM_RESOURCE }o--|| RESOURCES : "FK resource_id"
  ITEM_SCENARIO }o--|| ITEMS : "FK item_id"
  ITEM_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  ITEM_SHOP }o--|| ITEMS : "FK item_id"
  ITEM_SHOP }o--|| SHOPS : "FK shop_id"
  ITEM_TYPES }o--|| USERS : "FK created_by"
  ITEMS }o--|| USERS : "FK created_by"
  ITEMS }o--|| ITEM_TYPES : "FK item_type_id"
  MONSTER_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  MONSTER_CAMPAIGN }o--|| MONSTERS : "FK monster_id"
  MONSTER_RACES }o--|| USERS : "FK created_by"
  MONSTER_RACES }o--|| MONSTER_RACES : "FK id_super_race"
  MONSTER_SCENARIO }o--|| MONSTERS : "FK monster_id"
  MONSTER_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  MONSTERS }o--|| CREATURES : "FK creature_id"
  MONSTERS }o--|| MONSTER_RACES : "FK monster_race_id"
  NPC_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  NPC_CAMPAIGN }o--|| NPCS : "FK npc_id"
  NPC_PANOPLY }o--|| NPCS : "FK npc_id"
  NPC_PANOPLY }o--|| PANOPLIES : "FK panoply_id"
  NPC_SCENARIO }o--|| NPCS : "FK npc_id"
  NPC_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  NPCS }o--|| CLASSES : "FK classe_id"
  NPCS }o--|| CREATURES : "FK creature_id"
  NPCS }o--|| SPECIALIZATIONS : "FK specialization_id"
  PAGE_USER }o--|| PAGES : "FK page_id"
  PAGE_USER }o--|| USERS : "FK user_id"
  PAGES }o--|| USERS : "FK created_by"
  PAGES }o--|| PAGES : "FK parent_id"
  PANOPLIES }o--|| USERS : "FK created_by"
  PANOPLY_SHOP }o--|| PANOPLIES : "FK panoply_id"
  PANOPLY_SHOP }o--|| SHOPS : "FK shop_id"
  RESOURCE_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  RESOURCE_CAMPAIGN }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SCENARIO }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  RESOURCE_SHOP }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SHOP }o--|| SHOPS : "FK shop_id"
  RESOURCE_TYPES }o--|| USERS : "FK created_by"
  RESOURCES }o--|| USERS : "FK created_by"
  RESOURCES }o--|| RESOURCE_TYPES : "FK resource_type_id"
  SCENARIO_LINK }o--|| SCENARIOS : "FK next_scenario_id"
  SCENARIO_LINK }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_PAGE }o--|| PAGES : "FK page_id"
  SCENARIO_PAGE }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_PANOPLY }o--|| PANOPLIES : "FK panoply_id"
  SCENARIO_PANOPLY }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_SHOP }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_SHOP }o--|| SHOPS : "FK shop_id"
  SCENARIO_SPELL }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_SPELL }o--|| SPELLS : "FK spell_id"
  SCENARIO_USER }o--|| SCENARIOS : "FK scenario_id"
  SCENARIO_USER }o--|| USERS : "FK user_id"
  SCENARIOS }o--|| USERS : "FK created_by"
  SECTION_USER }o--|| SECTIONS : "FK section_id"
  SECTION_USER }o--|| USERS : "FK user_id"
  SECTIONS }o--|| USERS : "FK created_by"
  SECTIONS }o--|| PAGES : "FK page_id"
  SHOPS }o--|| USERS : "FK created_by"
  SHOPS }o--|| NPCS : "FK npc_id"
  SPECIALIZATIONS }o--|| USERS : "FK created_by"
  SPELL_INVOCATION }o--|| MONSTERS : "FK monster_id"
  SPELL_INVOCATION }o--|| SPELLS : "FK spell_id"
  SPELL_TYPE }o--|| SPELLS : "FK spell_id"
  SPELL_TYPE }o--|| SPELL_TYPES : "FK spell_type_id"
  SPELL_TYPES }o--|| USERS : "FK created_by"
  SPELLS }o--|| USERS : "FK created_by"
```
