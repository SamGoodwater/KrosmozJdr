import { Spell } from "@/Models/Entity/Spell";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptSpellEntitiesTableResponse = createEntityAdapter(Spell);

export default adaptSpellEntitiesTableResponse;
