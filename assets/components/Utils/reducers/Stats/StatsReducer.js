function StatsReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case "set-data": {
      let { chartData, topPlantsChartData, barData } = state;
      let [mostFetched, mostPlantsInZoneWithNames, topPlants] = payload;

      mostFetched.data.meta.forEach((zone) => {
        chartData.labels.push(zone.name);
        chartData.datasets[0].data.push(zone.count);
      });

      topPlants.data.meta.forEach((zone) => {
        topPlantsChartData.labels.push(zone.name);
        topPlantsChartData.datasets[0].data.push(zone.count);
      });

      mostPlantsInZoneWithNames.data.meta.forEach((plant) => {
        barData.labels.push(plant.name);
        barData.datasets[0].data.push(plant.count);
      });

      return {
        mostFetched: mostFetched.data.meta,
        mostPlantsInZoneWithNames: mostPlantsInZoneWithNames.data,
        topPlants: topPlants.data,
        loaded: true,
        chartData: chartData,
        barData: barData,
        topPlantsChartData: topPlantsChartData,
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
