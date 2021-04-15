import { deepCloneObject } from "app/assets/helpers/helper";
import { getRandomColor, hexToRGB } from "../../../../helpers/helper";

function StatsReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case "set-data": {
      let { chartData, topPlantsChartData, barData } = state;
      let [mostFetched, mostPlantsInZoneWithNames, topPlants] = payload;

      let newChartData = deepCloneObject(chartData);

      mostFetched.data.meta.forEach((zone) => {
        let color = getRandomColor();
        let lighterColor = hexToRGB(color, 0.5);

        newChartData.labels.push(zone.name);
        newChartData.datasets[0].data.push(zone.count);
        newChartData.datasets[0].backgroundColor.push(lighterColor);
        newChartData.datasets[0].borderColor.push(color);
      });

      let newTopPlantsChartData = deepCloneObject(topPlantsChartData);

      topPlants.data.meta.forEach((zone) => {
        let color = getRandomColor();
        let lighterColor = hexToRGB(color, 0.3);

        newTopPlantsChartData.labels.push(zone.name);
        newTopPlantsChartData.datasets[0].data.push(zone.count);
        newTopPlantsChartData.datasets[0].backgroundColor.push(lighterColor);
        newTopPlantsChartData.datasets[0].borderColor.push(color);
      });

      let newBarData = deepCloneObject(barData);

      mostPlantsInZoneWithNames.data.meta.forEach((plant) => {
        let color = getRandomColor();
        let lighterColor = hexToRGB(color, 0.3);

        newBarData.labels.push(plant.name);
        newBarData.datasets[0].data.push(plant.count);
        newBarData.datasets[0].backgroundColor.push(lighterColor);
        newBarData.datasets[0].borderColor.push(color);
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
