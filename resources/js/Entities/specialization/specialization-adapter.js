import { Specialization } from "@/Models/Entity/Specialization";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptSpecializationEntitiesTableResponse = createEntityAdapter(Specialization);

export default adaptSpecializationEntitiesTableResponse;
