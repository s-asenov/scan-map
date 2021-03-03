import axios from "axios";
import React, { useEffect, useState, useReducer } from "react";
import { Spinner } from "react-bootstrap";
import { Bar, Pie } from "react-chartjs-2";
import { deepCloneObject } from "../../helpers/helper";
import StatsReducer from "../Utils/reducers/Stats/StatsReducer";
import "./Statistics.css";

const initChartData = (label) => {
  return {
    labels: [],
    datasets: [
      {
        label: label,
        data: [],
        backgroundColor: [
          "rgba(157, 11, 4, 0.6)",
          "rgba(184, 91, 251, 0.6)",
          "rgba(11, 14, 60, 0.6)",
          "rgba(106, 82, 127, 0.6)",
          "rgba(103, 185, 90, 0.6)",
          "rgba(19, 36, 212, 0.6)",
          "rgba(94, 73, 238, 0.6)",
          "rgba(73, 47, 126, 0.6)",
          "rgba(251, 145, 51, 0.6)",
          "rgba(181, 231, 83, 0.6)",
        ],
        borderColor: [
          "rgba(157, 11, 4, 0.6)",
          "rgba(184, 91, 251, 0.6)",
          "rgba(11, 14, 60, 0.6)",
          "rgba(106, 82, 127, 0.6)",
          "rgba(103, 185, 90, 0.6)",
          "rgba(19, 36, 212, 0.6)",
          "rgba(94, 73, 238, 0.6)",
          "rgba(73, 47, 126, 0.6)",
          "rgba(251, 145, 51, 0.6)",
          "rgba(181, 231, 83, 0.6)",
        ],
        borderWidth: 1,
      },
    ],
  };
};

const initialState = {
  mostFetched: [],
  mostPlantsInZoneWithNames: [],
  topPlants: [],
  loaded: false,
  barData: deepCloneObject(initChartData("")),
  chartData: deepCloneObject(initChartData("")),
  topPlantsChartData: deepCloneObject(initChartData("")),
};

function Statistics() {
  const [state, dispatch] = useReducer(StatsReducer, initialState);

  const {
    mostPlantsInZoneWithNames,
    loaded,
    barData,
    chartData,
    topPlantsChartData,
  } = state;

  useEffect(() => {
    getRequests();
  }, []);

  const getRequests = async () => {
    const data = await Promise.all([
      axios.get(process.env.BASE_URL + "statistics/zones/most-fetched"),
      axios.get(process.env.BASE_URL + "statistics/plants/most-seen-names"),
      axios.get(process.env.BASE_URL + "statistics/zones/plants-top"),
    ]);

    dispatch({
      type: "set-data",
      payload: data,
    });
  };

  if (!loaded) {
    return (
      <div className="flex-1 d-flex justify-content-center align-items-center">
        <Spinner
          animation="border"
          style={{
            height: "4rem",
            width: "4rem",
            borderWidth: "0.5em",
            color: "var(--indigo)",
          }}
        />
      </div>
    );
  }

  return (
    <div className="flex-1 d-flex flex-wrap py-5 justify-content-center">
      <div className="chart-wrapper">
        <Pie
          data={chartData}
          width={600}
          height={600}
          options={{
            maintainAspectRatio: false,
            responsive: false,
            title: {
              display: true,
              text: "Най-посещавани региони",
              fontSize: 18,
              fontColor: "#000000",
            },
          }}
        />
      </div>
      <div className="chart-wrapper">
        <Pie
          data={topPlantsChartData}
          width={600}
          height={600}
          options={{
            maintainAspectRatio: false,
            responsive: false,
            title: {
              display: true,
              text: "Региони с най-много растения",
              fontSize: 18,
              fontColor: "#000000",
            },
          }}
        />
      </div>
      <div className="chart-wrapper">
        <Bar
          data={barData}
          width={600}
          height={600}
          options={{
            maintainAspectRatio: false,
            responsive: false,
            legend: {
              display: false,
            },
            title: {
              display: true,
              text: "Най-често срещани растения",
              fontSize: 18,
              fontColor: "#000000",
            },
            tooltips: {
              callbacks: {
                title: function (t) {
                  return `${t[0].label} (${
                    mostPlantsInZoneWithNames.meta[t[0].index].commonName || ""
                  }) се среща в ${t[0].value} региона:`;
                },
                label: function (t) {
                  const zones = mostPlantsInZoneWithNames.meta[t.index].zones;
                  const array = zones.split(",");

                  return array;
                },
              },
            },
            onClick: function (e, items) {
              if (items.length == 0) return; //Clicked outside any bar.
              let plantName =
                mostPlantsInZoneWithNames.meta[items[0]._index].name;
              window.open(
                `https://en.wikipedia.org/wiki/${encodeURIComponent(
                  plantName
                )}`,
                "_blank"
              );
            },
            hover: {
              onHover: function (e) {
                var point = this.getElementAtEvent(e);
                e.target.style.cursor = point.length ? "pointer" : "default";
              },
            },
          }}
        />
      </div>
    </div>
  );
}

export default Statistics;
