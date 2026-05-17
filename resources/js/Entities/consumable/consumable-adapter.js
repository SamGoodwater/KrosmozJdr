import { Consumable } from "@/Models/Entity/Consumable";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptConsumableEntitiesTableResponse = createEntityAdapter(Consumable);

export default adaptConsumableEntitiesTableResponse;
