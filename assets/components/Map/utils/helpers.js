function componentToHex(c) {
  let hex = c.toString(16);
  return hex.length === 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

/**
 * Fixes float value to 2 digits after the coma
 *
 * @param {number} x
 *
 * @returns {number}
 */
function fix(x) {
  return parseFloat(Number.parseFloat(x).toFixed(2));
}

/**
 * Convert a number to string with the expected digits.
 *
 * @param {number} n the number
 * @param {number} expected the ammount of digits expected - ex. 002=>3, 02=>1 so on.
 *
 * @returns {string}
 */
function addN(n, expected) {
  let string = n.toString();

  while (expected > string.length) {
    string = "0" + string;
  }

  return string;
}

/**
 * Every SRTMGL3 filename has its unique name containing the coordinates it represents,
 */
function getFilename(location) {
  const { lat, lng } = location;
  let filename = "";

  if (lat > 0) {
    filename += "N" + addN(Math.floor(lat), 2);
  } else {
    filename += "S" + addN(Math.abs(Math.floor(lat)), 2);
  }

  if (lng > 0) {
    filename += "E" + addN(Math.floor(lng), 3);
  } else {
    filename += "W" + addN(Math.abs(Math.floor(lng)), 3);
  }

  return filename + ".jpg";
}

/**
 * Gets the count and positioning of the SRTMGL3 images.
 *
 * @param {Array} images
 *
 * @returns {Array} of the unique images.
 */
function getUniqImagesPos(images) {
  let unique = [...new Set(images)];
  let length = unique.length;

  if (length === 4) {
    return {
      images: unique,
      count: length,
    };
  } else if (length === 2) {
    let split = [];
    unique.forEach((e) => {
      split.push(e.split(""));
    });
    if (
      split[0][0] === split[1][0] &&
      split[0][1] + split[0][2] == split[1][1] + split[1][2]
    ) {
      return {
        images: unique,
        count: length,
        direction: "horizontal",
      };
    } else {
      return {
        images: unique,
        count: length,
        direction: "vertical",
      };
    }
  } else {
    return {
      images: unique,
      count: length,
    };
  }
}

export { rgbToHex, getFilename, fix, getUniqImagesPos };
