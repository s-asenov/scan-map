function deepCloneObject(obj) {
  return JSON.parse(JSON.stringify(obj));
}

export { deepCloneObject };
