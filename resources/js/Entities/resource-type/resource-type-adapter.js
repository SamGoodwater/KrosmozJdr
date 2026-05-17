import { ResourceType } from "@/Models/Entity/ResourceType";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptResourceTypeEntitiesTableResponse = createEntityAdapter(ResourceType);

export default adaptResourceTypeEntitiesTableResponse;
