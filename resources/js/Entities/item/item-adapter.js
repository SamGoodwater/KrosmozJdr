import { Item } from "@/Models/Entity/Item";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptItemEntitiesTableResponse = createEntityAdapter(Item);

export default adaptItemEntitiesTableResponse;
