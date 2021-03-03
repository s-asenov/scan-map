function changeShownList(state, id) {
  const newState = state;

  const shownIndex = newState.shownData.findIndex((item) => item.id === id);
  const dataIndex = newState.data.findIndex((item) => item.id === id);

  if (shownIndex === -1 || dataIndex === -1) return;
  newState.data.splice(dataIndex, 1);

  if (newState.data.length % newState.itemsPerPage === 0 && newState.page > 1) {
    newState.page--;
  }

  let shownFirstIndex = shownDataStartingIndex(
    newState.page,
    newState.itemsPerPage
  );

  newState.shownData = newState.data.slice(
    shownFirstIndex,
    shownFirstIndex + newState.itemsPerPage
  );

  return newState;
}

function shownDataStartingIndex(page, itemsPerPage) {
  return (page - 1) * itemsPerPage;
}

export { changeShownList, shownDataStartingIndex };
