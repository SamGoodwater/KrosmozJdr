import { Capability } from "@/Models/Entity/Capability";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptCapabilityEntitiesTableResponse = createEntityAdapter(Capability);

export default adaptCapabilityEntitiesTableResponse;
