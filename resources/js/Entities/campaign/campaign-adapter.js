import { Campaign } from "@/Models/Entity/Campaign";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptCampaignEntitiesTableResponse = createEntityAdapter(Campaign);

export default adaptCampaignEntitiesTableResponse;
