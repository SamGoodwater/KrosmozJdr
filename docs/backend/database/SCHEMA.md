# Schéma relationnel global

```mermaid
erDiagram
  ADMIN_ACTIVITY_LOGS {
    id : bigint(20) unsigned
    domain : varchar(40)
    action : varchar(40)
    subject_type : varchar(255)
    subject_id : bigint(20) unsigned
    subject_label : varchar(255)
    actor_id : bigint(20) unsigned
    status : varchar(40)
    properties : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  APPLICATION_SETTINGS {
    id : bigint(20) unsigned
    key : varchar(255)
    value : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  BREED_CAPABILITY {
    id : bigint(20) unsigned
    breed_id : bigint(20) unsigned
    capability_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  BREED_CREATURE_TRAIT {
    breed_id : bigint(20) unsigned
    creature_trait_id : bigint(20) unsigned
    level : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  BREED_ELEMENT_ORIENTATIONS {
    id : bigint(20) unsigned
    breed_id : bigint(20) unsigned
    element : varchar(16)
    orientation_key : varchar(64)
    created_at : timestamp
    updated_at : timestamp
  }
  BREED_LANGUAGE {
    id : bigint(20) unsigned
    breed_id : bigint(20) unsigned
    language_id : bigint(20) unsigned
    sort_order : tinyint(3) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  BREED_SPELL {
    id : bigint(20) unsigned
    breed_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
    character_level : smallint(5) unsigned
    slot_index : tinyint(3) unsigned
    choice_order : tinyint(3) unsigned
  }
  BREEDS {
    id : bigint(20) unsigned
    official_id : varchar(255)
    dofusdb_id : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    name : varchar(255)
    description_fast : varchar(255)
    description : varchar(255)
    evolution : longtext
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
    campaign_id : bigint(20) unsigned
    panoply_id : bigint(20) unsigned
  }
  CAMPAIGN_SCENARIO {
    campaign_id : bigint(20) unsigned
    scenario_id : bigint(20) unsigned
  }
  CAMPAIGN_SHOP {
    campaign_id : bigint(20) unsigned
    shop_id : bigint(20) unsigned
  }
  CAMPAIGN_SPELL {
    campaign_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
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
    description : text
    effect : text
    level : varchar(255)
    pa : varchar(255)
    po : varchar(255)
    po_editable : tinyint(1)
    time_before_use_again : varchar(255)
    casting_time : varchar(255)
    duration : varchar(255)
    element : tinyint(3) unsigned
    is_magic : tinyint(1)
    ritual_available : tinyint(1)
    is_passive : tinyint(1)
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
    level : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTIC_CREATURE {
    id : bigint(20) unsigned
    characteristic_id : bigint(20) unsigned
    dofusdb_characteristic_id : int(10) unsigned
    entity : varchar(32)
    db_column : varchar(64)
    min : varchar(512)
    max : varchar(512)
    formula : text
    formula_display : text
    default_value : varchar(512)
    conversion_formula : text
    conversion_function : varchar(64)
    conversion_dofus_sample : longtext
    conversion_krosmoz_sample : longtext
    conversion_sample_rows : longtext
    norms_grid : longtext
    norms_conditions : longtext
    norms_description : text
    norms_help_section_id : bigint(20) unsigned
    labels : longtext
    validation : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTIC_OBJECT {
    id : bigint(20) unsigned
    characteristic_id : bigint(20) unsigned
    dofusdb_characteristic_id : int(10) unsigned
    entity : varchar(32)
    db_column : varchar(64)
    min : varchar(512)
    max : varchar(512)
    formula : text
    formula_display : text
    default_value : varchar(512)
    conversion_formula : text
    conversion_function : varchar(64)
    conversion_dofus_sample : longtext
    conversion_krosmoz_sample : longtext
    conversion_sample_rows : longtext
    norms_grid : longtext
    norms_conditions : longtext
    norms_description : text
    norms_help_section_id : bigint(20) unsigned
    forgemagie_max : tinyint(3) unsigned
    base_price_per_unit : decimal(12,2)
    rune_price_per_unit : decimal(12,2)
    value_available : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTIC_OBJECT_ITEM_TYPE {
    id : bigint(20) unsigned
    characteristic_object_id : bigint(20) unsigned
    item_type_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTIC_SPELL {
    id : bigint(20) unsigned
    characteristic_id : bigint(20) unsigned
    dofusdb_characteristic_id : int(10) unsigned
    entity : varchar(32)
    db_column : varchar(64)
    min : varchar(512)
    max : varchar(512)
    formula : text
    formula_display : text
    default_value : varchar(512)
    conversion_formula : text
    conversion_function : varchar(64)
    conversion_dofus_sample : longtext
    conversion_krosmoz_sample : longtext
    conversion_sample_rows : longtext
    norms_grid : longtext
    norms_conditions : longtext
    norms_description : text
    norms_help_section_id : bigint(20) unsigned
    value_available : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  CHARACTERISTICS {
    id : bigint(20) unsigned
    key : varchar(64)
    name : varchar(255)
    short_name : varchar(64)
    helper : text
    descriptions : text
    icon : varchar(64)
    icon_false : varchar(64)
    color : varchar(32)
    value_overrides : longtext
    unit : varchar(32)
    type : varchar(16)
    status : varchar(32)
    sort_order : smallint(5) unsigned
    group : varchar(16)
    hide_when_empty : tinyint(1)
    hide_when_false : tinyint(1)
    linked_to_characteristic_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CONDITION_CAPABILITY {
    condition_id : bigint(20) unsigned
    capability_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CONDITION_CREATURE {
    condition_id : bigint(20) unsigned
    creature_id : bigint(20) unsigned
  }
  CONDITION_SPELL {
    id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
    condition_id : bigint(20) unsigned
    application_mode : varchar(16)
    dofus_effect_id : int(10) unsigned
    duration : int(11)
    dispellable : tinyint(1)
    target_mask : varchar(64)
    created_at : timestamp
    updated_at : timestamp
  }
  CONDITIONS {
    id : bigint(20) unsigned
    dofusdb_id : int(10) unsigned
    name : varchar(255)
    description : text
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    icon : varchar(255)
    image : varchar(255)
    prevents_spell_cast : tinyint(1)
    prevents_fight : tinyint(1)
    cant_be_moved : tinyint(1)
    cant_be_pushed : tinyint(1)
    cant_deal_damage : tinyint(1)
    invulnerable : tinyint(1)
    cant_switch_position : tinyint(1)
    incurable : tinyint(1)
    invulnerable_melee : tinyint(1)
    invulnerable_range : tinyint(1)
    cant_tackle : tinyint(1)
    cant_be_tackled : tinyint(1)
    display_turn_remaining : tinyint(1)
    is_main_state : tinyint(1)
    dissipable : tinyint(1)
    raw : longtext
    created_at : timestamp
    updated_at : timestamp
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
    quantity : int(10) unsigned
    price : varchar(255)
    comment : varchar(255)
  }
  CONSUMABLE_SPECIALIZATION {
    specialization_id : bigint(20) unsigned
    consumable_id : bigint(20) unsigned
    level : smallint(5) unsigned
    quantity : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CONSUMABLE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    description : text
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
  CREATURE_CREATURE_TRAIT {
    creature_id : bigint(20) unsigned
    creature_trait_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CREATURE_ITEM {
    creature_id : bigint(20) unsigned
    item_id : bigint(20) unsigned
    quantity : int(10) unsigned
  }
  CREATURE_RESOURCE {
    creature_id : bigint(20) unsigned
    resource_id : bigint(20) unsigned
    quantity : int(10) unsigned
  }
  CREATURE_SPELL {
    creature_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
  }
  CREATURE_TRAIT_SPECIALIZATION {
    specialization_id : bigint(20) unsigned
    creature_trait_id : bigint(20) unsigned
    level : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  CREATURE_TRAITS {
    id : bigint(20) unsigned
    name : varchar(255)
    description : text
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    image : varchar(255)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
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
    critical_hit : varchar(16)
    heal_bonus : varchar(16)
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
    do_sagesse : varchar(255)
    do_vitalite : varchar(255)
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
    res_sagesse : varchar(255)
    res_vitalite : varchar(255)
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
    save_vitality_bonus : tinyint(3) unsigned
    save_wisdom_bonus : tinyint(3) unsigned
    save_strength_bonus : tinyint(3) unsigned
    save_intelligence_bonus : tinyint(3) unsigned
    save_chance_bonus : tinyint(3) unsigned
    save_agility_bonus : tinyint(3) unsigned
    save_vitality_mastery : tinyint(3) unsigned
    save_wisdom_mastery : tinyint(3) unsigned
    save_strength_mastery : tinyint(3) unsigned
    save_intelligence_mastery : tinyint(3) unsigned
    save_chance_mastery : tinyint(3) unsigned
    save_agility_mastery : tinyint(3) unsigned
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
  DATA_SUBJECT_REQUESTS {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    type : varchar(32)
    status : varchar(32)
    requested_at : timestamp
    confirmed_at : timestamp
    processed_at : timestamp
    expires_at : timestamp
    meta : longtext
    ip_address : varchar(45)
    user_agent : text
    created_at : timestamp
    updated_at : timestamp
  }
  DOFUSDB_EFFECT_MAPPINGS {
    id : bigint(20) unsigned
    dofusdb_effect_id : int(10) unsigned
    sub_effect_slug : varchar(64)
    characteristic_source : varchar(32)
    characteristic_key : varchar(64)
    created_at : timestamp
    updated_at : timestamp
  }
  EFFECT_DEGREES {
    id : bigint(20) unsigned
    effect_id : bigint(20) unsigned
    degree : tinyint(3) unsigned
    required_creature_level : smallint(5) unsigned
    area : varchar(64)
    slug : varchar(64)
    config_signature : varchar(64)
    created_at : timestamp
    updated_at : timestamp
  }
  EFFECT_SPELL {
    id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
    effect_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  EFFECT_SUB_EFFECT {
    id : bigint(20) unsigned
    effect_degree_id : bigint(20) unsigned
    sub_effect_id : bigint(20) unsigned
    order : smallint(5) unsigned
    scope : varchar(32)
    value_min : int(11)
    value_max : int(11)
    dice_num : tinyint(3) unsigned
    dice_side : tinyint(3) unsigned
    params : longtext
    crit_only : tinyint(1)
    duration_formula : varchar(255)
    logic_group : varchar(64)
    logic_operator : varchar(8)
    logic_condition : varchar(255)
    created_at : timestamp
    updated_at : timestamp
  }
  EFFECT_USAGES {
    id : bigint(20) unsigned
    entity_type : varchar(255)
    entity_id : bigint(20) unsigned
    effect_degree_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  EFFECTS {
    id : bigint(20) unsigned
    name : varchar(255)
    slug : varchar(64)
    description : text
    target_type : varchar(32)
    created_at : timestamp
    updated_at : timestamp
  }
  ENTITY_IMAGE_UPLOADS {
    id : bigint(20) unsigned
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
  FEEDBACK_MESSAGES {
    id : bigint(20) unsigned
    feedback_thread_id : bigint(20) unsigned
    author_id : bigint(20) unsigned
    author_role : varchar(16)
    body : text
    attachment_path : varchar(255)
    attachment_name : varchar(255)
    created_at : timestamp
    updated_at : timestamp
  }
  FEEDBACK_THREADS {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    type : varchar(32)
    status : varchar(32)
    url : varchar(500)
    subject_preview : varchar(160)
    last_message_at : timestamp
    user_unread_count : int(10) unsigned
    staff_unread_count : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
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
    quantity : int(10) unsigned
    price : varchar(255)
    comment : varchar(255)
  }
  ITEM_SPECIALIZATION {
    specialization_id : bigint(20) unsigned
    item_id : bigint(20) unsigned
    level : smallint(5) unsigned
    quantity : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  ITEM_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    description : text
    effect : varchar(255)
    bonus : text
    recipe : varchar(255)
    price_calculated : bigint(20)
    price_custom : bigint(20)
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
  LANGUAGES {
    id : bigint(20) unsigned
    name : varchar(255)
    description : text
    color : varchar(32)
    created_at : timestamp
    updated_at : timestamp
  }
  MEDIA {
    id : bigint(20) unsigned
    model_type : varchar(255)
    model_id : bigint(20) unsigned
    uuid : char(36)
    collection_name : varchar(255)
    name : varchar(255)
    file_name : varchar(255)
    mime_type : varchar(255)
    disk : varchar(255)
    conversions_disk : varchar(255)
    size : bigint(20) unsigned
    manipulations : longtext
    custom_properties : longtext
    generated_conversions : longtext
    responsive_images : longtext
    order_column : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
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
  MONSTER_LANGUAGE {
    id : bigint(20) unsigned
    monster_id : bigint(20) unsigned
    language_id : bigint(20) unsigned
    sort_order : tinyint(3) unsigned
    created_at : timestamp
    updated_at : timestamp
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
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
  }
  NOTIFICATION_DIGEST_QUEUE {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    notification_type : varchar(64)
    frequency : varchar(16)
    payload : longtext
    created_at : timestamp
  }
  NOTIFICATIONS {
    id : char(36)
    type : varchar(255)
    notifiable_type : varchar(255)
    notifiable_id : bigint(20) unsigned
    data : text
    read_at : timestamp
    archived_at : timestamp
    pinned_at : timestamp
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
    breed_id : bigint(20) unsigned
    specialization_id : bigint(20) unsigned
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
    created_at : timestamp
    updated_at : timestamp
    deleted_at : timestamp
  }
  OAUTH_ACCOUNTS {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    provider : varchar(32)
    provider_id : varchar(255)
    provider_email : varchar(255)
    provider_name : varchar(255)
    avatar_url : varchar(255)
    created_at : timestamp
    updated_at : timestamp
  }
  OBJECT_EFFECTS {
    id : bigint(20) unsigned
    object_effectable_type : varchar(255)
    object_effectable_id : bigint(20) unsigned
    action : varchar(32)
    characteristic_id : bigint(20) unsigned
    monster_id : bigint(20) unsigned
    value : int(11)
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
    state : varchar(255)
    in_menu : tinyint(1)
    parent_id : bigint(20) unsigned
    menu_order : int(11)
    menu_group : varchar(255)
    entity_key : varchar(50)
    icon : varchar(255)
    page_css_classes : varchar(500)
    title_css_classes : varchar(500)
    menu_item_css_classes : varchar(500)
    settings : longtext
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
    bonus : text
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
  PRIVACY_AUDIT_LOGS {
    id : bigint(20) unsigned
    actor_id : bigint(20) unsigned
    subject_user_id : bigint(20) unsigned
    action : varchar(64)
    context : longtext
    ip_address : varchar(45)
    user_agent : text
    created_at : timestamp
  }
  PRIVACY_EXPORTS {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    data_subject_request_id : bigint(20) unsigned
    status : varchar(32)
    path : varchar(255)
    checksum : varchar(64)
    expires_at : timestamp
    downloaded_at : timestamp
    meta : longtext
    created_at : timestamp
    updated_at : timestamp
  }
  PROJECT_SCHEDULE_TASKS {
    id : bigint(20) unsigned
    task_key : varchar(80)
    enabled : tinyint(1)
    cron_expression : varchar(120)
    without_overlapping : tinyint(1)
    created_at : timestamp
    updated_at : timestamp
  }
  RESOURCE_CAMPAIGN {
    resource_id : bigint(20) unsigned
    campaign_id : bigint(20) unsigned
  }
  RESOURCE_RECIPE {
    resource_id : bigint(20) unsigned
    ingredient_resource_id : bigint(20) unsigned
    quantity : varchar(255)
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
  RESOURCE_SPECIALIZATION {
    specialization_id : bigint(20) unsigned
    resource_id : bigint(20) unsigned
    level : smallint(5) unsigned
    quantity : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  RESOURCE_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    dofusdb_type_id : int(10) unsigned
    decision : varchar(255)
    seen_count : int(10) unsigned
    last_seen_at : timestamp
    state : varchar(255)
    read_level : tinyint(4)
    write_level : tinyint(4)
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
    description : text
    effect : varchar(255)
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
  SCRAPPING_ENTITY_MAPPING_CHARACTERISTIC {
    id : bigint(20) unsigned
    scrapping_entity_mapping_id : bigint(20) unsigned
    characteristic_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SCRAPPING_ENTITY_MAPPING_TARGETS {
    id : bigint(20) unsigned
    scrapping_entity_mapping_id : bigint(20) unsigned
    target_model : varchar(64)
    target_field : varchar(64)
    sort_order : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SCRAPPING_ENTITY_MAPPINGS {
    id : bigint(20) unsigned
    source : varchar(64)
    entity : varchar(64)
    mapping_key : varchar(128)
    from_path : varchar(256)
    from_lang_aware : tinyint(1)
    characteristic_id : bigint(20) unsigned
    formatters : longtext
    spell_level_aggregation : varchar(16)
    sort_order : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SCRAPPING_JOBS {
    id : char(36)
    kind : varchar(64)
    status : varchar(32)
    run_id : varchar(64)
    requested_by : bigint(20) unsigned
    payload : longtext
    summary : longtext
    results : longtext
    progress_done : int(10) unsigned
    progress_total : int(10) unsigned
    error : text
    started_at : timestamp
    finished_at : timestamp
    cancelled_at : timestamp
    created_at : timestamp
    updated_at : timestamp
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
  SECTION_BREED {
    breed_id : bigint(20) unsigned
    section_id : bigint(20) unsigned
    level : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SECTION_SPECIALIZATION {
    specialization_id : bigint(20) unsigned
    section_id : bigint(20) unsigned
    level : smallint(5) unsigned
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
    params : longtext
    read_level : tinyint(4)
    write_level : tinyint(4)
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
  SPECIALIZATION_SPELL {
    specialization_id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
    level : smallint(5) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SPECIALIZATIONS {
    id : bigint(20) unsigned
    name : varchar(255)
    short_description : text
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
  SPELL_EFFECT_TYPES {
    id : bigint(20) unsigned
    name : varchar(255)
    slug : varchar(64)
    category : varchar(32)
    description : text
    value_type : varchar(16)
    element : varchar(16)
    unit : varchar(32)
    is_positive : tinyint(1)
    sort_order : smallint(5) unsigned
    dofusdb_effect_id : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  SPELL_EFFECTS {
    id : bigint(20) unsigned
    spell_id : bigint(20) unsigned
    spell_effect_type_id : bigint(20) unsigned
    value_min : int(11)
    value_max : int(11)
    dice_num : tinyint(3) unsigned
    dice_side : tinyint(3) unsigned
    duration : smallint(5) unsigned
    target_scope : varchar(16)
    zone_shape : varchar(32)
    dispellable : tinyint(1)
    order : smallint(5) unsigned
    raw_description : text
    summon_monster_id : bigint(20) unsigned
    created_at : timestamp
    updated_at : timestamp
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
    level : varchar(255)
    po_min : varchar(64)
    po_max : varchar(64)
    po_editable : tinyint(1)
    pa : varchar(255)
    casting_time : varchar(255)
    ritual_available : tinyint(1)
    cast_per_turn : varchar(255)
    cast_per_target : varchar(255)
    sight_line : tinyint(1)
    cast_in_line : tinyint(1)
    cast_in_diagonal : tinyint(1)
    target_type : varchar(16)
    max_stack : tinyint(3) unsigned
    global_cooldown : tinyint(3) unsigned
    number_between_two_cast : varchar(255)
    duration : varchar(255)
    element : int(11)
    category : int(11)
    is_magic : tinyint(1)
    powerful : int(11)
    resolution_mode : varchar(32)
    attack_characteristic_key : varchar(64)
    save_characteristic_key : varchar(64)
    save_dc_formula : varchar(255)
    save_success_note : text
    auto_success_if_willing_target : tinyint(1)
    allows_reaction : tinyint(1)
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
  SUB_EFFECTS {
    id : bigint(20) unsigned
    slug : varchar(64)
    type_slug : varchar(64)
    template_text : text
    formula : text
    variables_allowed : longtext
    param_schema : longtext
    dofusdb_effect_id : int(10) unsigned
    created_at : timestamp
    updated_at : timestamp
  }
  TABLE_FILTER_PRESETS {
    id : bigint(20) unsigned
    user_id : bigint(20) unsigned
    entity_type : varchar(120)
    table_id : varchar(191)
    name : varchar(120)
    search_text : text
    filters : longtext
    limit : smallint(5) unsigned
    is_default : tinyint(1)
    created_at : timestamp
    updated_at : timestamp
  }
  USERS {
    id : bigint(20) unsigned
    name : varchar(255)
    email : varchar(255)
    email_verified_at : timestamp
    password : varchar(255)
    remember_token : varchar(100)
    last_login_at : timestamp
    role : int(11)
    is_system : tinyint(1)
    avatar : varchar(255)
    notifications_enabled : tinyint(1)
    notification_channels : longtext
    notification_preferences : longtext
    deleted_at : timestamp
    created_at : timestamp
    updated_at : timestamp
  }
  ADMIN_ACTIVITY_LOGS }o--|| USERS : "FK actor_id"
  BREED_CAPABILITY }o--|| BREEDS : "FK breed_id"
  BREED_CAPABILITY }o--|| CAPABILITIES : "FK capability_id"
  BREED_CREATURE_TRAIT }o--|| BREEDS : "FK breed_id"
  BREED_CREATURE_TRAIT }o--|| CREATURE_TRAITS : "FK creature_trait_id"
  BREED_ELEMENT_ORIENTATIONS }o--|| BREEDS : "FK breed_id"
  BREED_LANGUAGE }o--|| BREEDS : "FK breed_id"
  BREED_LANGUAGE }o--|| LANGUAGES : "FK language_id"
  BREED_SPELL }o--|| BREEDS : "FK breed_id"
  BREED_SPELL }o--|| SPELLS : "FK spell_id"
  BREEDS }o--|| USERS : "FK created_by"
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
  CHARACTERISTIC_CREATURE }o--|| CHARACTERISTICS : "FK characteristic_id"
  CHARACTERISTIC_CREATURE }o--|| SECTIONS : "FK norms_help_section_id"
  CHARACTERISTIC_OBJECT }o--|| CHARACTERISTICS : "FK characteristic_id"
  CHARACTERISTIC_OBJECT }o--|| SECTIONS : "FK norms_help_section_id"
  CHARACTERISTIC_OBJECT_ITEM_TYPE }o--|| CHARACTERISTIC_OBJECT : "FK characteristic_object_id"
  CHARACTERISTIC_OBJECT_ITEM_TYPE }o--|| ITEM_TYPES : "FK item_type_id"
  CHARACTERISTIC_SPELL }o--|| CHARACTERISTICS : "FK characteristic_id"
  CHARACTERISTIC_SPELL }o--|| SECTIONS : "FK norms_help_section_id"
  CHARACTERISTICS }o--|| CHARACTERISTICS : "FK linked_to_characteristic_id"
  CONDITION_CAPABILITY }o--|| CAPABILITIES : "FK capability_id"
  CONDITION_CAPABILITY }o--|| CONDITIONS : "FK condition_id"
  CONDITION_CREATURE }o--|| CONDITIONS : "FK condition_id"
  CONDITION_CREATURE }o--|| CREATURES : "FK creature_id"
  CONDITION_SPELL }o--|| CONDITIONS : "FK condition_id"
  CONDITION_SPELL }o--|| SPELLS : "FK spell_id"
  CONDITIONS }o--|| USERS : "FK created_by"
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
  CONSUMABLE_SPECIALIZATION }o--|| CONSUMABLES : "FK consumable_id"
  CONSUMABLE_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
  CONSUMABLE_TYPES }o--|| USERS : "FK created_by"
  CONSUMABLES }o--|| CONSUMABLE_TYPES : "FK consumable_type_id"
  CONSUMABLES }o--|| USERS : "FK created_by"
  CREATURE_CREATURE_TRAIT }o--|| CREATURES : "FK creature_id"
  CREATURE_CREATURE_TRAIT }o--|| CREATURE_TRAITS : "FK creature_trait_id"
  CREATURE_ITEM }o--|| CREATURES : "FK creature_id"
  CREATURE_ITEM }o--|| ITEMS : "FK item_id"
  CREATURE_RESOURCE }o--|| CREATURES : "FK creature_id"
  CREATURE_RESOURCE }o--|| RESOURCES : "FK resource_id"
  CREATURE_SPELL }o--|| CREATURES : "FK creature_id"
  CREATURE_SPELL }o--|| SPELLS : "FK spell_id"
  CREATURE_TRAIT_SPECIALIZATION }o--|| CREATURE_TRAITS : "FK creature_trait_id"
  CREATURE_TRAIT_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
  CREATURE_TRAITS }o--|| USERS : "FK created_by"
  CREATURES }o--|| USERS : "FK created_by"
  DATA_SUBJECT_REQUESTS }o--|| USERS : "FK user_id"
  EFFECT_DEGREES }o--|| EFFECTS : "FK effect_id"
  EFFECT_SPELL }o--|| EFFECTS : "FK effect_id"
  EFFECT_SPELL }o--|| SPELLS : "FK spell_id"
  EFFECT_SUB_EFFECT }o--|| EFFECT_DEGREES : "FK effect_degree_id"
  EFFECT_SUB_EFFECT }o--|| SUB_EFFECTS : "FK sub_effect_id"
  EFFECT_USAGES }o--|| EFFECT_DEGREES : "FK effect_degree_id"
  FEEDBACK_MESSAGES }o--|| USERS : "FK author_id"
  FEEDBACK_MESSAGES }o--|| FEEDBACK_THREADS : "FK feedback_thread_id"
  FEEDBACK_THREADS }o--|| USERS : "FK user_id"
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
  ITEM_SPECIALIZATION }o--|| ITEMS : "FK item_id"
  ITEM_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
  ITEM_TYPES }o--|| USERS : "FK created_by"
  ITEMS }o--|| USERS : "FK created_by"
  ITEMS }o--|| ITEM_TYPES : "FK item_type_id"
  MONSTER_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  MONSTER_CAMPAIGN }o--|| MONSTERS : "FK monster_id"
  MONSTER_LANGUAGE }o--|| LANGUAGES : "FK language_id"
  MONSTER_LANGUAGE }o--|| MONSTERS : "FK monster_id"
  MONSTER_RACES }o--|| USERS : "FK created_by"
  MONSTER_RACES }o--|| MONSTER_RACES : "FK id_super_race"
  MONSTER_SCENARIO }o--|| MONSTERS : "FK monster_id"
  MONSTER_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  MONSTERS }o--|| CREATURES : "FK creature_id"
  MONSTERS }o--|| MONSTER_RACES : "FK monster_race_id"
  NOTIFICATION_DIGEST_QUEUE }o--|| USERS : "FK user_id"
  NPC_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  NPC_CAMPAIGN }o--|| NPCS : "FK npc_id"
  NPC_PANOPLY }o--|| NPCS : "FK npc_id"
  NPC_PANOPLY }o--|| PANOPLIES : "FK panoply_id"
  NPC_SCENARIO }o--|| NPCS : "FK npc_id"
  NPC_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  NPCS }o--|| BREEDS : "FK breed_id"
  NPCS }o--|| CREATURES : "FK creature_id"
  NPCS }o--|| SPECIALIZATIONS : "FK specialization_id"
  OAUTH_ACCOUNTS }o--|| USERS : "FK user_id"
  OBJECT_EFFECTS }o--|| CHARACTERISTICS : "FK characteristic_id"
  OBJECT_EFFECTS }o--|| MONSTERS : "FK monster_id"
  PAGE_USER }o--|| PAGES : "FK page_id"
  PAGE_USER }o--|| USERS : "FK user_id"
  PAGES }o--|| USERS : "FK created_by"
  PAGES }o--|| PAGES : "FK parent_id"
  PANOPLIES }o--|| USERS : "FK created_by"
  PANOPLY_SHOP }o--|| PANOPLIES : "FK panoply_id"
  PANOPLY_SHOP }o--|| SHOPS : "FK shop_id"
  PRIVACY_AUDIT_LOGS }o--|| USERS : "FK actor_id"
  PRIVACY_AUDIT_LOGS }o--|| USERS : "FK subject_user_id"
  PRIVACY_EXPORTS }o--|| DATA_SUBJECT_REQUESTS : "FK data_subject_request_id"
  PRIVACY_EXPORTS }o--|| USERS : "FK user_id"
  RESOURCE_CAMPAIGN }o--|| CAMPAIGNS : "FK campaign_id"
  RESOURCE_CAMPAIGN }o--|| RESOURCES : "FK resource_id"
  RESOURCE_RECIPE }o--|| RESOURCES : "FK ingredient_resource_id"
  RESOURCE_RECIPE }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SCENARIO }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SCENARIO }o--|| SCENARIOS : "FK scenario_id"
  RESOURCE_SHOP }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SHOP }o--|| SHOPS : "FK shop_id"
  RESOURCE_SPECIALIZATION }o--|| RESOURCES : "FK resource_id"
  RESOURCE_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
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
  SCRAPPING_ENTITY_MAPPING_CHARACTERISTIC }o--|| CHARACTERISTICS : "FK characteristic_id"
  SCRAPPING_ENTITY_MAPPING_CHARACTERISTIC }o--|| SCRAPPING_ENTITY_MAPPINGS : "FK scrapping_entity_mapping_id"
  SCRAPPING_ENTITY_MAPPING_TARGETS }o--|| SCRAPPING_ENTITY_MAPPINGS : "FK scrapping_entity_mapping_id"
  SCRAPPING_ENTITY_MAPPINGS }o--|| CHARACTERISTICS : "FK characteristic_id"
  SCRAPPING_JOBS }o--|| USERS : "FK requested_by"
  SECTION_BREED }o--|| BREEDS : "FK breed_id"
  SECTION_BREED }o--|| SECTIONS : "FK section_id"
  SECTION_SPECIALIZATION }o--|| SECTIONS : "FK section_id"
  SECTION_SPECIALIZATION }o--|| SPECIALIZATIONS : "FK specialization_id"
  SECTION_USER }o--|| SECTIONS : "FK section_id"
  SECTION_USER }o--|| USERS : "FK user_id"
  SECTIONS }o--|| USERS : "FK created_by"
  SECTIONS }o--|| PAGES : "FK page_id"
  SHOPS }o--|| USERS : "FK created_by"
  SHOPS }o--|| NPCS : "FK npc_id"
  SPECIALIZATION_SPELL }o--|| SPECIALIZATIONS : "FK specialization_id"
  SPECIALIZATION_SPELL }o--|| SPELLS : "FK spell_id"
  SPECIALIZATIONS }o--|| USERS : "FK created_by"
  SPELL_EFFECTS }o--|| SPELL_EFFECT_TYPES : "FK spell_effect_type_id"
  SPELL_EFFECTS }o--|| SPELLS : "FK spell_id"
  SPELL_EFFECTS }o--|| MONSTERS : "FK summon_monster_id"
  SPELL_INVOCATION }o--|| MONSTERS : "FK monster_id"
  SPELL_INVOCATION }o--|| SPELLS : "FK spell_id"
  SPELL_TYPE }o--|| SPELLS : "FK spell_id"
  SPELL_TYPE }o--|| SPELL_TYPES : "FK spell_type_id"
  SPELL_TYPES }o--|| USERS : "FK created_by"
  SPELLS }o--|| USERS : "FK created_by"
  TABLE_FILTER_PRESETS }o--|| USERS : "FK user_id"
```
