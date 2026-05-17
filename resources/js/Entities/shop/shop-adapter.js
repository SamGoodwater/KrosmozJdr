import { Shop } from "@/Models/Entity/Shop";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptShopEntitiesTableResponse = createEntityAdapter(Shop);

export default adaptShopEntitiesTableResponse;
