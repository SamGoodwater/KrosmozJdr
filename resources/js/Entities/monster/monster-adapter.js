import { Monster } from "@/Models/Entity/Monster";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptMonsterEntitiesTableResponse = createEntityAdapter(Monster);

export default adaptMonsterEntitiesTableResponse;
