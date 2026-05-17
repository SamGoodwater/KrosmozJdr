import { Panoply } from "@/Models/Entity/Panoply";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptPanoplyEntitiesTableResponse = createEntityAdapter(Panoply);

export default adaptPanoplyEntitiesTableResponse;
