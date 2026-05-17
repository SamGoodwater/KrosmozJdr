import { Scenario } from "@/Models/Entity/Scenario";
import { createEntityAdapter } from "@/Utils/Entity/createEntityAdapter";

export const adaptScenarioEntitiesTableResponse = createEntityAdapter(Scenario);

export default adaptScenarioEntitiesTableResponse;
