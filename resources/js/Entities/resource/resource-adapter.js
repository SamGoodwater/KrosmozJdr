import { Resource } from "@/Models/Entity/Resource";
import { ResourceMapper } from "@/Mappers/Entity/ResourceMapper";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptResourceEntitiesTableResponse = createEntityAdapter(Resource, ResourceMapper);

export default adaptResourceEntitiesTableResponse;
