import {deepCloneObject} from "app/assets/helpers/helper";

function StatsReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case "set-data": {
      let { chartData, topPlantsChartData, barData } = state;
      let [mostFetched, mostPlantsInZoneWithNames, topPlants] = payload;

      let newChartData= deepCloneObject(chartData);

      mostFetched.data.meta.forEach((zone) => {
        newChartData.labels.push(zone.name);
        newChartData.datasets[0].data.push(zone.count);
      });

      let newTopPlantsChartData = deepCloneObject(topPlantsChartData);

      topPlants.data.meta.forEach((zone) => {
        newTopPlantsChartData.labels.push(zone.name);
        newTopPlantsChartData.datasets[0].data.push(zone.count);
      });

      let newBarData = deepCloneObject(barData);

      mostPlantsInZoneWithNames.data.meta.forEach((plant) => {
        newBarData.labels.push(plant.name);
        newBarData.datasets[0].data.push(plant.count);
      });

      return {
        mostFetched: mostFetched.data.meta,
        mostPlantsInZoneWithNames: mostPlantsInZoneWithNames.data,
        topPlants: topPlants.data,
        loaded: true,
        chartData: newChartData,
        barData: newBarData,
        topPlantsChartData: newTopPlantsChartData,
      };
    }
    case "reset":
      return {
        ...payload,
      };
    default:
      throw new Error();
  }
}

export default StatsReducer;
