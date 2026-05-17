import { Npc } from "@/Models/Entity/Npc";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptNpcEntitiesTableResponse = createEntityAdapter(Npc);

export default adaptNpcEntitiesTableResponse;
